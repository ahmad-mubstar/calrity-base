<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category	Innovate
 * @package	Innovate_InnovatePayments
 * @copyright	Copyright (c) 2011 Innovate Payments (http://www.innovatepayments.com/)
 */

class Innovate_InnovatePayments_ProcessingController extends Mage_Core_Controller_Front_Action
{
	protected $_successBlockType = 'innovatepayments/success';
	protected $_failureBlockType = 'innovatepayments/failure';
	protected $_cancelBlockType = 'innovatepayments/cancel';

	protected $_order = NULL;
	protected $_paymentInst = NULL;


	/**
	 * Get singleton of Checkout Session Model
	 *
	 * @return Mage_Checkout_Model_Session
	 */
	protected function _getCheckout()
	{
		return Mage::getSingleton('checkout/session');
	}

	/**
	 * when customer selects InnovatePayments payment method
	 */
	public function redirectAction()
	{
		try {
			$session = $this->_getCheckout();
			$order = Mage::getModel('sales/order');
			$order->loadByIncrementId($session->getLastRealOrderId());
			if (!$order->getId()) {
				Mage::throwException('No order for processing found');
			}
			if ($order->getState() != Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) {
				$order->setState(
					Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
					$this->_getPendingPaymentStatus(),
					Mage::helper('innovatepayments')->__('Customer was redirected to Innovate Payments.')
					)->save();
			}
			if ($session->getQuoteId() && $session->getLastSuccessQuoteId()) {
				$session->setInnovatePaymentsQuoteId($session->getQuoteId());
				$session->setInnovatePaymentsSuccessQuoteId($session->getLastSuccessQuoteId());
				$session->setInnovatePaymentsRealOrderId($session->getLastRealOrderId());
				$session->getQuote()->setIsActive(false)->save();
				$session->clear();
			}
			$this->loadLayout();
			$this->renderLayout();
			return;
		} catch (Mage_Core_Exception $e) {
			$this->_getCheckout()->addError($e->getMessage());
		} catch(Exception $e) {
			Mage::logException($e);
		}
		$this->_redirect('checkout/cart');
	}

	/**
	 * InnovatePayments returns POST variables to this action through the callback handler (not customer return)
	 */
	public function responseAction()
	{
		try {
			$request = $this->_checkCallbackData();
			if ($request['auth_status'] == 'A') {
				$this->_processSale($request);
			} elseif ($request['auth_status'] == 'H') {
				$this->_processSale($request);
			} elseif ($request['auth_status'] == 'C') {
				$this->_processCancel($request);
			} else {
				Mage::throwException('Transaction was not successfull.');
			}
		} catch (Mage_Core_Exception $e) {
			$this->getResponse()->setBody(
				$this->getLayout()
				->createBlock($this->_failureBlockType)
				->setOrder($this->_order)
				->toHtml()
			);
		}
	}

	/**
	 * InnovatePayments return action (after callback)
	 */
	public function successAction()
	{
		try {
			$session = $this->_getCheckout();
			$session->unsInnovatePaymentsRealOrderId();
			$session->setQuoteId($session->getInnovatePaymentsQuoteId(true));
			$session->setLastSuccessQuoteId($session->getInnovatePaymentsSuccessQuoteId(true));
			$this->_redirect('checkout/onepage/success');
			return;
		} catch (Mage_Core_Exception $e) {
			$this->_getCheckout()->addError($e->getMessage());
		} catch(Exception $e) {
			Mage::logException($e);
		}
		$this->_redirect('checkout/cart');
	}

	/**
	 * InnovatePayments return action
	 */
	public function cancelAction()
	{
		// set quote to active
		$session = $this->_getCheckout();
		if ($quoteId = $session->getInnovatePaymentsQuoteId()) {
			$quote = Mage::getModel('sales/quote')->load($quoteId);
			if ($quote->getId()) {
				$quote->setIsActive(true)->save();
				$session->setQuoteId($quoteId);
			}
		}
		$session->addError(Mage::helper('innovatepayments')->__('The order has been canceled.'));
		$this->_redirect('checkout/cart');
	}


	/**
	 * Checking POST variables.
	 * Creating invoice if payment was successfull or cancel order if payment was declined
	 */
	protected function _checkCallbackData()
	{
		// Check request type
		if (!$this->getRequest()->isPost()) {
			Mage::throwException('Wrong request type.');
		}
		// Validate request ip is Innovate Payments
		$helper = Mage::helper('core/http');
		if (method_exists($helper, 'getRemoteAddr')) {
			$remoteAddr = $helper->getRemoteAddr();
		} else {
			$request = $this->getRequest()->getServer();
			$remoteAddr = $request['REMOTE_ADDR'];
		}
		$connection_allowed=false;
		$ip_conf=Mage::getStoreConfig('payment/innovatepayments_cc/server_ip');
		if (empty($ip_conf)) {
			$ip_conf="91.75.72.164, 91.75.72.165, 91.75.72.166";
		}
		$ip_list=explode(',',$ip_conf);
		foreach ($ip_list as $ip_check) {
			if (strcmp(trim($ip_check),$remoteAddr)==0) {
				$connection_allowed=true;
			}
		}
		if (!$connection_allowed) {
			Mage::throwException('IP '.$remoteAddr.' can\'t be validated as Innovate Payments.');
		}
		// Get request variables
		$request = $this->getRequest()->getPost();
		if (empty($request)) {
			Mage::throwException('Request doesn\'t contain POST elements.');
		}
		// Validate data hash check
		$fieldList='auth_status,auth_code,auth_message,auth_tranref,auth_cvv,auth_avs,card_code,card_desc,cart_id,cart_desc,cart_currency,cart_amount,tran_currency,tran_amount,tran_cust_ip';
		$signatureParams = explode(',', $fieldList);
		$signatureString=Mage::getStoreConfig('payment/innovatepayments_cc/security_key');
		foreach ($signatureParams as $param) {
			if (array_key_exists($param, $request)) {
				$signatureString .= ':' . $request[$param];
			} else {
				$signatureString .= ':';
			}
		}
		$hash_check=sha1($signatureString);
		if (strcasecmp($request['auth_hash'],$hash_check)!=0) {
			// Hash check does not match. Data may of been tampered with.
			Mage::throwException('Callback data security check failed');
		}
		// Check order id
		if (empty($request['cart_id']) || strlen($request['cart_id']) > 50) {
			Mage::throwException('Missing or invalid order ID');
		}

		// Load order for further validation
		$this->_order = Mage::getModel('sales/order')->loadByIncrementId($request['cart_id']);
		if (!$this->_order->getId()) {
			Mage::throwException('Order not found');
		}
		$this->_paymentInst = $this->_order->getPayment()->getMethodInstance();
		return $request;
	}

	/**
	 * Process success response
	 */
	protected function _processSale($request)
	{
		// save transaction information
		$this->_order->getPayment()
			->setTransactionId($request['auth_tranref'])
			->setLastTransId($request['auth_tranref'])
			->setCcAvsStatus($request['auth_avs']);

		$additional_data = $this->_order->getPayment()->getAdditionalData();
		$additional_data .= ($additional_data ? "<br/>\n" : '') . $request['card_desc'];
		$this->_order->getPayment()->setAdditionalData($additional_data);
		if (Mage::getStoreConfigFlag('payment/innovatepayments_cc/autocapture')) {
			if ($this->_order->canInvoice()) {
				$invoice = $this->_order->prepareInvoice();
				$invoice->register()->capture();
				Mage::getModel('core/resource_transaction')
					->addObject($invoice)
					->addObject($invoice->getOrder())
					->save();
			}
			$this->_order->addStatusToHistory($this->_paymentInst->getConfigData('order_status'), Mage::helper('innovatepayments')->__('The transaction has been authorised and captured by Innovate Payments.'));
		} else {
			$this->_order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, $this->_paymentInst->getConfigData('order_status'), Mage::helper('innovatepayments')->__('The amount has been authorised by Innovate Payments. Capture is required to complete the transaction.'));
		}

		$this->_order->sendNewOrderEmail();
		$this->_order->setEmailSent(true);

		$this->_order->save();

		$this->getResponse()->setBody(
			$this->getLayout()
			->createBlock($this->_successBlockType)
			->setOrder($this->_order)
			->toHtml()
		);
	}

	/**
	 * Process success response
	 */
	protected function _processCancel($request)
	{
		// cancel order
		if ($this->_order->canCancel()) {
			$this->_order->cancel();
			$this->_order->addStatusToHistory(Mage_Sales_Model_Order::STATE_CANCELED, Mage::helper('innovatepayments')->__('Payment was canceled'));
			$this->_order->save();
		}

		$this->getResponse()->setBody(
			$this->getLayout()
			->createBlock($this->_cancelBlockType)
			->setOrder($this->_order)
			->toHtml()
		);
	}

	protected function _getPendingPaymentStatus()
	{
		return Mage::helper('innovatepayments')->getPendingPaymentStatus();
	}
}
