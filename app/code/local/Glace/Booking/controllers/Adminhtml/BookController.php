<?php
/*
 * Developer: Rene Voorberg
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 *
*/
class Glace_Booking_Adminhtml_BookController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        //$this->_title($this->__('Sales'))->_title($this->__('Booking'));
        $this->loadLayout();
        //$this->_setActiveMenu('sales/sales');
        $this->_addContent($this->getLayout()->createBlock('booking/adminhtml_book'));
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('booking/adminhtml_book_grid')->toHtml()
        );
    }

    public function newAction(){
    	
    	$request = Mage::app()->getResponse()
    	->setRedirect(Mage::helper('adminhtml')->getUrl('adminhtml/sales_order_create/index'));

    }

    protected function _isAllowed() {
//        return Mage::getSingleton('admin/session')->isAllowed('admin/system/config/carriers');
        return true;
    }

}