<?php

class Shopgo_Social_Model_Linkedin_Info_User extends Shopgo_Social_Model_Linkedin_Info
{

    /**
     *
     * @var type Mage_Core_Model_Customer
     */
    protected $customer = null;


    /**
     * Load customer user info
     *
     * @param null|int $id Customer Id
     * @return Shopgo_Social_Model_Linkedin_Userinfo
     */
    public function load($id = null)
    {
        if(is_null($id) && Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->customer = Mage::getSingleton('customer/session')->getCustomer();
        } else if(is_int($id)){
            
            $this->customer = Mage::getModel('customer/customer')->load($id);
            
            // TODO: Implement
        }

        if(!$this->customer->getId()) {
            return $this;
        }

        if(!($socialconnectLid = $this->customer->getShopgoSocialLid()) ||
                !($socialconnectLtoken = $this->customer->getShopgoSocialLtoken())) {
            return $this;
        }

        $this->setAccessToken($socialconnectLtoken);
        $this->_load();

        return $this;
    }

    protected function _onException($e) {
        parent::_onException($e);

        $helper = Mage::helper('shopgo_social/linkedin');
        /* @var $helper Shopgo_Social_Helper_Linkedin */

        $helper->disconnect($this->customer);
    }

}