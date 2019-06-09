<?php

class Shopgo_Bannerslider_Model_System_Config_Source_Animation
{
    public function toOptionArray()
    {
        $helper = Mage::helper('bannerslider');

        return array(
            array('value' => 'slide',   'label' => $helper->__('Slide')),
            array('value' => 'fade',    'label' => $helper->__('Fade'))
        );
    }
}
