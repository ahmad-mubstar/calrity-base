<?php
class Shopgo_Social_Helper_Data extends Mage_Core_Helper_Abstract
{
    public static function log($message, $level = null, $file = '', $forceLog = false)
    {
        if(Mage::getIsDeveloperMode()) {
            Mage::log($message, $level, $file, $forceLog);
        }
    }

    public function createGoogleButtonLink() {
        $google_button = new Shopgo_Social_Block_Google_Button();
        return Mage::helper('core/url')->escapeUrl($google_button->getButtonUrl());
    }

    public function createFacebookButtonLink() {
        $facebook_button = new Shopgo_Social_Block_Facebook_Button();
        return Mage::helper('core/url')->escapeUrl($facebook_button->getButtonUrl());
    }

    public function createTwitterButtonLink() {
        $twitter_button = new Shopgo_Social_Block_Twitter_Button();
        return Mage::helper('core/url')->escapeUrl($twitter_button->getButtonUrl());
    }

    public function createLinkedinButtonLink() {
        $linkedin_button = new Shopgo_Social_Block_Linkedin_Button();
        return Mage::helper('core/url')->escapeUrl($linkedin_button->getButtonUrl());
    }

    public function getConfig($path) {
        return Mage::getStoreConfig('social' . DS . $path);
    }
    
    public function isMageVersionGeq191() {
    
        $version = Mage::getVersionInfo();
        return $version['major'] == 1
               && $version['minor'] >= 9
               && $version['revision'] >= 1;
    }
}