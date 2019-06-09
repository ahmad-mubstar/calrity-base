<?php

class Shopgo_EC_Model_Adminhtml_System_Config_Source_ShippingDateFormat
{
	public function toOptionArray()
	{
		$format = array(
				array('value' => 'yyyy/MM/dd hh:mm tt', 'label' => 'YYYY/MM/DD'),
				array('value' => 'yyyy/dd/MM hh:mm tt', 'label' => 'YYYY/DD/MM'),
				
		);

		return $format;
	}
}
