<?php

class Shopgo_Social_AccountController extends Mage_Core_Controller_Front_Action
{

    public function preDispatch()
    {
        parent::preDispatch();

        if (!$this->getRequest()->isDispatched()) {
            return $this;
        }

        /*
         * Avoid situations where before_auth_url redirects when doing connect
         * and disconnect from account dashboard. Authenticate.
         */
        if (!Mage::getSingleton('customer/session')
                ->unsBeforeAuthUrl()
                ->unsAfterAuthUrl()
                ->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }

    }

    public function googleAction()
    {
        $userInfo = Mage::getSingleton('shopgo_social/google_info_user')
                ->load();

        Mage::register('shopgo_social_google_userinfo', $userInfo);

        $this->loadLayout();
        $this->renderLayout();
    }

    public function facebookAction()
    {
        $userInfo = Mage::getSingleton('shopgo_social/facebook_info_user')
            ->load();

        Mage::register('shopgo_social_facebook_userinfo', $userInfo);

        $this->loadLayout();
        $this->renderLayout();
    }

    public function twitterAction()
    {
        // Cache user info inside customer session due to Twitter window frame rate limits
        if(!($userInfo = Mage::getSingleton('customer/session')
                ->getSocialTwitterUserinfo()) || !$userInfo->hasData()) {
            
            $userInfo = Mage::getSingleton('shopgo_social/twitter_info_user')
                ->load();

            Mage::getSingleton('customer/session')
                ->setSocialTwitterUserinfo($userInfo);
        }

        Mage::register('shopgo_social_twitter_userinfo', $userInfo);

        $this->loadLayout();
        $this->renderLayout();
    }

    public function linkedinAction()
    {
        $userInfo = Mage::getSingleton('shopgo_social/linkedin_info_user')
            ->load();

        Mage::register('shopgo_social_linkedin_userinfo', $userInfo);

        $this->loadLayout();
        $this->renderLayout();
    }

    public function createFacebookButtonLinkAction() {
        $this->redirectUrl($this->escapeUrl(Mage::getModel('shopgo_social/facebook_button')->_getButtonUrl()));
        return;
    }

}
