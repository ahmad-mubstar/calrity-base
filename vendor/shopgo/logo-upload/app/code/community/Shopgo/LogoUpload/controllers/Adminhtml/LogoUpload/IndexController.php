<?php

class Shopgo_LogoUpload_Adminhtml_LogoUpload_IndexController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        $stores = Mage::app()->getStores();
        /* foreach ($stores as $store) {
          var_dump($store->getId());
          }
          return; */
        foreach ($stores as $store) {
            $skin = Mage::getStoreConfig('design/theme/skin', $store->getId());
            if (is_null($skin) || empty($skin)) {
                $skin = Mage::getStoreConfig('design/theme/skin');
            }
            $package = Mage::getStoreConfig('design/package/name', $store->getId());
            if (is_null($package) || empty($package)) {
                $package = Mage::getStoreConfig('design/theme/package');
            }
            $path = Mage::getBaseDir() . DS . 'skin/frontend/' . $package . DS . $skin . DS . 'images' . DS;
        }



        $this->_title($this->__('System'))->_title($this->__('Configuration'));
        $current = $this->getRequest()->getParam('section');
        $website = $this->getRequest()->getParam('website');
        $store = $this->getRequest()->getParam('store');

        Mage::getSingleton('adminhtml/config_data')
                ->setSection($current)
                ->setWebsite($website)
                ->setStore($store);

        $this->loadLayout();

        $block = $this->getLayout()->createBlock(
                        'Mage_Core_Block_Template', 'logo_upload_block', array('template' => 'logoupload/options.phtml')
                )
                ->setCurrent($current)
                ->setWebsite($website)
                ->setStore($store);



        $this->getLayout()->getBlock('left')
                ->append($this->getLayout()->createBlock('adminhtml/system_config_switcher'));



        $this->getLayout()->getBlock('content')->append($block);

        $this->renderLayout();
    }

    public function saveAction() {

        $session = Mage::getSingleton('adminhtml/session');
        if (!$this->getRequest()->isPost())
            return;

        $website = $this->getRequest()->getParam('website');
        $store = $this->getRequest()->getParam('store');
        $storeId = '';
        $skin = '';
        $package = '';
        $path = '';

        if (isset($_FILES['logoImage']['name']) && $_FILES['logoImage']['name'] != '') {
            try {
                $fname = $_FILES['logoImage']['name']; //file name
                $fname = Mage::helper('logoupload/data')->strip_special_chars($fname);
                $uploader = new Varien_File_Uploader('logoImage'); //load class
                $uploader->setAllowedExtensions(array('gif', 'png', 'jpg', 'jpeg')); //Allowed extension for file
                $uploader->setAllowCreateFolders(true); //for creating the directory if not exists
                $uploader->setAllowRenameFiles(true); //if true, uploaded file's name will be changed, if file with the same name already exists directory.
                //$uploader->setFilesDispersion(false);
                if (empty($store)) {
                    $stores = Mage::app()->getStores();
                    foreach ($stores as $store) {
                        $skin = Mage::getStoreConfig('design/theme/skin', $store->getId());
                        if (is_null($skin) || empty($skin)) {
                            $skin = Mage::getStoreConfig('design/theme/skin');
                        }
                        $package = Mage::getStoreConfig('design/package/name', $store->getId());
                        if (is_null($package) || empty($package)) {
                            $package = Mage::getStoreConfig('design/theme/package');
                        }
                        $path = Mage::getBaseDir() . DS . 'skin/frontend/' . $package . DS . $skin . DS . 'images' . DS;
                        $uploader->save($path, $fname); //save the file on the specified path
                    }
                } else {
                    $storeId = Mage::getModel('core/store')->load($store)->getId();
                    $skin = Mage::getStoreConfig('design/theme/skin', $storeId);
                    if (is_null($skin) || empty($skin)) {
                        $skin = Mage::getStoreConfig('design/theme/skin');
                    }
                    $package = Mage::getStoreConfig('design/package/name', $storeId);
                    if (is_null($package) || empty($package)) {
                        $package = Mage::getStoreConfig('design/theme/package');
                    }
                    $path = Mage::getBaseDir() . DS . 'skin/frontend/' . $package . DS . $skin . DS . 'images' . DS;
                    $uploader->save($path, $fname); //save the file on the specified path
                }


                // resize
                $width = $this->getRequest()->getParam('width');
                $height = $this->getRequest()->getParam('height');
                if (!empty($width) || !empty($height)) {
                    $imagePath = $path;
                    $fname = Mage::helper('logoupload/data')->resize($fname, $imagePath, $width, $height);
                }
                if (empty($website) || empty($store) || empty($storeId)) {
                    Mage::helper('logoupload/data')->saveConfig('design/header/logo_src', 'images/' . $fname);
                } else {
                    Mage::helper('logoupload/data')->saveConfig('design/header/logo_src', 'images/' . $fname, 'stores', $storeId);
                }
                $session->addSuccess(Mage::helper('adminhtml')->__('The configuration has been saved.'));
                $this->_redirectReferer($this->getRequest()->getServer('HTTP_REFERER'));
                return;
            } catch (Exception $e) {
                echo 'Error Message: ' . $e->getMessage();
            }
        } elseif ($this->getRequest()->getParam('width') || $this->getRequest()->getParam('height')) {
            $width = $this->getRequest()->getParam('width');
            $height = $this->getRequest()->getParam('height');
            $fname = '';
            if (empty($website) || empty($store) || empty($storeId)) {
                $fname = str_replace('images/', '', Mage::getStoreConfig('design/header/logo_src'));
            } else {
                $fname = str_replace('images/', '', Mage::getStoreConfig('design/header/logo_src', $store));
            }
            $imagePath = $path;
            $fname = Mage::helper('logoupload/data')->resize($fname, $imagePath, $width, $height);
            if (empty($website) || empty($store) || empty($storeId)) {
                Mage::helper('logoupload/data')->saveConfig('design/header/logo_src', 'images/' . $fname);
            } else {
                Mage::helper('logoupload/data')->saveConfig('design/header/logo_src', 'images/' . $fname, 'stores', $storeId);
            }
            $session->addSuccess(Mage::helper('adminhtml')->__('The configuration has been saved.'));
            $this->_redirectReferer($this->getRequest()->getServer('HTTP_REFERER'));
            return;
        } else {
            $session->addError(Mage::helper('adminhtml')->__('no image.'));
            $this->_redirectReferer($this->getRequest()->getServer('HTTP_REFERER'));
            return;
        }
    }

}
