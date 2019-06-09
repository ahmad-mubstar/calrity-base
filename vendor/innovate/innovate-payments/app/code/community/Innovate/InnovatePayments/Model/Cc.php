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

class Innovate_InnovatePayments_Model_Cc extends Mage_Payment_Model_Method_Abstract

{
	protected $_code = 'innovatepayments_cc';

	protected $_isGateway			= true;
	protected $_canAuthorize		= true;
	protected $_canCapture			= true;
	protected $_canCapturePartial		= false;
	protected $_canRefund			= true;
	protected $_canRefundInvoicePartial	= true;
	protected $_canVoid			= false;
	protected $_canUseInternal		= false;
	protected $_canUseCheckout		= true;
	protected $_canUseForMultishipping	= false;
	protected $_canSaveCc			= false;

	protected $_paymentMethod		= 'cc';
	protected $_defaultLocale		= 'en';


	protected $_testAdminUrl	= 'https://secure.innovatepayments.com/gateway/remote.html';
	protected $_liveAdminUrl	= 'https://secure.innovatepayments.com/gateway/remote.html';

	protected $_formBlockType = 'innovatepayments/form';
	protected $_infoBlockType = 'innovatepayments/info';

	protected $_order;

	public function getOrder()
	{
		if (!$this->_order) {
			$this->_order = $this->getInfoInstance()->getOrder();
		}
		return $this->_order;
	}

	public function getOrderPlaceRedirectUrl()
	{
		return Mage::getUrl('innovatepayments/processing/redirect');
	}

	public function getPaymentMethodType()
	{
		return $this->_paymentMethod;
	}

	public function getUrl()
	{
		return 'https://secure.innovatepayments.com/gateway/index.html';
	}

	public function getAdminUrl()
	{
		return 'https://secure.innovatepayments.com/gateway/remote.html';
	}

	private function _signData($post_data,$fieldList,$key) {
		$signatureParams = explode(',', $fieldList);
		$signatureString=$key;
		foreach ($signatureParams as $param) {
			if (array_key_exists($param, $post_data)) {
				$signatureString .= ':' . $post_data[$param];
			} else {
				$signatureString .= ':';
			}
		}
		return sha1($signatureString);
	}

	private function _buildUrl($part) {
		$url= Mage::getUrl($part,array('_nosid' => true));
		$url = trim(str_replace('&amp;', '&', $url));
		return $url;
	}

	private function _tidy($value,$remove_accents) { 
		if ($accents>0) {
			$value=Mage::helper('core')->removeAccents($value);
		}
		return trim($value);
	}

