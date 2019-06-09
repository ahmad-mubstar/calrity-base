<?php

class Shopgo_AdminLogin_Block_Adminhtml_Page_Header extends Mage_Adminhtml_Block_Page_Header
{
    public function __construct()
    {
        parent::__construct();
        $path = Mage::getStoreConfig('shopgo_adminlogin/general/enabled')
            ? 'shopgo/adminlogin/adminhtml/page/header.phtml'
            : 'page/header.phtml';
        $this->setTemplate($path);
    }
}
