<?php

class Shopgo_StyleEditor_Adminhtml_StyleeditorController extends Mage_Adminhtml_Controller_Action
{
    //
    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('styleeditor');
        return $this;
    }

    //
    public function indexAction()
    {
        $this->_title($this->__('ShopGo StyleEditor'));
        $this->_initAction();
        $this->renderLayout();
    }

    public function themeAction()
    {
        $styleEditor = Mage::getModel('styleeditor/styleeditor');
        $themeData = $styleEditor->getThemeData();

        Mage::app()->getResponse()
            ->setHeader('content-type', 'application/json; charset=utf-8')
            ->setBody(json_encode($themeData));
    }

    public function saveAction()
    {
        $data = $this->getRequest()->getParams();
        $styleEditor = Mage::getModel('styleeditor/styleeditor');
        $result = $styleEditor->saveThemeData($data);


        Mage::app()->getResponse()
            ->setHeader('content-type', 'application/json; charset=utf-8')
            ->setBody(json_encode($result));
    }


    public function updateAction()
    {
        $data = $this->getRequest()->getParams();


        Mage::log('got it.', null, 'my.log', true);
//
//
//
//        $styleEditor = Mage::getModel('styleeditor/styleeditor');
//        $result = $styleEditor->saveThemeData($data);

        Mage::app()->getResponse()
            ->setHeader('content-type', 'application/json; charset=utf-8')
            ->setBody(json_encode($result));
    }


    public function uploadAction()
    {
        $data = $this->getRequest()->getParams();
        $styleEditor = Mage::getModel('styleeditor/styleeditor');
        $result = $styleEditor->uploadThemeImage();

        Mage::app()->getResponse()
            ->setHeader('content-type', 'application/json; charset=utf-8')
            ->setBody(json_encode($result));
    }

    protected function _isAllowed() {
        return true;
        //return Mage::getSingleton('admin/session')->isAllowed('admin/system/generalc/catalog/styleeditor'); // note admin/pdfadmin_menu from file: adminhtml.xml
    }

}