	public function getFormFields()
	{
		$securityKey = trim($this->getConfigData('security_key'));
		if (empty($securityKey)) {
			$message = 'Secret key not set';
			Mage::throwException($message);
		}
		$price		= number_format($this->getOrder()->getGrandTotal(),2,'.','');
		$currency	= $this->getOrder()->getOrderCurrencyCode();
		$billing	= $this->getOrder()->getBillingAddress();
		$shipping	= $this->getOrder()->getShippingAddress();

		$locale = trim(Mage::app()->getLocale()->getLocaleCode());
		if (empty($locale)) {
			$locale = trim($this->getDefaultLocale());
		}
		$order_id = $this->getOrder()->getRealOrderId();
		$tran_desc = trim($this->getConfigData('tran_desc'));
		if (empty($tran_desc)) {
			$tran_desc = 'Your purchase at ' . Mage::app()->getStore()->getName();
		}
		$tran_desc = str_replace('{order}', $order_id, $tran_desc);

		$params = 	array(
			'ivp_store'		=>	$this->_tidy($this->getConfigData('store_id'),0),
			'ivp_source'		=>	$this->_tidy('Magento',0),
			'ivp_cart'		=>	$this->_tidy($order_id,0),
			'ivp_test'		=>	($this->getConfigData('transaction_testmode') == '0') ? '0' : '1',
			'ivp_timestamp'		=>	'0',
			'ivp_amount'		=>	$this->_tidy($price,0),
			'ivp_currency'		=>	$this->_tidy($currency,0),
			'ivp_desc'		=>	$this->_tidy($tran_desc,0),
			'ivp_extra'		=>	'bill,delv,return',
			'ivp_lang'		=>	$this->_tidy($locale,0),
			'bill_title'		=>	'',
			'bill_fname'		=>	$this->_tidy($billing->getFirstname(),0),
			'bill_sname'		=>	$this->_tidy($billing->getLastname(),0),
			'bill_addr1'		=>	$this->_tidy($billing->getStreet(1),0),
			'bill_addr2'		=>	$this->_tidy($billing->getStreet(2),0),
			'bill_addr3'		=>	$this->_tidy($billing->getStreet(3),0),
			'bill_city'		=>	$this->_tidy($billing->getCity(),0),
			'bill_region'		=>	$this->_tidy($billing->getRegion(),0),
			'bill_zip'		=>	$this->_tidy($billing->getPostcode(),0),
			'bill_country'		=>	$this->_tidy($billing->getCountry(),0),
			'bill_email'		=>	$this->_tidy($this->getOrder()->getCustomerEmail(),0),
			'bill_phone1'		=>	$this->_tidy($billing->getTelephone(),0),
			'delv_title'		=>	'',
			'delv_fname'		=>	$this->_tidy($shipping->getFirstname(),0),
			'delv_sname'		=>	$this->_tidy($shipping->getLastname(),0),
			'delv_addr1'		=>	$this->_tidy($shipping->getStreet(1),0),
			'delv_addr2'		=>	$this->_tidy($shipping->getStreet(2),0),
			'delv_addr3'		=>	$this->_tidy($shipping->getStreet(3),0),
			'delv_city'		=>	$this->_tidy($shipping->getCity(),0),
			'delv_region'		=>	$this->_tidy($shipping->getRegion(),0),
			'delv_zip'		=>	$this->_tidy($shipping->getPostcode(),0),
			'delv_country'		=>	$this->_tidy($shipping->getCountry(),0),
			'delv_phone1'		=>	$this->_tidy($shipping->getTelephone(),0),
			'return_cb_auth'	=>	$this->_buildUrl('innovatepayments/processing/response'),
			'return_cb_decl'	=>	'none',
			'return_cb_can'		=>	$this->_buildUrl('innovatepayments/processing/response'),
			'return_auth'		=>	'auto:'.$this->_buildUrl('innovatepayments/processing/success'),
			'return_can'		=>	'auto:'.$this->_buildUrl('innovatepayments/processing/cancel'),
		);

		$params['ivp_signature']=$this->_signData($params,'ivp_store,ivp_source,ivp_amount,ivp_currency,ivp_test,ivp_timestamp,ivp_cart,ivp_desc,ivp_extra',$securityKey);
		$params['bill_signature']=$this->_signData($params,'bill_title,bill_fname,bill_sname,bill_addr1,bill_addr2,bill_addr3,bill_city,bill_region,bill_country,bill_zip,ivp_signature',$securityKey);
		$params['delv_signature']=$this->_signData($params,'delv_title,delv_fname,delv_sname,delv_addr1,delv_addr2,delv_addr3,delv_city,delv_region,delv_country,delv_zip,ivp_signature',$securityKey);
		$params['return_signature']=$this->_signData($params,'return_cb_auth,return_cb_decl,return_cb_can,return_auth,return_can,ivp_signature',$securityKey);

		return $params;
	}

	/*
	public function void(Varien_Object $payment, $amount)
	{
		$transactionId = $payment->getLastTransId();
		$params = $this->_prepareAdminRequestParams();

		$params['ivp_trantype']		= 'void';
		$params['ivp_amount']		= $amount;
		$params['ivp_currency']		= $payment->getOrder()->getOrderCurrencyCode();
		$params['tran_ref']		= $transactionId;

		$response = $this->processRemoteRequest($params);
		return $this;
	}
	*/

