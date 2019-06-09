<?php

class Shopgo_ProductFlipper_FlipperController extends Mage_Core_Controller_Front_Action {
    public function getFlipperAction() {
        $result['flipper'] = '';

        try {
            $productID = $this->getRequest()->getParam('product_id');
            $width = $this->getRequest()->getParam('width');
            $height = $this->getRequest()->getParam('height');
            $result = Mage::getModel('shopgo_flipper/flipper')->getFlipperByProductId($productID, $width, $height);
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'shopgo_product_flipper.log');
            $result['flipper'] = '';
        }

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
} 