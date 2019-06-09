<?php
/*
 * Developer: Rene Voorberg
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 *
*/

class Glace_Booking_Model_Book_Session extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('booking/book_session');
    }
    
    public function setProduct($product){
    	if(!$product)
    		Mage::throwException('no product is setted');
    	
    	if($product instanceof Mage_Catalog_Model_Product){
    		$this->setEntityId($product->getId());
    	}else{
    		Mage::throwException('not a product in book setup');
    	}
    }
}