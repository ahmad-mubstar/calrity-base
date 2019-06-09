<?php

class Shopgo_ProductFlipper_Block_Flipper extends Mage_Core_Block_Template {
    const XML_PATH_FLIPPER_IMAGE_SELECTOR = "shopgo_flipper/extension_settings/img_selector";
    const XML_PATH_FLIPPER_PRELOADER = "shopgo_flipper/extension_settings/preloader";
    const XML_PATH_FLIPPER_EFFECT = "shopgo_flipper/image_flip/effect";
    const XML_PATH_FLIPPER_EFFECT_DURATION = "shopgo_flipper/image_flip/duration";

    public function __construct() {
        return parent::__construct();
    }

    protected function _prepareLayout() {
        if (Mage::helper('shopgo_flipper')->isEnabled()) {
            $this->getLayout()->getBlock('head')->addItem('skin_js', 'shopgo/flipper/js/flipper.js');
            $this->getLayout()->getBlock('head')->addCss('shopgo/flipper/css/flipper.css');
        }
        return parent::_prepareLayout();
    }

    public function isPreLoader() {
        return Mage::getStoreConfig(self::XML_PATH_FLIPPER_PRELOADER, Mage::app()->getStore());
    }

    public function getImageContainer() {
        $selector = Mage::getStoreConfig(self::XML_PATH_FLIPPER_IMAGE_SELECTOR, Mage::app()->getStore());
        if (empty($selector))
            return "a.product-image";
        return $selector;
    }

    public function getEffectType() {
        return Mage::getStoreConfig(self::XML_PATH_FLIPPER_EFFECT, Mage::app()->getStore());
    }

    public function getEffectDuration() {
        $duration = Mage::getStoreConfig(self::XML_PATH_FLIPPER_EFFECT_DURATION, Mage::app()->getStore());
        if (empty($duration))
            return 500;
        return $duration;
    }

    public function getUrlController() {
        $url = Mage::getUrl('flipper/flipper/getflipper');
       // return str_replace("https://", "http://", $url);
	return $url;
    }
}
