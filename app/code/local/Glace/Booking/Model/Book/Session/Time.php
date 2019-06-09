<?php
/*
 * Developer: Rene Voorberg
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 *
*/

class Glace_Booking_Model_Book_Session_Time extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('booking/book_session_time');
    }
    
    public function setSession($session){
    	if(!$session)
    		Mage::throwException('no product is setted');
    	
    	if($session instanceof Glace_Booking_Model_Book_Session){
    		$this->setSessionId($session->getId());
    	}else{
    		Mage::throwException('not a session in book setup');
    	}
    }
}