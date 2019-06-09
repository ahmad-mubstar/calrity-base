<?php

class Shopgo_AdminLogin_Helper_Data extends Mage_Core_Helper_Abstract
{
    const SHOPGO_PAGE_HELP_URL = 'http://support.shopgo.me';

    public function getShopgoPageHelpUrl()
    {
        return self::SHOPGO_PAGE_HELP_URL;
    }
}
