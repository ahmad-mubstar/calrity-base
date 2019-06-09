<?php

class Shopgo_Social_Model_System_Config_Source_Icons
{
    public function toOptionArray()
    {
        $helper = Mage::helper('shopgo_social');

        return array(
            array('value' => 'icon icon-facebook',   'label' => $helper->__('Facebook')),
            array('value' => 'icon icon-twitter',    'label' => $helper->__('Twiter')),
            array('value' => 'icon icon-google-plus',    'label' => $helper->__('Google Plus')),
            array('value' => 'icon icon-pinterest',    'label' => $helper->__('Pinterest')),
            array('value' => 'icon icon-tumblr',    'label' => $helper->__('Tumblr'))
        );
    }
}
