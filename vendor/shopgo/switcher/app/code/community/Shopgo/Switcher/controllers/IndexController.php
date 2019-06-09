<?php

class Shopgo_Switcher_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function testAction() {
        
    }

    protected function _isAllowed() {
        //return true;
        return Mage::getSingleton('admin/session')->isAllowed('admin/system/config/switcher');
    }

}