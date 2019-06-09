<?php
/*
 * Developer: Rene Voorberg
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 *
*/
class Glace_Booking_Block_Adminhtml_Book extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		parent::__construct();
		$this->_blockGroup = 'booking';
		$this->_controller = 'adminhtml_book';
		$this->_headerText = Mage::helper('booking')->__('Booked Products');
		
		
	}
}