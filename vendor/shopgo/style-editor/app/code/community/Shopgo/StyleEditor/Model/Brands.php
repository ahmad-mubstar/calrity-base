<?php

class Shopgo_StyleEditor_Model_Brands extends Mage_Core_Model_Abstract
{

    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'bottom',
                'label' => 'Bottom',
            ),
            array(
                'value' => 'top',
                'label' => 'Top',
            ),
            array(
                'value' => 'none',
                'label' => 'None',
            ),
        );
    }

}