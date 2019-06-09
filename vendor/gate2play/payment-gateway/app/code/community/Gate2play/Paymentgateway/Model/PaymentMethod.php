<?php

/**
 * Gate2play Payment payment method model.
 *
 * @category   Gate2play
 * @package    Gate2play_Paymentgateway
 * @author     gate2play.com
 */
class Gate2play_Paymentgateway_Model_PaymentMethod extends Mage_Payment_Model_Method_Abstract {

    protected $_code = 'paymentgateway';
    protected $_formBlockType = 'paymentgateway/form_paymentgateway';
    //protected $_infoBlockType = 'paymentgateway/info_paymentgateway';

    protected $_isGateway = true;
    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_canCapturePartial = false;
    protected $_canRefund = false;
    protected $_canVoid = false;
    protected $_canUseInternal = false;
    protected $_canUseCheckout = true;
    protected $_canUseForMultishipping = false;
    //protected $_isInitializeNeeded = true;
    protected $_order;
    
    public function getOrderPlaceRedirectUrl() {
        return Mage::getUrl('gate2play/payment/');
    }
    
    public function getStatusUrl() {
        return Mage::getUrl('gate2play/payment/status');
    }    

    public function getBrands() {
        $brands = $this->getConfigData('brands');
        $brands = str_replace(',', ' ', $brands);

        return $brands;
    }

    public function getJavascript() {
        $testMode = $this->getConfigData('test');
        if ($testMode == 0) {
            $url = Mage::helper('gate2play')->getJsLiveUrl();
        } else {
            $url = Mage::helper('gate2play')->getJsTestUrl();
        }
        
        $style = $this->getConfigData('payment_style');
        $url .= $style;

        return $url;
    }

    public function generateToken() {       
        $order_id = $this->getOrder()->getRealOrderId();
        $billing = $this->getOrder()->getBillingAddress();
        if ($this->getOrder()->getBillingAddress()->getEmail()) {
            $email = $this->getOrder()->getBillingAddress()->getEmail();
        } else {
            $email = $this->getOrder()->getCustomerEmail();
        }       

        $amount = round($this->getOrder()->getGrandTotal(), 2);
        $currency_website = $this->getOrder()->getOrderCurrencyCode();    
        $currency = $this->getConfigData('currency');                
        
        if($currency_website != $currency) {           
            $total = $this->getOrder()->getBaseGrandTotal();
            $amount = Mage::app()->getStore()->getBaseCurrency()->format($total, array(), true);            
            $amount = preg_replace('/[^\d\.,]/i', '', $amount);
        }
        
        $amount = str_replace(',', '', $amount);

        $testMode = $this->getConfigData('test');
        if ($testMode == 0) {
            $url = Mage::helper('gate2play')->getLiveUrl();
        } else {
            $url = Mage::helper('gate2play')->getTestUrl();
        }

        $sender = $this->getConfigData('sender');
        $channel = $this->getConfigData('channel');
        $mode = $this->getConfigData('trans_mode');
        $login = $this->getConfigData('loginid');
        $pwd = $this->getConfigData('password');
        $type = $this->getConfigData('trans_type');
        $transactionID = $order_id;
        $firstName = $billing->getFirstname();
        $family = $billing->getLastname();
        $street = $billing->getStreet(-1);
        $zip = $billing->getPostcode();
        $city = $billing->getCity();
        $state = $billing->getRegion();
        $country = $billing->getCountryModel()->getIso2Code();
        $email = $email;
        $ip = $this->get_client_ip();

        if (empty($state)) {
            $state = $city;
        }

        $street = preg_replace('/\s+/', ' ', trim($street));
        
        $data = "SECURITY.SENDER=$sender" .
                "&TRANSACTION.CHANNEL=$channel" .
                "&TRANSACTION.MODE=$mode" .
                "&USER.LOGIN=$login" .
                "&USER.PWD=$pwd" .
                "&PAYMENT.TYPE=$type" .
                "&PRESENTATION.AMOUNT=$amount" .
                "&PRESENTATION.CURRENCY=$currency" .
                "&IDENTIFICATION.TRANSACTIONID=$transactionID" .
                "&CRITERION.CashU_language=en".
                "&NAME.GIVEN=$firstName" .
                "&NAME.FAMILY=$family" .
                "&ADDRESS.STREET=$street" .
                "&ADDRESS.ZIP=$zip" .
                "&ADDRESS.CITY=$city" .
                "&ADDRESS.STATE=$state" .
                "&ADDRESS.COUNTRY=$country" .
                "&CONTACT.EMAIL=$email" .
                "&CONTACT.IP=$ip";

        $params = array('http' => array(
                'method' => 'POST',
                'header'=> "Content-type: application/x-www-form-urlencoded\r\n"
                    . "Content-Length: " . strlen($data) . "\r\n",  
                'content' => $data
                ));


        $ctx = stream_context_create($params);
        $fp = @fopen($url, 'rb', false, $ctx);
        if (!$fp) {
            Mage::throwException("Problem with $url");        
        }

        $response = @stream_get_contents($fp);

        if ($response === false) {
            Mage::throwException("Problem reading data from $url");   
        }

        $result = json_decode($response);
        $token = '';

        if (isset($result->transaction->token)) {
            $token = $result->transaction->token;
        }

        return $token;
    }

    protected function get_client_ip() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP'])
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']) && $_SERVER['HTTP_X_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']) && $_SERVER['HTTP_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']) && $_SERVER['HTTP_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'])
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';

        return $ipaddress;
    }

    public function getOrder() {
        if (!$this->_order) {
            $this->_order = $this->getInfoInstance()->getOrder();
        }
        return $this->_order;
    }

    public function capture(Varien_Object $payment, $amount) {
        $payment->setStatus(self::STATUS_APPROVED)
                ->setTransactionId($payment->getParentTransactionId())
                ->setParentTransactionId(null)
                ->setIsTransactionClosed(1);       
                               
        return $this;
    }

    public function cancel(Varien_Object $payment) {     
        $payment->setStatus(self::STATUS_DECLINED)
                ->setTransactionId($this->getTransactionId())
                ->setIsTransactionClosed(1);
        
        return $this;
    }

    public function isInitializeNeeded() {
        return true;
    }

    public function initialize($paymentAction, $stateObject) {
        $state = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
        $stateObject->setState($state);
        $stateObject->setStatus(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
        $stateObject->setIsNotified(false);
    }

    public function getConfigPaymentAction() {
        $paymentAction = $this->getConfigData('payment_action');
        return empty($paymentAction) ? true : $paymentAction;
    }
  
}
