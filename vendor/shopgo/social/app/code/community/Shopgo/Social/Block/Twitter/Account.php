<?php

class Shopgo_Social_Block_Twitter_Account extends Mage_Core_Block_Template
{
    /**
     *
     * @var Shopgo_Social_Model_Twitter_Oauth_Client
     */
    protected $client = null;
    
    /**
     *
     * @var Shopgo_Social_Model_Twitter_Info_User
     */
    protected $userInfo = null;

    protected function _construct() {
        parent::_construct();

        $this->client = Mage::getSingleton('shopgo_social/twitter_oauth_client');
        if(!($this->client->isEnabled())) {
            return;
        }

        $this->userInfo = Mage::registry('shopgo_social_twitter_userinfo');

        $this->setTemplate('shopgo/social/twitter/account.phtml');

    }

    protected function _hasData()
    {
        return $this->userInfo->hasData();
    }


    protected function _getTwitterId()
    {
        return $this->userInfo->getId();
    }

    protected function _getStatus()
    {
        return '<a href="'.sprintf('https://twitter.com/%s', $this->userInfo->getScreenName()).'" target="_blank">'.
                $this->escapeHtml($this->userInfo->getScreenName()).'</a>';
    }

    protected function _getPicture()
    {
        if(!empty($this->userInfo->getProfileImageUrl())) {
            return Mage::helper('shopgo_social/twitter')
                    ->getProperDimensionsPictureUrl($this->userInfo->getId(),
                            $this->userInfo->getProfileImageUrl());
        }

        return null;
    }

    protected function _getName()
    {
        return $this->userInfo->getName();
    }

}