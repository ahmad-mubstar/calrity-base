<?php
class Shopgo_EC_Model_Resources_Cityinput
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'dropdown', 'label' => 'Dropdown'),
            array('value' => 'autocomplete', 'label' => 'Autocomplete'),
          );
    }
}
