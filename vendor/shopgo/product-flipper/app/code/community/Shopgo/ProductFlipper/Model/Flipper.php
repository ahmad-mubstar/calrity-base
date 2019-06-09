<?php

class Shopgo_ProductFlipper_Model_Flipper extends Mage_Core_Model_Abstract {

    public function getFlipperByProductId($product_id, $width, $height) {
        $result = array('flipper' => "", 'type' => "");
        $result['flipper'] = $this->getFlipperImageByProductId($product_id, $width, $height);
        $result['type'] = "image";

        return $result;
    }

    public function getFlipperImageByProductId($product_id, $width, $height) {
        $_product = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('flipper_image')
            ->addAttributeToFilter('entity_id', $product_id)
            ->getLastItem();

        if (!$_product->getFlipperImage() || $_product->getFlipperImage() == "no_selection")
            return "";

        if (!empty($width) && !empty($height))
            return (string)Mage::helper('catalog/image')->init($_product, 'flipper_image')->resize($width, $height);

        return (string)Mage::helper('catalog/image')->init($_product, 'flipper_image')->resize();
    }

}