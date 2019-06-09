<?php

class Shopgo_AutoNewsletter_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isEnabled()
    {
        return Mage::getStoreConfig('shopgo_autonewsletter/general/enabled');
    }

    public function isMageVersionGeq191()
    {
        $version = Mage::getVersionInfo();

        return $version['major'] == 1
               && $version['minor'] >= 9
               && $version['revision'] >= 1;
    }
}
