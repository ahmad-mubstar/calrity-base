<?php

class Shopgo_Social_Block_Facebook_Button extends Mage_Core_Block_Template
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

        // CSRF protection
        Mage::getSingleton('core/session')->setFacebookCsrf($csrf = md5(uniqid(rand(), true)));
        $this->client->setState($csrf);

        Mage::getSingleton('customer/session')
            ->setSocialRedirect(Mage::helper('core/url')->getCurrentUrl());

        $this->setTemplate('shopgo/social/facebook/button.phtml');
    }

    protected function _getButtonUrl()
    {
        if(is_null($this->userInfo) || !$this->userInfo->hasData()) {
            return $this->client->createAuthUrl();
        } else {
            return $this->getUrl('social/facebook/disconnect');
        }
    }

    protected function _getButtonText()
    {
        if(is_null($this->userInfo) || !$this->userInfo->hasData()) {
            if(!($text = Mage::registry('shopgo_social_button_text'))){
                $text = $this->__('Connect');
            }
        } else {
            $text = $this->__('Disconnect');
        }

        if($text == 'Register') {
            $text = 'Sign up with Facebook';
        }

        if($text == 'Login') {
            $text = 'Sign in with Facebook';
        }

        return $this->__($text);
    }

    public function getButtonUrl()
    {
        return $this->_getButtonUrl();
    }

}