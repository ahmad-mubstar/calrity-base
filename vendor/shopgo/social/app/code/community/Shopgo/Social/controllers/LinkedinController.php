<?php

class Shopgo_Social_LinkedinController extends Shopgo_Social_Controller_Abstract
{

    protected function _disconnectCallback(Mage_Customer_Model_Customer $customer) {
        Mage::helper('shopgo_social/linkedin')->disconnect($customer);

        Mage::getSingleton('core/session')
            ->addSuccess(
                $this->__('You have successfully disconnected your Linkedin account from our store account.')
            );
    }

    protected function _connectCallback() {
        $errorCode = $this->getRequest()->getParam('error');
        $code = $this->getRequest()->getParam('code');
        $state = $this->getRequest()->getParam('state');
        if(!($errorCode || $code) && !$state) {
            // Direct route access - deny
            return $this;
        }

        if(!$state || $state != Mage::getSingleton('core/session')->getLinkedinCsrf()) {
            return $this;
        }

        if($errorCode) {
            // Linkedin API read light - abort
            if($errorCode === 'access_denied') {
                Mage::getSingleton('core/session')
                    ->addNotice(
                        $this->__('Linkedin Connect process aborted.')
                    );

                return $this;
            }

            throw new Exception(
                sprintf(
                    $this->__('Sorry, "%s" error occured. Please try again.'),
                    $errorCode
                )
            );
        }

        if ($code) {
            // Linkedin API green light - proceed

            $info = Mage::getModel('shopgo_social/linkedin_info')->load();
            /* @var $info Shopgo_Social_Model_Linkedin_Userinfo */

            $token = $info->getClient()->getAccessToken();

            $customersByLinkedinId = Mage::helper('shopgo_social/linkedin')
                ->getCustomersByLinkedinId($info->getId());

            if(Mage::getSingleton('customer/session')->isLoggedIn()) {
                // Logged in user
                if($customersByLinkedinId->getSize()) {
                    // Linkedin account already connected to other account - deny
                    Mage::getSingleton('core/session')
                        ->addNotice(
                            $this->__('Your Linkedin account is already connected to one of our store accounts.')
                        );

                    return $this;
                }

                // Connect from account dashboard - attach
                $customer = Mage::getSingleton('customer/session')->getCustomer();

                Mage::helper('shopgo_social/linkedin')->connectByLinkedinId(
                    $customer,
                    $info->getId(),
                    $token
                );

                Mage::getSingleton('core/session')->addSuccess(
                    $this->__('Your Linkedin account is now connected to your store account. You can now login using our LinkedIn Login button or using store account credentials you will receive to your email address.')
                );

                return $this;
            }

            if($customersByLinkedinId->getSize()) {
                // Existing connected user - login
                $customer = $customersByLinkedinId->getFirstItem();

                Mage::helper('shopgo_social/linkedin')->loginByCustomer($customer);

                Mage::getSingleton('core/session')
                    ->addSuccess(
                        $this->__('You have successfully logged in using your Linkedin account.')
                    );

                return $this;
            }

            $customersByEmail = Mage::helper('shopgo_social/linkedin')
                ->getCustomersByEmail($info->getEmailAddress());

            if($customersByEmail->getSize()) {
                // Email account already exists - attach, login
                $customer = $customersByEmail->getFirstItem();

                Mage::helper('shopgo_social/linkedin')->connectByLinkedinId(
                    $customer,
                    $info->getId(),
                    $token
                );

                Mage::getSingleton('core/session')->addSuccess(
                    $this->__('We have discovered you already have an account at our store. Your Linkedin account is now connected to your store account.')
                );

                return $this;
            }

            // New connection - create, attach, login
            if(empty($info->getFirstName())) {
                throw new Exception(
                    $this->__('Sorry, could not retrieve your Linkedin first name. Please try again.')
                );
            }

            if(empty($info->getLastName())) {
                throw new Exception(
                    $this->__('Sorry, could not retrieve your Linkedin last name. Please try again.')
                );
            }

            Mage::helper('shopgo_social/linkedin')->connectByCreatingAccount(
                $info->getEmailAddress(),
                $info->getFirstName(),
                $info->getLastName(),
                $info->getId(),
                $token
            );

            Mage::getSingleton('core/session')->addSuccess(
                $this->__('Your Linkedin account is now connected to your new user account at our store. Now you can login using our LinkedIn Login button.')
            );
        }
    }

}