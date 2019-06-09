<?php
/*
 * Developer: Rene Voorberg
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 *
*/

class Glace_Booking_Block_Adminhtml_Catalog_Product_Edit_Tab_Options_Type_Multidate
extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Type_Abstract
{
	
	
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('booking/options/type/multidate.phtml');
    }
}