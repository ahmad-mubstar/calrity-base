<?php

class Shopgo_Social_Model_System_Config_Source_Iconsize
{
    public function toOptionArray()
    {
        $helper = Mage::helper('shopgo_social');

        return array(
            array('value' => 'Medium',    'label' => $helper->__('Medium')),
            array('value' => 'Large',    'label' => $helper->__('Large')),
            array('value' => 'x-large',    'label' => $helper->__('x-large')),
            array('value' => 'xx-large',    'label' => $helper->__('xx-large'))
        );
    }
}
