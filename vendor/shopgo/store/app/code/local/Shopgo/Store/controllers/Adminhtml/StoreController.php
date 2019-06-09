<?php
/*
 * Developer: Rene Voorberg
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 *
 *
*/
class Shopgo_Store_Adminhtml_StoreController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
//        //$this->_title($this->__('Sales'))->_title($this->__('Booking'));
//        $this->loadLayout();
//        //$this->_setActiveMenu('sales/sales');
//        $this->_addContent($this->getLayout()->createBlock('booking/adminhtml_book'));
//        $this->renderLayout();
        Mage::log("opsopsopsopsopsopsopsopsopsopsopsopsopsopsopsopsopsopsopsopsopsopsopsopsopsopsopsopsopsops", Zend_Log::DEBUG, 'test.log');

        echo 'hello';


//        $this->loadLayout();
//        Mage::helper("adminhtml")->getUrl("store/index/index");
//        $this->renderLayout();

    }

    protected function _isAllowed() {
//        return Mage::getSingleton('admin/session')->isAllowed('admin/system/config/carriers');
        return true;
    }

}