	public function refund(Varien_Object $payment, $amount)
	{
		$transactionId = $payment->getLastTransId();
		$params = $this->_prepareAdminRequestParams();

		$params['ivp_trantype']		= 'refund';
		$params['ivp_amount']		= $amount;
		$params['ivp_currency']		= $payment->getOrder()->getOrderCurrencyCode();
		$params['tran_ref']		= $transactionId;

		$response = $this->processRemoteRequest($params);
		return $this;
	}

	public function capture(Varien_Object $payment, $amount)
	{
		if (!$this->canCapture()) {
			return $this;
		}

		if (Mage::app()->getRequest()->getParam('auth_tranref')) {
			// Capture is being called from inside response action (autocapture=1)
			$payment->setStatus(self::STATUS_APPROVED);
			return $this;
		}
		$transactionId = $payment->getLastTransId();
		$params = $this->_prepareAdminRequestParams();
		$params['ivp_trantype']		= 'capture';
		$params['ivp_amount']		= $amount;
		$params['ivp_currency']		= $payment->getOrder()->getOrderCurrencyCode();
		$params['tran_ref']		= $transactionId;

		$response = $this->processRemoteRequest($params);
		$payment->getOrder()->addStatusToHistory($payment->getOrder()->getStatus(), $this->_getHelper()->__('InnovatePayments transaction has been captured.'));
		return $this;
	}

	public function canEdit () {
		return false;
	}

	public function canManageBillingAgreements () {
		return false;
	}

	public function canManageRecurringProfiles () {
		return false;
	}

	/*
	public function canVoid ()
	{
		return $this->_remoteEnabled();
	}
	*/

	public function canRefund ()
	{
		return $this->_remoteEnabled();
	}

	public function canRefundInvoicePartial()
	{
		return $this->_remoteEnabled();
	}

	public function canRefundPartialPerInvoice()
	{
		return $this->canRefundInvoicePartial();
	}

	public function canCapturePartial()
	{
		if (Mage::app()->getFrontController()->getAction()->getFullActionName() != 'adminhtml_sales_order_creditmemo_new'){
			return false;
		}
		return $this->_remoteEnabled();
	}

	protected function processRemoteRequest($params, $requestTimeout = 60)
	{
		try {
			$client = new Varien_Http_Client();
			$client->setUri($this->getAdminUrl())
				->setConfig(array('timeout'=>$requestTimeout,))
				->setParameterPost($params)
				->setMethod(Zend_Http_Client::POST);

			$response = $client->request();
			$responseBody = $response->getBody();

			if (empty($responseBody))
				Mage::throwException($this->_getHelper()->__('InnovatePayments API failure. The request has not been processed.'));

		} catch (Exception $e) {
			Mage::throwException($this->_getHelper()->__('InnovatePayments API connection error. The request has not been processed.'));
		}
		// create array out of response
		parse_str($responseBody,$response);
		if ($response['auth_status']=='A') {
			return $response;
		}
		if (empty($response['auth_message'])) {
			$err="Invalid response from server.";
		} else {
			$err=$response['auth_status'].$response['auth_code'].':'.$response['auth_message'];
		}
		Mage::throwException($err);
	}

	protected function _remoteEnabled() {
		// Remote connections are only used if automatic capture is not enabled.
		if ($this->getConfigData('autocapture') == '0') {
			return 1;
		}
		return 0;
	}

	protected function _prepareAdminRequestParams()
	{
		$securityKey = trim($this->getConfigData('security_key'));
		if (empty($securityKey)) {
			$message = 'Secret key not set';
			Mage::throwException('Authorisation password is not set');
		}
		$params = array (
			'ivp_password'	=> $securityKey,
			'ivp_store'	=> $this->getConfigData('store_id'),
			'ivp_test'	=>($this->getConfigData('transaction_testmode') == '0') ? '0' : '1',
			'ivp_tranclass' => 'ecom',
		);
		return $params;
	}
}
