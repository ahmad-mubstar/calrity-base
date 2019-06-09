<?php

class Shopgo_AdminLogin_Helper_Adminhtml_Data extends Mage_Adminhtml_Helper_Data
{
    public function setPageHelpUrl($url=null)
    {
        if (is_null($url) && Mage::getStoreConfig('shopgo_adminlogin/general/enabled')) {
            $url = Mage::helper('adminlogin')->getShopgoPageHelpUrl();
        }

        if (is_null($url)) {
            $request = Mage::app()->getRequest();
            $frontModule = $request->getControllerModule();
            if (!$frontModule) {
                $frontName = $request->getModuleName();
                $router = Mage::app()->getFrontController()->getRouterByFrontName($frontName);

                $frontModule = $router->getModuleByFrontName($frontName);
                if (is_array($frontModule)) {
                    $frontModule = $frontModule[0];
                }
            }
            $url = 'http://www.magentocommerce.com/gethelp/';
            $url.= Mage::app()->getLocale()->getLocaleCode().'/';
            $url.= $frontModule.'/';
            $url.= $request->getControllerName().'/';
            $url.= $request->getActionName().'/';

            $this->_pageHelpUrl = $url;
        }
        $this->_pageHelpUrl = $url;

        return $this;
    }

    public function addPageHelpUrl($suffix)
    {
        $this->_pageHelpUrl = $this->getPageHelpUrl();

        if (!Mage::getStoreConfig('shopgo_adminlogin/general/enabled')) {
            $this->_pageHelpUrl .= $suffix;
        }

        return $this;
    }
}
