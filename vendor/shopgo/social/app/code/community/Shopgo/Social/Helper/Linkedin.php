<?php

class Shopgo_Social_Helper_Linkedin extends Mage_Core_Helper_Abstract
{

    public function disconnect(Mage_Customer_Model_Customer $customer) {
        // Note: LinkedIn API doesn't support revoking OAuth2 token programatically

        $pictureFilename = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA)
                .DS
                .'shopgo'
                .DS
                .'social'
                .DS
                .'linkedin'
                .DS
                .$customer->getShopgoSocialLid();

        if(file_exists($pictureFilename)) {
            @unlink($pictureFilename);
        }

        $customer->setShopgoSocialLid(null)
        ->setShopgoSocialLtoken(null)
        ->save();
    }

    public function connectByLinkedinId(
            Mage_Customer_Model_Customer $customer,
            $linkedinId,
            $token)
    {
        $customer->setShopgoSocialLid($linkedinId)
                ->setShopgoSocialLtoken($token)
                ->save();

        Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
    }

    public function connectByCreatingAccount(
            $email,
            $firstName,
            $lastName,
            $linkedinId,
            $token)
    {
        $customer = Mage::getModel('customer/customer');

        $customer->setWebsiteId(Mage::app()->getWebsite()->getId())
                ->setEmail($email)
                ->setFirstname($firstName)
                ->setLastname($lastName)
                ->setShopgoSocialLid($linkedinId)
                ->setShopgoSocialLtoken($token)
                ->setPassword($customer->generatePassword(10))
                ->save();

        $customer->setConfirmation(null);
        $customer->save();

        $customer->sendNewAccountEmail('confirmed', '', Mage::app()->getStore()->getId());

        Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);

    }

    public function loginByCustomer(Mage_Customer_Model_Customer $customer)
    {
        if($customer->getConfirmation()) {
            $customer->setConfirmation(null);
            $customer->save();
        }

        Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
    }

    public function getCustomersByLinkedinId($linkedinId)
    {
        $customer = Mage::getModel('customer/customer');

        $collection = $customer->getCollection()
            ->addAttributeToFilter('shopgo_social_lid', $linkedinId)
            ->setPageSize(1);

        if($customer->getSharingConfig()->isWebsiteScope()) {
            $collection->addAttributeToFilter(
                'website_id',
                Mage::app()->getWebsite()->getId()
            );
        }

        if(Mage::getSingleton('customer/session')->isLoggedIn()) {
            $collection->addFieldToFilter(
                'entity_id',
                array('neq' => Mage::getSingleton('customer/session')->getCustomerId())
            );
        }

        return $collection;
    }

    public function getCustomersByEmail($email)
    {
        $customer = Mage::getModel('customer/customer');

        $collection = $customer->getCollection()
                ->addFieldToFilter('email', $email)
                ->setPageSize(1);

        if($customer->getSharingConfig()->isWebsiteScope()) {
            $collection->addAttributeToFilter(
                'website_id',
                Mage::app()->getWebsite()->getId()
            );
        }

        if(Mage::getSingleton('customer/session')->isLoggedIn()) {
            $collection->addFieldToFilter(
                'entity_id',
                array('neq' => Mage::getSingleton('customer/session')->getCustomerId())
            );
        }

        return $collection;
    }

    public function getProperDimensionsPictureUrl($linkedinId, $pictureUrl)
    {
        $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)
                .'shopgo'
                .'/'
                .'social'
                .'/'
                .'linkedin'
                .'/'
                .$linkedinId;

        $filename = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA)
                .DS
                .'shopgo'
                .DS
                .'social'
                .DS
                .'linkedin'
                .DS
                .$linkedinId;

        $directory = dirname($filename);

        if (!file_exists($directory) || !is_dir($directory)) {
            if (!@mkdir($directory, 0777, true))
                return null;
        }

        if(!file_exists($filename) ||
                (file_exists($filename) && (time() - filemtime($filename) >= 3600))){
            $client = new Zend_Http_Client($pictureUrl);
            $client->setStream();
            $response = $client->request('GET');
            stream_copy_to_stream($response->getStream(), fopen($filename, 'w'));

            $imageObj = new Varien_Image($filename);
            $imageObj->constrainOnly(true);
            $imageObj->keepAspectRatio(true);
            $imageObj->keepFrame(false);
            $imageObj->resize(150, 150);
            $imageObj->save($filename);
        }

        return $url;
    }

}