<?php

class Shopgo_Social_Block_Google_Account extends Mage_Core_Block_Template
{
    /**
     *
     * @var Shopgo_Social_Model_Google_Oauth2_Client
     */
    protected $client = null;

    /**
     *
     * @var Shopgo_Social_Model_Google_Info_User
     */
    protected $userInfo = null;

    protected function _construct() {
        parent::_construct();

        $this->client = Mage::getSingleton('shopgo_social/google_oauth2_client');
        if(!($this->client->isEnabled())) {
            return;
        }

        $this->userInfo = Mage::registry('shopgo_social_google_userinfo');

        $this->setTemplate('shopgo/social/google/account.phtml');

    }

    protected function _hasData()
    {
        return $this->userInfo->hasData();
    }

    protected function _getGoogleId()
    {
        return $this->userInfo->getId();
    }

    protected function _getStatus()
    {
        if(!empty($this->userInfo->getLink())) {
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
        if(!empty($this->userInfo->getPicture())) {
            return Mage::helper('shopgo_social/google')
                    ->getProperDimensionsPictureUrl($this->userInfo->getId(),
                            $this->userInfo->getPicture());
        }

        return null;
    }

    protected function _getName()
    {
        return $this->userInfo->getName();
    }

    protected function _getGender()
    {
        if(!empty($this->userInfo->getGender())) {
            return ucfirst($this->userInfo->getGender());
        }

        return null;
    }

    protected function _getBirthday()
    {
        if(!empty($this->userInfo->getBirthday())) {
            if((strpos($this->userInfo->getBirthday(), '0000')) === false) {
                $birthday = date('F j, Y', strtotime($this->userInfo->getBirthday()));
            } else {
                $birthday = date(
                    'F j',
                    strtotime(
                        str_replace('0000', '1970', $this->userInfo->getBirthday())
                    )
                );
            }

            return $birthday;
        }

        return null;
    }

}
