<?php

class Shopgo_Bannerslider_Model_System_Config_Source_Type
{
    public function toOptionArray()
    {
        $helper = Mage::helper('bannerslider');

        return array(
            array('value' => 'basic',   'label' => $helper->__('Basic')),
            array('value' => 'thumb',    'label' => $helper->__('Thumbnail')),
            array('value' => 'thumb_cvp',    'label' => $helper->__('Thumbnail controlNav'))
        );
    }
}
