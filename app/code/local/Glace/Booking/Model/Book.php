<?php
/*
 * Developer: Rene Voorberg
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 *
*/

class Glace_Booking_Model_Book extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('booking/book');
    }
}