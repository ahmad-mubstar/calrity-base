<?php
/*
 * Developer: Rene Voorberg
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 *
*/

class Glace_Booking_Model_Product_Type_Booking
extends Mage_Catalog_Model_Product_Type_Simple{
	
	
	
	/**
	 * Check is virtual product
	 *
	 * @param Mage_Catalog_Model_Product $product
	 * @return bool
	 */
	public function isVirtual($product = null)
	{
		if($product)
		{
			$product = Mage::getModel('catalog/product')->load($product->getId());
			
			return $product->getAttributeText('include_shipping') == 'disabled';
		}
		
		return true;
	}
	
}