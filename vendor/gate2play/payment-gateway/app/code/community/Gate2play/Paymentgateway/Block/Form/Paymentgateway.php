<?php
/**
 * Gate2play Payment payment method model.
 *
 * @category   Gate2play
 * @package    Gate2play_Paymentgateway
 * @author     gate2play.com
 */
class Gate2play_Paymentgateway_Block_Form_Paymentgateway extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('gate2play/paymentgateway/info.phtml');
    }

    protected function _toHtml()
    { 
        if ($this->getMethod()->getCode() != Mage::getSingleton('Gate2play_Paymentgateway_Model_PaymentMethod')->getCode()) {
            return null;
        }

        return parent::_toHtml();
    }
    
    public function setMethodInfo()
    {
        $payment = Mage::getSingleton('checkout/type_onepage')
            ->getQuote()
            ->getPayment();

        $this->setMethod($payment->getMethodInstance());
	//Mage::log('setMethodInfo!');
        return $this;
    }

public function getToken() {
    /*
        $payment = Mage::getSingleton('checkout/type_onepage')
            ->getQuote()
            ->getPayment();

	$testMode = $payment->getMethodInstance()->getConfigData('test');
	if($testMode == 1) {
            $url = "https://test.ctpe.net/frontend/GenerateToken";
	}
        else {
            $url = "https://ctpe.net/frontend/GenerateToken";
        }
        
        $sender = trim($payment->getMethodInstance()->getConfigData('sender'));
        $channel = trim($payment->getMethodInstance()->getConfigData('channel'));
                
	$data = "SECURITY.SENDER=$sender" .
	   "&TRANSACTION.CHANNEL=$channel" .
	   "&TRANSACTION.MODE=INTEGRATOR_TEST" .
	   "&USER.LOGIN=1143238d620a572a726fe92eede0d1ab" .
	   "&USER.PWD=demo" .
	   "&PAYMENT.TYPE=DB" .
	   "&PRESENTATION.AMOUNT=50.99" .
	   "&PRESENTATION.CURRENCY=EUR";
	$params = array('http' => array(
		 'method' => 'POST',
		 'content' => $data
	       ));
         
	$ctx = stream_context_create($params);
	$fp = @fopen($url, 'rb', false, $ctx);
	if (!$fp) {
	  throw new Exception("Problem with $url, $php_errormsg");
	}
	$response = @stream_get_contents($fp);
	if ($response === false) {
	  throw new Exception("Problem reading data from $url, $php_errormsg");
	}
	 
	return $response;*/
}
}
