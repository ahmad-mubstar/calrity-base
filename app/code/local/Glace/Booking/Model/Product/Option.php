<?php
/*
 * Developer: Rene Voorberg
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 *
*/
class Glace_Booking_Model_Product_Option extends Mage_Catalog_Model_Product_Option{
	
	const OPTION_GROUP_MULTIDATE       = 'multidate';
	const OPTION_TYPE_MULTIDATE_TYPE   = 'multidate_type';
	/**
	 * Get group name of option by given option type
	 *
	 * @param string $type
	 * @return string
	 */
	public function getGroupByType($type = null)
	{
		if (is_null($type)) {
			$type = $this->getType();
		}
	
		$group = parent::getGroupByType($type);
		if( $group === '' && $type == self::OPTION_TYPE_MULTIDATE_TYPE ){
			$group = self::OPTION_GROUP_MULTIDATE;
		}
		return $group;
	}
	/**
	 * Group model factory
	 *
	 * @param string $type Option type
	 * @return Mage_Catalog_Model_Product_Option_Group_Abstract
	 */
	public function groupFactory($type)
	{
		if( $type === self::OPTION_TYPE_MULTIDATE_TYPE ){
			return Mage::getModel('booking/catalog_product_option_type_multidate');
		}
		return parent::groupFactory($type);
	}
}