<?php

class Shopgo_EC_Model_Adminhtml_System_Config_Source_PlaceOrderButtonColor
{
	public function toOptionArray()
	{
		$themes = array(
				array('value' => 'green', 'label' => 'Green'),
				array('value' => 'blue', 'label' => 'Blue'),
				array('value' => 'red', 'label' => 'Red'),
				array('value' => 'black', 'label' => 'Black'),
				array('value' => 'purple', 'label' => 'Purple'),
				array('value' => 'teal', 'label' => 'Teal'),
				array('value' => 'orange', 'label' => 'Orange'),
				array('value' => 'custom', 'label' => 'Custom'),
		);

		return $themes;
	}
}