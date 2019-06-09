<?php

class Shopgo_Swinzoom_Model_System_Config_Source_Dockposition
{
    public function toOptionArray()
    {
        $helper = Mage::helper('swinzoom');

        return array(
            array('value' => 'right',   'label' => $helper->__('Right')),
            array('value' => 'left',   'label' => $helper->__('Left'))
        );
    }
}
