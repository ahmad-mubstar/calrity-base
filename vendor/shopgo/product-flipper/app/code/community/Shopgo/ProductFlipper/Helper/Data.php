<?php

class Shopgo_ProductFlipper_Helper_Data extends Mage_Core_Helper_Abstract {
    const XML_PATH_FLIPPER_ENABLED = "shopgo_flipper/extension_settings/enable";

    public function isEnabled() {
        return Mage::getStoreConfig(self::XML_PATH_FLIPPER_ENABLED, Mage::app()->getStore());
    }

}