<?php

class Shopgo_Bannerslider_Model_System_Config_Source_Position
{
    public function toOptionArray()
    {
        $helper = Mage::helper('bannerslider');

        return array(
            array('value' => 'top',   'label' => $helper->__('Top')),
            array('value' => 'bottom',    'label' => $helper->__('Bottom')),
            array('value' => '',    'label' => $helper->__('Free'))
        );
    }
}
