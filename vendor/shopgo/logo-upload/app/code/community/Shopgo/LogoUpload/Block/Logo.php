<?php

class Shopgo_LogoUpload_Block_Logo extends Mage_Adminhtml_Model_System_Config_Backend_Image {

    /**
     * Getter for allowed extensions of uploaded files
     *
     * @return array
     */
    protected function _getAllowedExtensions() {
        return array('jpg', 'jpeg', 'gif', 'png');
    }

    protected function _beforeSave() {
        $value = $this->getValue();
        if ($_FILES['groups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value']) {

            $uploadDir = Mage::getBaseDir() . $this->_getUploadDir();

            try {
                $file = array();
                $tmpName = $_FILES['groups']['tmp_name'];
                $file['tmp_name'] = $tmpName[$this->getGroupId()]['fields'][$this->getField()]['value'];
                $name = $_FILES['groups']['name'];
                $file['name'] = $name[$this->getGroupId()]['fields'][$this->getField()]['value'];
                $uploader = new Mage_Core_Model_File_Uploader($file);
                $uploader->setAllowedExtensions($this->_getAllowedExtensions());
                $uploader->setAllowRenameFiles(TRUE);
                $uploader->setFilesDispersion(false);
                $uploader->addValidateCallback('size', $this, 'validateMaxSize');
                //here I added new name same as scopeId
                $ext = explode('.', $file['name']);
                $ext = array_pop($ext);
                $result = $uploader->save($uploadDir, $this->getScopeId() . '.' . $ext);
            } catch (Exception $e) {
                Mage::throwException($e->getMessage());
                return $this;
            }

            $filename = $result['file'];
            if ($filename) {
                if ($this->_addWhetherScopeInfo()) {
                    $filename = $this->_prependScopeInfo($filename);
                }
                $this->setValue($filename);
                //Mage::getConfig()->saveConfig('design/header/logo_src', $logo);
                $core_config = new Mage_Core_Model_Config();
                $core_config->saveConfig('design/header/logo_src', $filename, $this->getScope(), $this->getScopeId());
            }
        } else {
            if (is_array($value) && !empty($value['delete'])) {
                $this->setValue('');
            } else {
                $this->unsValue();
            }
        }

        return $this;
    }

    public function resizeImg($fileName, $width, $height = '') {
        $folderURL = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN);
        $imageURL = $folderURL . $fileName;

        $basePath = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_SKIN) . DS . $fileName;
        $newPath = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_SKIN) . DS . "resized" . DS . $fileName;
        //if width empty then return original size image's URL
        if ($width != '') {
            //if image has already resized then just return URL
            if (file_exists($basePath) && is_file($basePath) && !file_exists($newPath)) {
                $imageObj = new Varien_Image($basePath);
                $imageObj->constrainOnly(TRUE);
                $imageObj->keepAspectRatio(FALSE);
                $imageObj->keepFrame(FALSE);
                $imageObj->resize($width, $height);
                $imageObj->save($newPath);
            }
            $resizedURL = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . "resized" . DS . $fileName;
        } else {
            $resizedURL = $imageURL;
        }
        return $resizedURL;
    }

}
