<?php
/**
 * @category   Knet
 * @package    Knet_PG
 * @author     ali@shopgo.me
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Knet_PG_ProcessController extends Mage_Core_Controller_Front_Action
{
    protected $_order;

    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

   	protected function _expireAjax()
    {
        if (!$this->_getCheckout()->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    }

    public function getPG()
    {
        return Mage::getModel('pg/pg');
    }

    public function getOrder()
    {
        if ($this->_order == null) {
            $session = Mage::getSingleton('checkout/session');
            $this->_order = Mage::getModel('sales/order');
            $this->_order->loadByIncrementId($session->getLastRealOrderId());
        }
        return $this->_order;
    }

	public function redirectAction()
	{
		$PG = $this->getPG();
		$helper	= Mage::helper('pg');

		$session 	= $this->_getCheckout();
		$order 		= $this->getOrder();
		if (!$order->getId()) {
			$this->norouteAction();
			return;
		}

		$order->addStatusToHistory(
			$order->getStatus(),
			$this->__('Customer was redirected to Knet.')
		);
		$order->save();

		/*
		
		For testing

		8888880000000001 (any Pin /Expiry) Captured
		8888880000000002 (any Pin /Expiry) Not Captured

		*/

		// Handling the currency
		$baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
		$currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();

		$order_amount = $order->getBaseGrandTotal();

		try {
			if($baseCurrencyCode != 'KWD')
			$order_amount = Mage::helper('directory')->currencyConvert($order_amount, $baseCurrencyCode, 'KWD');
		} catch(Mage_Exception $e) {
			$this->_getCheckout()->addError($this->__('There is a problem with your store currency setup: ' . $e->getMessage()));
			$this->_redirect('checkout/cart');
    	    return;
		}
		

		require_once Mage::getBaseDir('lib') . DS . 'Knet' . DS . 'PG' . DS . 'e24PaymentPipe.inc.php' ;
		$pipe = new e24PaymentPipe;

   		$pipe->setAction(1);
   		$pipe->setCurrency(414); // KWD
   		$pipe->setLanguage("ENG"); //change it to "ARA" for arabic language
   		$pipe->setResponseURL($PG->getResponseUrl()); //set your respone page URL
   		$pipe->setErrorURL($PG->getIpnUrl()); //set your error page URL
   		$pipe->setAmt($order_amount); //set the amount for the transaction
   		$pipe->setResourcePath(Mage::getBaseDir('var') . DS . 'knet' . DS . 'pg' . DS . 'resource' . DS); //change the path where your resource file is
   		$pipe->setAlias(Mage::getStoreConfig('payment/pg/alias')); //set your alias name here
   		$pipe->setTrackId($order->getIncrementId());//generate the random number here
 
   		$pipe->setUdf1(""); //set User defined value
   		$pipe->setUdf2(""); //set User defined value
   		$pipe->setUdf3(""); //set User defined value
   		$pipe->setUdf4(""); //set User defined value
   		$pipe->setUdf5(""); //set User defined value

        //get results
		if ($pipe->performPaymentInitialization() != $pipe->SUCCESS) {
			$helper->log("Result=" . $pipe->SUCCESS);
			$helper->log($pipe->getErrorMsg());
			$helper->log($pipe->getDebugMsg());
		} else {
			$payID = $pipe->getPaymentId();
            $payURL = $pipe->getPaymentPage();
			$helper->log($pipe->getDebugMsg());
			$this->_redirectUrl($payURL . "?PaymentID=" . $payID);
			return;
		}
    }

    public function responseAction() {

    	$PG = $this->getPG();
    	$helper	= Mage::helper('pg');
	   	$request	= $this->getRequest();
	   	$params	= $request->getParams();
	   	$paymentid = $request->getParam('PaymentID');
	   	$result = $request->getParam('Result');
	   	$postdate = $request->getParam('PostDate');
	   	$tranid = $request->getParam('TranID');
	   	$auth = $request->getParam('Auth');
	   	$ref = $request->getParam('Ref');
	   	$trackid = $request->getParam('TrackID');
	   	$udf1 = $request->getParam('UDF1');
	   	$udf2 = $request->getParam('UDF2');
	   	$udf3 = $request->getParam('UDF3');
	   	$udf4 = $request->getParam('UDF4');
	   	$udf5 = $request->getParam('UDF5');

	   $helper->log('responseAction()::start');
    	if ( $result == "CAPTURED" )
		{
    		$result_url = $PG->getIpnUrl();
   			$result_params = "?paymentid=" . $paymentid . "&result=" . $result . "&postdate=" . $postdate . "&tranid=" . $tranid . "&auth=" . $auth . "&ref=" . $ref . "&trackid=" . $trackid . "&udf1=" . $udf1 . "&udf2=" .$udf2  . "&udf3=" . $udf3  . "&udf4=" . $udf4 . "&udf5=" . $udf5  ;
		}
		else
		{
    		$result_url = $PG->getIpnUrl();
    		$result_params = "?paymentid=" . $paymentid . "&result=" . $result . "&postdate=" . $postdate . "&tranid=" . $tranid . "&auth=" . $auth . "&ref=" . $ref . "&trackid=" . $trackid . "&udf1=" . $udf1 . "&udf2=" .$udf2  . "&udf3=" . $udf3  . "&udf4=" . $udf4 . "&udf5=" . $udf5  ;
    	}
		echo "REDIRECT=" . $result_url . $result_params;
    }

    public function ipnAction()
    {

	   $helper	= Mage::helper('pg');
	   $request	= $this->getRequest();
	   $params	= $request->getParams();
	   $paymentid = $request->getParam('PaymentID');
	   $result = $request->getParam('Result');
	   $postdate = $request->getParam('PostDate');
	   $tranid = $request->getParam('TranID');
	   $auth = $request->getParam('Auth');
	   $ref = $request->getParam('Ref');
	   $trackid = $request->getParam('TrackID');
	   $udf1 = $request->getParam('UDF1');
	   $udf2 = $request->getParam('UDF2');
	   $udf3 = $request->getParam('UDF3');
	   $udf4 = $request->getParam('UDF4');
	   $udf5 = $request->getParam('UDF5');

	   $helper->log('ipnAction()::start');

	   //signature check...
	   if($this->_validateResponse($params)) {
			$orderId = isset($params['trackid']) ? $params['trackid'] : null;
			$order	 = Mage::getModel('sales/order')->loadByIncrementId($orderId);
			if ($order && $order->canInvoice()) {
				$invoice = $order->prepareInvoice();
				$invoice->register()->capture();

				$invoice->setEmailSent(true);
 
            	$invoice->getOrder()->setIsInProcess(true);

				Mage::getModel('core/resource_transaction')
				   ->addObject($invoice)
				   ->addObject($invoice->getOrder())
				   ->save();

				$invoice->sendEmail(true, '');

				$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
				$order->getPayment()->setLastTransId($params['tranid']);
				$order->sendNewOrderEmail();
				$order->setEmailSent(true);

				$order->save();
				$helper->log('ipnAction()::invoice-created, main sent');

				$this->_redirect('checkout/onepage/success');
            	return;
			} else {
				$this->_getCheckout()->addError($this->__('There is an error processing your payment: cannot create an invoice.'));
				$this->_redirect('checkout/cart');
    	    	return;
			}
		} else {
			$this->_getCheckout()->addError($this->__('There is an error processing your payment.'));
			$this->_redirect('checkout/onepage/failure');
    	    return;
		}
    }

	protected function _validateResponse($params)
	{
		$helper = Mage::helper('pg');
		$helper->log('_validateResponse()::');
		$helper->log($params);

		$orderId = isset($params['trackid']) ? $params['trackid'] : null;
		$order	 = Mage::getModel('sales/order')->loadByIncrementId($orderId);
		if(!$order){
			return false;
		}
		$errors = array();
		if(isset($params['result']) && $params['result'] != 'CAPTURED'){
			$errors[] = 'result is not CAPTURED';
		}
		// There is no response/reason code for Knet PG.
		// if( isset($params['reason_code']) && !in_array($params['reason_code'], array(100, 110)) ){
		// 	$errors[] = 'reason_code is not 100, 110';
		// }

		// Transaction result
		// CAPTURED - Transaction was approved.
		// VOIDED - Transaction was voided.
		// NOT CAPTURED - Transaction was not approved.
		// CANCELED – Canceled Transaction.
		// DENIED BY RISK - Risk denied the transaction.
		// HOST TIMEOUT - The authorization system did not respond within the
		// timeout limit.

		// Add the comment and save the order
		
		$order->addStatusToHistory(
			$order->getStatus(),
			$this->__(
				"Transaction result: " . $params["result"]
				. "<br>" .
				"Transaction authorization number: " . $params["auth"]
				. "<br>" .
				"Transaction unique resulting reference number: " . $params["ref"]
				)
		);
		$order->save();

		// Todo
		// Singature

		if(count($errors) == 0){
			return true;
		}else{
			return false;
		}
	}

    public function successAction()
    {
		$helper		   = Mage::helper('pg');
		$order         = $this->getOrder();
		if ( !$order->getId() ) {
			$this->_redirect('checkout/cart');
			return false;
		}

		$helper->log('successAction()::');
		$responseParams     = $this->getRequest()->getParams();
        $validateResponse	= $this->_validateResponse($responseParams);
		if($validateResponse){

			$order->addStatusToHistory(
				$order->getStatus(),
				$this->__('Customer successfully returned from Knet and the payment is APPROVED.')
			);
			#$order->sendNewOrderEmail(); //already sent above
			$order->save();

            $this->_redirect('checkout/onepage/success');
            return;
		} else {
			$comment = '';
			if(isset($responseParams['message'])){
				$comment .= '<br />Error: ';
				$comment .= "'" . $responseParams['message'] . "'";
			}
			$order->cancel();
            $order->addStatusToHistory(
				$order->getStatus(),
				$this->__('Customer successfully returned from Knet but the payment is DECLINED.') . $comment
			);
			$order->save();

			$this->_getCheckout()->addError($this->__('There is an error processing your payment.' . $comment));
			$this->_redirect('checkout/cart');
    	    return;
		}
    }

    public function cancelAction()
	{
		$order         = $this->getOrder();
		if ( !$order->getId() ) {
			$this->_redirect('checkout/cart');
			return false;
		}

        $order->cancel();
        $order->addStatusToHistory(
			$order->getStatus(),
			$this->__('Payment was canceled.')
		);
        $order->save();

		$this->_getCheckout()->addError($this->__('Payment was canceled.'));
		$this->_redirect('checkout/cart');
	}

    public function failureAction()
    {
		$order         = $this->getOrder();
		if ( !$order->getId() ) {
			$this->_redirect('checkout/cart');
			return false;
		}

        $order->cancel();
        $order->addStatusToHistory(
			$order->getStatus(),
			$this->__('Payment failed.')
		);
        $order->save();

		$this->_getCheckout()->addError($this->__('Payment failed.'));
		$this->_redirect('checkout/cart');
    }
}