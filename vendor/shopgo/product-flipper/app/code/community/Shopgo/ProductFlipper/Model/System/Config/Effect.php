<?php

class Shopgo_ProductFlipper_Model_System_Config_Effect {
    public function toOptionArray() {
        $helper = Mage::helper('shopgo_flipper');
        return array(
            array('value' => 'none', 'label' => $helper->__('No effect')),
            array('value' => 'fade', 'label' => $helper->__('Fade Out/In')),
        );
    }
}