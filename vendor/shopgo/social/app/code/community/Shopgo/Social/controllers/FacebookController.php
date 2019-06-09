<?php

class Shopgo_Social_FacebookController extends Shopgo_Social_Controller_Abstract
{

    protected function _disconnectCallback(Mage_Customer_Model_Customer $customer) {
        Mage::helper('shopgo_social/facebook')->disconnect($customer);

        Mage::getSingleton('core/session')
            ->addSuccess(
                $this->__('You have successfully disconnected your Facebook account from our store account.')
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

        if(!$state || $state != Mage::getSingleton('core/session')->getFacebookCsrf()) {
            return $this;
        }

        if($errorCode) {
            // Facebook API read light - abort
            if($errorCode === 'access_denied') {
                Mage::getSingleton('core/session')
                    ->addNotice(
                        $this->__('Facebook Connect process aborted.')
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
            /** @var Inchoo_SocialConnect_Helper_Facebook $helper */
            $helper = Mage::helper('shopgo_social/facebook');

            // Facebook API green light - proceed
            $info = Mage::getModel('shopgo_social/facebook_info');
            /* @var $info Shopgo_Social_Model_Facebook_Info */

            $token = $info->getClient()->getAccessToken($code);
            $info->load();

            $customersByFacebookId = $helper->getCustomersByFacebookId($info->getId());

            if(Mage::getSingleton('customer/session')->isLoggedIn()) {
                // Logged in user
                if($customersByFacebookId->getSize()) {
                    // Facebook account already connected to other account - deny
                    Mage::getSingleton('core/session')
                        ->addNotice(
                            $this->__('Your Facebook account is already connected to one of our store accounts.')
                        );

                    return $this;
                }

                // Connect from account dashboard - attach
                $customer = Mage::getSingleton('customer/session')->getCustomer();

                $helper->connectByFacebookId(
                    $customer,
                    $info->getId(),
                    $token
                );

                Mage::getSingleton('core/session')->addSuccess(
                    $this->__('Your Facebook account is now connected to your store account. You can now login using '.
                        'our Facebook Login button or using store account credentials you will receive to your email '.
                        'address.')
                );

                return $this;
            }

            if($customersByFacebookId->getSize()) {
                // Existing connected user - login
                $customer = $customersByFacebookId->getFirstItem();

                $helper->loginByCustomer($customer);

                Mage::getSingleton('core/session')
                    ->addSuccess(
                        $this->__('You have successfully logged in using your Facebook account.')
                    );

                return $this;
            }

            $customersByEmail = $helper->getCustomersByEmail($info->getEmail());

            if($customersByEmail->getSize()) {
                // Email account already exists - attach, login
                $customer = $customersByEmail->getFirstItem();

                $helper->connectByFacebookId(
                    $customer,
                    $info->getId(),
                    $token
                );

                Mage::getSingleton('core/session')->addSuccess(
                    $this->__('We have discovered you already have an account at our store. Your Facebook account is '.
                        'now connected to your store account.')
                );

                return $this;
            }

            // New connection - create, attach, login
            if(!$info->getFirstName()) {
                throw new Exception(
                    $this->__('Sorry, could not retrieve your Facebook first name. Please try again.')
                );
            }

            if(!$info->getLastName()) {
                throw new Exception(
                    $this->__('Sorry, could not retrieve your Facebook last name. Please try again.')
                );
            }

            // No email address workaround
            if(!$info->getEmail())
                $info->setEmail($info->getUsername() . '@facebook.com');



            $birthday = $info->getBirthday();
            $birthday = Mage::app()->getLocale()->date($birthday, null, null, false)
                ->toString('yyyy-MM-dd');

            $gender = $info->getGender();
            if(empty($gender)) {
                $gender = null;
            } else if($gender = 'male') {
                $gender = 1;
            } else if($gender = 'female') {
                $gender = 2;
            }

            $helper->connectByCreatingAccount(
                $info->getEmail(),
                $info->getFirstName(),
                $info->getLastName(),
                $info->getId(),
                $birthday,
                $gender,
                $token
            );

            Mage::getSingleton('core/session')->addSuccess(
                $this->__('Your Facebook account is now connected to your new user account at our store.'.
                    ' Now you can login using our Facebook Login button.')
            );
        }
    }

}