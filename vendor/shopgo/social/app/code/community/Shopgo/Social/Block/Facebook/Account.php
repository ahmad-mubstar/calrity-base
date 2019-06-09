<?php

class Shopgo_Social_Block_Facebook_Account extends Mage_Core_Block_Template
{
    /**
     *
     * @var Shopgo_Social_Model_Facebook_Oauth2_Client
     */
    protected $client = null;

    /**
     *
     * @var Shopgo_Social_Model_Facebook_Info_User
     */
    protected $userInfo = null;

    protected function _construct() {
        parent::_construct();

        $this->client = Mage::getSingleton('shopgo_social/facebook_oauth2_client');
        if(!($this->client->isEnabled())) {
            return;
        }

        $this->userInfo = Mage::registry('shopgo_social_facebook_userinfo');

        $this->setTemplate('shopgo/social/facebook/account.phtml');
    }
    
    protected function _hasData()
    {
        return $this->userInfo->hasData();
    }    

    protected function _getFacebookId()
    {
        return $this->userInfo->getId();
    }

    protected function _getStatus()
    {
        if($this->userInfo->getLink()) {
            $link = '<a href="'.$this->userInfo->getLink().'" target="_blank">'.
                    $this->escapeHtml($this->userInfo->getName()).'</a>';
        } else {
            $link = $this->userInfo->getName();
        }

        return $link;
    }

    protected function _getEmail()
    {
        return $this->userInfo->getEmail();
    }

    protected function _getPicture()
    {
        if($this->userInfo->getPicture()) {
            return Mage::helper('shopgo_social/facebook')
                    ->getProperDimensionsPictureUrl($this->userInfo->getId(),
                            $this->userInfo->getPicture()->data->url);
        }

        return null;
    }

    protected function _getName()
    {
        return $this->userInfo->getName();
    }

    protected function _getGender()
    {
        if($this->userInfo->getGender()) {
            return ucfirst($this->userInfo->getGender());
        }

        return null;
    }

    protected function _getBirthday()
    {
        if($this->userInfo->getBirthday()) {
            $birthday = date('F j, Y', strtotime($this->userInfo->getBirthday()));
            return $birthday;
        }

        return null;
    }

}