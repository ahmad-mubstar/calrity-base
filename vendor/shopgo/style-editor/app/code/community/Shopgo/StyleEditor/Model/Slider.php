<?php

class Shopgo_StyleEditor_Model_Slider extends Mage_Core_Model_Abstract
{

    public function toOptionArray()
    {



        return array(
            array(
                'value' => 'full',
                'label' => 'Full',
            ),
            array(
                'value' => 'left',
                'label' => 'Left',
            ),
            array(
                'value' => 'middle',
                'label' => 'Middle',
            ),
            array(
                'value' => 'right',
                'label' => 'Right',
            ),
            array(
                'value' => 'none',
                'label' => 'None',
            ),
        );
    }

}