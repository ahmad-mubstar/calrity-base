<?php
/**
 * Gate2play Payment payment method model.
 *
 * @category   Gate2play
 * @package    Gate2play_Paymentgateway
 * @author     gate2play.com
 */
class Gate2play_Paymentgateway_Block_Content_Paymentgateway extends Mage_Core_Block_Template
{
    protected $_token;
    
    public function setTokenID() {   
        $this->_token = $this->_getOrder()->getPayment()->getMethodInstance()->generateToken();
    } 
    
    public function getTokenID() {
        return $this->_token;
    }
    
    public function getJs() {
        return $this->_getOrder()->getPayment()->getMethodInstance()->getJavascript();
    }   
    
    public function getBrands() {
        $brands = $this->_getOrder()->getPayment()->getMethodInstance()->getBrands();

        return $brands;
    }    
    
    public function getStatusUrl() {
        return $this->_getOrder()->getPayment()->getMethodInstance()->getStatusUrl();
    }    
        
    protected function _getOrder()
    {
        if ($this->getOrder()) {
            return $this->getOrder();
        } elseif ($orderIncrementId = $this->_getCheckout()->getLastRealOrderId()) {
            return Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
        } else {
            return null;
        }
    }  
    
    public function getMethod()
    {
        if (!$this->_method) {
            $this->_method = Mage::getModel('Gate2play_Paymentgateway_Model_PaymentMethod');
        }
        return $this->_method;
    }    
    
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }    
    
    protected function _getModel()
    {
        return Mage::getModel('Gate2play_Paymentgateway_Model');
    }    
}
?>