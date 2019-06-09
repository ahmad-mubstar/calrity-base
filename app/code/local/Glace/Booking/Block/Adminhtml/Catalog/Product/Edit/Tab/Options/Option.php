<?php
/*
 * Developer: Rene Voorberg
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 *
*/
class Glace_Booking_Block_Adminhtml_Catalog_Product_Edit_Tab_Options_Option
extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Option
{
	/**
	 * Class constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('booking/edit/options/option.phtml');
	}
	/**
	 * Retrieve html templates for different types of product custom options
	 *
	 * @return string
	 */
	public function getTemplatesHtml()
	{
		$canEditPrice = $this->getCanEditPrice();
		$canReadPrice = $this->getCanReadPrice();
	
		$this->getChild('multidate_option_type')
		->setCanReadPrice($canReadPrice)
		->setCanEditPrice($canEditPrice);
		$templates = parent::getTemplatesHtml() . "\n" .
				$this->getChildHtml('multidate_option_type');
		return $templates;    
	} 

}
