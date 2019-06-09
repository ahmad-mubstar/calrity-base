<?php
/*
 * Developer: Rene Voorberg
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 *
*/

class Glace_Booking_Model_Book_Item extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('booking/book_item');
    }
    
    public function setProduct($product){
    	if(!$product)
    		Mage::throwException('product is null');
    	if($product instanceof Mage_Catalog_Model_Product){
    		$this->setProductId($product->getId());
    	}else{
    		Mage::throwException('not a product instance');
    	}
    }
    
    public function setBook($book){
    	if(!$book)
    		Mage::throwException('book is null');
    	if($book instanceof Glace_Booking_Model_Book){
    		$this->setBookId($book->getId());
    	}else{
    		Mage::throwException('not a book instance');
    	}
    }
}