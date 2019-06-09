<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension
 * to newer versions in the future.
 *
 * @category   Netzarbeiter
 * @package    Shopgo_CatalogLogin
 * @copyright  Copyright (c) 2012 Vinai Kopp http://netzarbeiter.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Shopgo_LogoUpload_Helper_Data extends Mage_Core_Helper_Abstract {

    public function saveConfig($path, $value, $scope = 'default', $scopeId = 0) {

        $configModel = Mage::getModel('core/config');

        try {
            $configModel->saveConfig($path, $value, $scope, $scopeId);
            return true;
        } catch (Mage_Exception $e) {
            Mage::log($e->getMessage());
            return false;
        }
    }

    public function resize($img_name, $path, $width, $height)
    {
//        if (!file_exists(Mage::getBaseDir('media').DS."catalog".DS."category".DS."resized")) {
//            mkdir(Mage::getBaseDir('media').DS."catalog".DS."category".DS."resized",0777);
//        };

//        $imageName = substr(strrchr($imageUrl,"/"),1);

//        $img_name_arr = explode('_', $img_name);
//        $img_name = $img_name_arr[2];

        $img_name_arr = explode('.', $img_name);
        $img_ext = $img_name_arr[1];


        $imageName = $width . '_' . $height . '_logo.' . $img_ext;
        $imageResized = $path . $imageName;

        $dirImg = $path . $img_name;
//        $imageResized = Mage::getBaseDir('media').DS."catalog".DS."category".DS."resized".DS.$imageName;
//
//        $dirImg = Mage::getBaseDir().str_replace("/",DS,strstr($imageUrl,'/media'));

//        if (!file_exists($imageResized) && file_exists($dirImg)) {
//            $imageObj = new Varien_Image($dirImg);
//            $imageObj->constrainOnly(false);
//            $imageObj->keepAspectRatio(false);
//            $imageObj->keepFrame(false);
//            // $imageObj->keepTransparency(true);
//            //$imageObj->backgroundColor($this->getBackgroundColor());
//            $imageObj->resize($width, $height);
//            $imageObj->save($imageResized);
//        }

        if (file_exists($dirImg)) {
            $imageObj = new Varien_Image($dirImg);
            $imageObj->constrainOnly(TRUE);
            $imageObj->keepAspectRatio(TRUE);
            $imageObj->keepFrame(FALSE);
            $imageObj->keepTransparency(false);
            //$imageObj->backgroundColor($imageObj->getBackgroundColor());
            $imageObj->resize($width, $height);
            $imageObj->save($imageResized);
        }



//        return $imageResized;
        return $imageName;
    }

    public function getBackgroundColor()
    {
        $rgb = Mage::getStoreConfig('easycatalogimg/general/background');
        $rgb = explode(',', $rgb);
        foreach ($rgb as $i => $color) {
            $rgb[$i] = (int) $color;
        }
        return $rgb;
    }

    public function strip_special_chars($string){
     $string = preg_replace('/([^.a-z0-9]+)/i', '_', $string);
     return $string;
    }

}

