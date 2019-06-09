<?php

class Shopgo_FacebookApi_Controller_Router_Standard extends Mage_Core_Controller_Varien_Router_Standard {

    public function match(Zend_Controller_Request_Http $request) {

    	/*
        Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getBaseUrl() . 'grs');
        return parent::match($request);
        */
    	
    	
    }


    protected function _isAllowed() {
        return true;
        //return Mage::getSingleton('admin/session')->isAllowed('admin/system/config/carriers');
    }

}