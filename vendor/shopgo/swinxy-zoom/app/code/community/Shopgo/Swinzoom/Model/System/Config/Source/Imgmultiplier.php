<?php

class Shopgo_Swinzoom_Model_System_Config_Source_Imgmultiplier
{
    public function toOptionArray()
    {
        $helper = Mage::helper('swinzoom');

        return array(
            array('value' => '1',   'label' => $helper->__('1')),
            array('value' => '1.1',   'label' => $helper->__('1.1')),
            array('value' => '1.2',    'label' => $helper->__('1.2')),
            array('value' => '1.3',    'label' => $helper->__('1.3')),
            array('value' => '1.4',    'label' => $helper->__('1.4')),
            array('value' => '1.5',    'label' => $helper->__('1.5')),
            array('value' => '1.6',    'label' => $helper->__('1.6')),
            array('value' => '1.7',    'label' => $helper->__('1.7')),
            array('value' => '1.8',    'label' => $helper->__('1.8')),
            array('value' => '1.9',    'label' => $helper->__('1.9')),
            array('value' => '2',    'label' => $helper->__('2')),
            array('value' => '2.1',    'label' => $helper->__('2.1')),
            array('value' => '2.2',    'label' => $helper->__('2.2')),
            array('value' => '2.3',    'label' => $helper->__('2.3')),
            array('value' => '2.4',    'label' => $helper->__('2.4')),
            array('value' => '2.5',    'label' => $helper->__('2.5'))


        );
    }
}
