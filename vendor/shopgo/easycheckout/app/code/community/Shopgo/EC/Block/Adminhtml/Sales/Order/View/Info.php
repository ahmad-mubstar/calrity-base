<?php
class Shopgo_EC_Block_Adminhtml_Sales_Order_View_Info extends Mage_Adminhtml_Block_Sales_Order_View_Info
{
    protected function _beforeToHtml(){
        if(Mage::helper('ec')->enableFSockOpen()) {
            if(Mage::helper('ec')->isActive()) {
                if(Mage::helper('ec')->useGeoIp()) {
                    $this->setTemplate('ec/sales/order/view/info.phtml');
                }
            }
        } else {
            if(Mage::helper('ec')->isActive()) {
                if(Mage::helper('ec')->useGeoIp()) {
                    Mage::getSingleton('core/session')->addError('Your server is not support fsockopen.');
                }
            }
        }
        parent::_beforeToHtml();
    }
}
