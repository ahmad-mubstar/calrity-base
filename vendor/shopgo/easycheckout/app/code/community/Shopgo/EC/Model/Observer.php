<?php
class Shopgo_EC_Model_Observer extends Mage_Core_Controller_Varien_Action {
    
    public function __construct() {

    }

    public function initController($observer) {
        if (Mage::helper('ec')->isActive()) {
            $observer->getControllerAction()->_redirect('ec');
        }
    }
    
    public function admin_system_config_changed_section_onestepcheckout($observer) {
        $defaultCountry = Mage::getStoreConfig('ec/general/default_country');
        $guestCheckout = Mage::getStoreConfig('ec/general/guest_checkout');
        $enableAgreements = Mage::getStoreConfig('ec/general/enable_agreements');
        $allowOrder = Mage::getStoreConfig('ec/general/allow_order');
        $allowItems = Mage::getStoreConfig('ec/general/allow_items');
        Mage::getModel('core/config')->saveConfig('general/country/default', $defaultCountry);
        Mage::getModel('core/config')->saveConfig('checkout/options/guest_checkout', $guestCheckout);
        Mage::getModel('core/config')->saveConfig('checkout/options/enable_agreements', $enableAgreements);
        Mage::getModel('core/config')->saveConfig('sales/gift_messages/allow_order', $allowOrder);
        Mage::getModel('core/config')->saveConfig('sales/gift_messages/allow_items', $allowItems);
        //Fix on Magento 1.5.0.0
        Mage::getModel('core/config')->saveConfig('sales/gift_options/allow_order', $allowOrder);
        Mage::getModel('core/config')->saveConfig('sales/gift_options/allow_items', $allowItems);
        //-----------------------//
    }

    public function admin_system_config_changed_section_general($observer) {
        $defaultCountry = Mage::getStoreConfig('general/country/default');
        Mage::getModel('core/config')->saveConfig('ec/general/default_country', $defaultCountry);
    }

    public function admin_system_config_changed_section_checkout($observer) {
        $guestCheckout = Mage::getStoreConfig('checkout/options/guest_checkout');
        $enableAgreements = Mage::getStoreConfig('checkout/options/enable_agreements');
        Mage::getModel('core/config')->saveConfig('ec/general/guest_checkout', $guestCheckout);
        Mage::getModel('core/config')->saveConfig('ec/general/enable_agreements', $enableAgreements);
    }

    public function admin_system_config_changed_section_sales($observer) {
        $allowOrder = Mage::getStoreConfig('sales/gift_messages/allow_order');
        $allowItems = Mage::getStoreConfig('sales/gift_messages/allow_items');
        //Fix on Magento 1.5.0.0
        $allowOrder = Mage::getStoreConfig('sales/gift_options/allow_order');
        $allowItems = Mage::getStoreConfig('sales/gift_options/allow_items');
        //-----------------------//
        Mage::getModel('core/config')->saveConfig('ec/general/allow_order', $allowOrder);
        Mage::getModel('core/config')->saveConfig('ec/general/allow_items', $allowItems);
    }

    public function afterAddToCart(Varien_Event_Observer $observer) {
        if(!Mage::getStoreConfig('ec/advanced/skip_cart')) return;
        $response = $observer->getResponse();
        $response->setRedirect(Mage::getUrl('checkout/onepage'));
        Mage::getSingleton('checkout/session')->setNoCartRedirect(true);
    }

    public function clearCart(Varien_Event_Observer $observer) {
        if(!Mage::getStoreConfig('ec/advanced/allow_one_item')) return;
        $item = $observer->getEvent()->getQuoteItem();
        $cart = Mage::getSingleton('checkout/cart');
        foreach ($cart->getQuote()->getItemsCollection() as $_item) {
            if($_item->getId() != $item->getId()) {
                $_item->isDeleted(true);
            }
        }
    }

    public function handleCollect($observer) {
        if (!Mage::getStoreConfig('ec/ux/always_show_shipping_methods'))
            return $this;
            
        $quote = $observer->getEvent()->getQuote();
        $shippingAddress = $quote->getShippingAddress();
        $billingAddress = $quote->getBillingAddress();
        $saveQuote = false;
        if (!$shippingAddress->getCountryId()) {
            $country = Mage::getStoreConfig('shipping/origin/country_id');
            $state = Mage::getStoreConfig('shipping/origin/region_id');
            $postcode = Mage::getStoreConfig('shipping/origin/postcode');
            $method = Mage::getStoreConfig('shipping/origin/shippingmethod');
            
            $shippingAddress
                ->setCountryId($country)
                ->setRegionId($state)
                ->setPostcode($postcode)
                ->setShippingMethod($method)
                ->setCollectShippingRates(true);
            $shippingAddress->save();
            
            $saveQuote = true;
        }
        if (Mage::getStoreConfig('ec/ux/always_show_shipping_methods') && !$billingAddress->getCountryId()) {
            $country = Mage::getStoreConfig('shipping/origin/country_id');
            $state = Mage::getStoreConfig('shipping/origin/region_id');
            $postcode = Mage::getStoreConfig('shipping/origin/postcode');
                        
            $billingAddress
                ->setCountryId($country)
                ->setRegionId($state)
                ->setPostcode($postcode);
                
            $saveQuote = true;
            
            $quote->save();
        }
        if ($saveQuote)
            $quote->save();
        return $this;
    }
}
