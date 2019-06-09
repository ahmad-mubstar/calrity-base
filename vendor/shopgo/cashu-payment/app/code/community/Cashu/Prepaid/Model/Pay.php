<?php
class Cashu_Prepaid_Model_Pay extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'cashu_prepaid';
    protected $_isInitializeNeeded = true;
    protected $_canUseInternal = true;
    protected $_canUseForMultishipping = false;
    
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('cashu/payment/redirect', array(
            '_secure' => true
        ));
    }
}
