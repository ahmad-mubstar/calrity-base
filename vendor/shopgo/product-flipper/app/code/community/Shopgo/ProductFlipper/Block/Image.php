<?php

class Shopgo_ProductFlipper_Block_Image extends Mage_Core_Block_Template {
    public function __construct() {
        $this->setTemplate("shopgo/flipper/image.phtml");
    }

    public function getFlipperImage() {
        $product = $this->getData('product');
        $width = $this->getData('width');
        $height = $this->getData('height');
        return Mage::getModel('shopgo_flipper/flipper')->getFlipperImageByID($product, $width, $height);
    }

    public function getWidth() {
        $this->getData('width');
    }

    public function getHeight() {
        $this->getData('height');
    }
}