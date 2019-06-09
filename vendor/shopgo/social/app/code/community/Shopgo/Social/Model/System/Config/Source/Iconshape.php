<?php

class Shopgo_Social_Model_System_Config_Source_Iconshape
{
    public function toOptionArray()
    {
        $helper = Mage::helper('shopgo_social');

        return array(
            array('value' => 'square',   'label' => $helper->__('Square')),
            array('value' => 'circle',    'label' => $helper->__('Circular')),
            array('value' => 'radius',    'label' => $helper->__('Rounded Corners'))
        );
    }
}
