<?php

class Shopgo_EC_Helper_Data extends Mage_Core_Helper_Abstract {
    /*
     * @return boolean : The module is enable or disable
     */

    public function isActive() {
        return (boolean) Mage::getStoreConfig('ec/general/active');
    }

    /*
     * @return string : Default ZIP/Postal Code
     */

    public function defaultZipCode() {
        return Mage::getStoreConfig('ec/general/default_zipcode');
    }

    /*
     * @return string : Default shipping method
     */

    public function defaultShippingMethod() {
        return Mage::getStoreConfig('ec/shipping/default_shipping_method');
    }

    /*
     * @return string : Default payment method
     */

    public function defaultPaymentMethod() {
        return Mage::getStoreConfig('ec/payment/default_payment_method');
    }

    /*
     * @return string : Checkout title
     */

    public function checkoutTitle() {
        return Mage::getStoreConfig('ec/general/checkout_title');
    }

    /*
     * @return string : Checkout Description
     */

    public function checkoutDescription() {
        return Mage::getStoreConfig('ec/general/checkout_description');
    }

    /*
     * @return boolean : Use GeoIp or not
     */

    public function useGeoIp() {
        return (boolean) Mage::getStoreConfig('ec/address/use_geoip');
    }

    /*
     * @return boolean : Enable Update Qty or not
     */

    public function enableUpdateQty() {
        return false;
    }

    /**
     * Retrieve path to Favicon
     *
     * @return string
     */
    public function getFaviconFile() {
        $folderName = Mage_Adminhtml_Model_System_Config_Backend_Image_Favicon::UPLOAD_DIR;
        $storeConfig = Mage::getStoreConfig('design/head/shortcut_icon');
        $faviconFile = Mage::getBaseUrl('media') . $folderName . '/' . $storeConfig;
        $absolutePath = Mage::getBaseDir('media') . '/' . $folderName . '/' . $storeConfig;

        if (!is_null($storeConfig) && $this->_isFile($absolutePath)) {
            $url = $faviconFile;
        } else {
            $url = Mage::getDesign()->getSkinUrl('favicon.ico');
        }
        return $url;
    }

    /**
     * If DB file storage is on - find there, otherwise - just file_exists
     *
     * @param string $filename
     * @return bool
     */
    protected function _isFile($filename) {
        if (Mage::helper('core/file_storage_database')->checkDbUsage() && !is_file($filename)) {
            Mage::helper('core/file_storage_database')->saveFileToFilesystem($filename);
        }
        return is_file($filename);
    }
    
    /**
     * @return AW_Orderattributes_Model_Resource_Attribute_Collection
     */
    public function getAttributeCollection()
    {
        $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        /** @var AW_Orderattributes_Model_Resource_Attribute_Collection $collection */
        $collection = Mage::helper('aw_orderattributes/order')
        ->getAttributeCollectionForCheckoutByCustomerGroupId($customerGroupId);
        return $collection;
    }

    // Retrieve current installed version
    public function getExtensionVersion()
    {
      return (string) Mage::getConfig()->getNode()->modules->Shopgo_EC->version;
    }

    // Check for available update
    public function checkUpdate($version = null) {
        $extensions = file_get_contents('http://downloader.devshopgo.me/');

        $extensions = json_decode($extensions, true);

        if(isset($extensions['Shopgo_EC'])) {
            $extension_data = $extensions['Shopgo_EC'];
            if(version_compare($extension_data['version'], $this->getExtensionVersion())) {
                if(!$version) {
                    return $extension_data;
                } else {
                    return $extension_data['version'];
                }
            } else {
              return false;
            }
        }
    }

    // Retrieve translation data
    public function getTranslationData($fileName) {
        $locale = Mage::app()->getLocale()->getLocaleCode();
        $file = Mage::getBaseDir('locale');
        $file .= DS . $locale . DS . $fileName . '.csv';

        $data = array();
        if (file_exists($file)) {
            $parser = new Varien_File_Csv();
            $parser->setDelimiter(',');
            $data = $parser->getDataPairs($file);
        }
        return $data;
    }

    // Retrieve store config
    public function getConfig($path) {
        return Mage::getStoreConfig($path);
    }
}