<?php
/*
 * Developer: Rene Voorberg
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 *
*/
class Glace_Booking_Block_Adminhtml_Catalog_Product_View_Options_Type_Multidate
extends Mage_Catalog_Block_Product_View_Options_Abstract
{
 
	protected function _construct()
	{
		parent::_construct();
	}
	
	/**
	 * Returns default value to show in text input
	 *
	 * @return string
	 */
	public function getDefaultValue()
	{
		return $this->getProduct()->getPreconfiguredValues()->getData('options/' . $this->getOption()->getId());
	}
	
	public function getBookPrice(){
		return $this->getProduct()->getPrice();
	}
	
	public function getSessions(){
		return '[]';
	}

}