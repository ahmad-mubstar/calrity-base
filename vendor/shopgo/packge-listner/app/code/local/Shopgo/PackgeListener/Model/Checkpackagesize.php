<?php

class Shopgo_PackgeListener_Model_checkpackagesize
{
    public function checkproductsize()
    {
        $url="http://downloader.devshopgo.me/shopgo_packages.php";

        try {
            $httpClient = new Varien_Http_Client();
            $response = $httpClient
                ->setUri($url)
                ->setHeaders('Content-Type: json')
                ->request(Varien_Http_Client::GET);

            if ($response->isSuccessful()) {

                $shopgoPackages = json_decode(file_get_contents($url), true);

            } else {

                Mage::helper('PackgeListener')->sendMail("UrlAlert");
                return;
            }
        } catch (Exception $e) {
            Mage::log($e);
        }

        $currentPackage = Mage::getBaseDir().DS.'var'.DS.'package.json';

        if  (!is_readable($currentPackage)) {

            Mage::helper('PackgeListener')->sendMail("FileAlert");
            return;
        }

        $productsCount  = Mage::getModel('catalog/product')->getCollection()
                            ->addAttributeToFilter('status', 1)->getSize();

        $currentPackage = json_decode(file_get_contents($currentPackage),true);
        $packageName    = $currentPackage['name'];
        $packageLimit   = $currentPackage['limit'];

        $availableLimit = !empty($packageLimit) ? $packageLimit : $shopgoPackages[(string)$packageName];

        if ($productsCount > $availableLimit) {
        
            $bcc   = array("generalMail" => Mage::getStoreConfig('trans_email/ident_general/email'),
                           "salesMail"   => Mage::getStoreConfig('trans_email/ident_sales/email')
            );

            $param = array(
                "packageName"    => $packageName,
                "availableLimit" => $availableLimit,
                "productsCount"  => $productsCount,
                "sender"         => $shopgoPackages['sender'],
                "receiver"       => $shopgoPackages['receiver'],
                "subject"        => $shopgoPackages['subject'],
                "senderName"     => $shopgoPackages['senderName'],
                "bcc"            => $bcc
            );

            Mage::helper('PackgeListener')->sendMail("info",$param);
        }
    }
}