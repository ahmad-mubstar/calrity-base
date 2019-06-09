<?php
/*
 * Developer: Rene Voorberg
* Team site: http://cmsideas.net/
* Support: http://support.cmsideas.net/
*
*
*/

class Glace_Booking_Model_Attributes extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
	public function getAllOptions(){
		return array(
				'exclude_day' => 'booking/adminhtml_catalog_product_edit_tab_day_exclude',
				'custom_session' => 'booking/adminhtml_catalog_product_edit_tab_customsession',
				'price_rule' => 'booking/adminhtml_catalog_product_edit_tab_price_rule'
		);
	}
}