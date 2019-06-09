<?php

class Shopgo_StyleEditor_Model_Flipperscount extends Mage_Core_Model_Abstract
{

    public function toOptionArray()
    {
        return array(
            array(
                'value' => 2,
                'label' => '2 columns',
            ),
            array(
                'value' => 3,
                'label' => '3 Columns',
            ),
            array(
                'value' => 4,
                'label' => '4 columns',
            )
        );
    }

}