<?php
/**
 * Rewrites Mage_Core_Model_Store
 * Returns currency code based on visitor's IP Address
 *
 * @category   ShopGo
 * @package    Shopgo_Geo
 * @version    1.0.2
 * @author     Ali Halabyah <ali@shopgo.me> 
 * @link       http://shopgo.me
 */
 
class Shopgo_Geo_Model_Store extends Mage_Core_Model_Store
{     
    /**
     * Update default store currency code
     *
     * @return string
     */
    public function getDefaultCurrencyCode() {
        //
        if (Mage::helper('geo')->isEnabled() && Mage::getStoreConfig('geo/general/currency_switcher')) {
            $geoModel = Mage::getSingleton('geo/country');
            $currentCountryCode = $geoModel->getCountry();
            $allowed_currency_codes = Mage::app()->getStore()->getAvailableCurrencyCodes(true);
            $currentCountryCurrencyCode = Mage::helper('geo')->getCurrencyByCountry($currentCountryCode);
            if(in_array($currentCountryCurrencyCode, $allowed_currency_codes))
                return $currentCountryCurrencyCode;
        }
        return parent::getDefaultCurrencyCode();
    }
}