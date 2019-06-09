<?php

class Shopgo_Store_IndexController extends Mage_Core_Controller_Front_Action {
	//
    public function indexAction()
    {
//        //$this->_title($this->__('Sales'))->_title($this->__('Booking'));
//        $this->loadLayout();
//        //$this->_setActiveMenu('sales/sales');
//        $this->_addContent($this->getLayout()->createBlock('booking/adminhtml_book'));
//        $this->renderLayout();
        $this->loadLayout();
        $this->renderLayout();

        echo 'hello';


//        $this->loadLayout();
//        Mage::helper("adminhtml")->getUrl("store/index/index");
//        $this->renderLayout();

    }

}