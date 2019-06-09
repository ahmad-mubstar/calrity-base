<?php

class Faturah_Pay_Model_Pay extends Mage_Payment_Model_Method_Abstract {

    protected $_code = 'faturah';
    protected $_isInitializeNeeded = true;
    protected $_canUseInternal = true;
    protected $_canUseForMultishipping = false;

    public function getOrderPlaceRedirectUrl() {
        return Mage::getUrl('faturah/payment', array('_secure' => true));
    }

}
