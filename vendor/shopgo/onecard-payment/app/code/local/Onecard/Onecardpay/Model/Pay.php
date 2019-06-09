<?php
class Onecard_Onecardpay_Model_Pay extends Mage_Payment_Model_Method_Abstract {
	protected $_code = 'onecardpay';
	
	protected $_isInitializeNeeded      = true;
	protected $_canUseInternal          = true;
	protected $_canUseForMultishipping  = false;
	
	public function getOrderPlaceRedirectUrl() {
		return Mage::getUrl('onecardpay/payment/redirect', array('_secure' => true));
	}
}
