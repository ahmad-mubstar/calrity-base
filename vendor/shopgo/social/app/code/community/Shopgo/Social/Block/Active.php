<?php

class Shopgo_Social_Block_Active extends Mage_Core_Block_Template 
{
    public function getAppId()
    {
        return Mage::getStoreConfig('social/general/appid');
    }

    public function getSecretKey()
    {
        return Mage::getStoreConfig('social/general/secret');
    }
}