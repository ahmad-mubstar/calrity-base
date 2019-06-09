<?php

class Shopgo_EC_Block_EC_Shipping extends Mage_Checkout_Block_Onepage_Billing {

    public function _prepareLayout() {
        return parent::_prepareLayout();
    }

    /* */

    public function getAddressesHtmlSelect($type) {
        if ($this->isCustomerLoggedIn()) {
            $options = array();
            foreach ($this->getCustomer()->getAddresses() as $address) {
                $options[] = array(
                    'value' => $address->getId(),
                    'label' => $address->format('oneline')
                );
            }

            $addressId = $this->getAddress()->getCustomerAddressId();
            if (empty($addressId)) {
                if ($type == 'billing') {
                    $address = $this->getCustomer()->getPrimaryBillingAddress();
                } else {
                    $address = $this->getCustomer()->getPrimaryShippingAddress();
                }
                if ($address) {
                    $addressId = $address->getId();
                }
            }

            $select = $this->getLayout()->createBlock('core/html_select')
                    ->setName($type . '_address_id')
                    ->setId($type . '-address-select')
                    ->setClass('address-select')
                    ->setExtraParams('onchange=""')
                    ->setValue($addressId)
                    ->setOptions($options);

            $select->addOption('', Mage::helper('checkout')->__('New Address'));

            return $select->getHtml();
        }
        return '';
    }
    
    public function getCountryHtmlSelect($type)
    {
    	$countryId = $this->getAddress()->getCountryId();
    	if (is_null($countryId)) {
    		$countryId = Mage::helper('core')->getDefaultCountry();
    	}
    	$countryId = (Mage::getModel('geo/country')->getCountry() && Mage::getStoreConfig('ec/address/use_geoip')) ? Mage::getModel('geo/country')->getCountry() : $countryId;
        $countryOptions = $this->getCountryOptions();
        $countryOptions = array_filter($countryOptions, 'Shopgo_EC_Block_EC_Billing::filterEmpty');
    	$select = $this->getLayout()->createBlock('core/html_select')
    	->setName($type.'[country_id]')
    	->setId($type.':country_id')
    	->setTitle(Mage::helper('checkout')->__('Country'))
    	->setClass('validate-select')
    	->setValue($countryId)
    	->setOptions($countryOptions);
    	if ($type === 'shipping') {
    		$select->setExtraParams('onchange="if(window.shipping)shipping.setSameAsBilling(false);"');
    	}
    
    	return $select->getHtml();
    }

    public static function filterEmpty($arr) {
        return (bool)$arr['value'];
    }

}