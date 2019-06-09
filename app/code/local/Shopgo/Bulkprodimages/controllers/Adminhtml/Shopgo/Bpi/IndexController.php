<?php

class Shopgo_Bulkprodimages_Adminhtml_Shopgo_Bpi_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle('ShopGo / Bulk Products Images Import');
        $this->_setActiveMenu('bulkprodimages_menu/first_page');
        $this->renderLayout();
    }

    public function importAction()
    {
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

        $request = $this->getRequest()->getPost();
        $session = $this->_getSession();
        $redirect = Mage::helper('adminhtml')->getUrl('adminhtml/shopgo_bpi_index');
        $logFile = 'bpi.log';
        $logFilePath = Mage::getBaseDir('var') . DS . 'log' . DS . $logFile;
        $msg = '';

        if (file_exists($logFilePath)) {
            unlink($logFilePath);
        }

        $msg = $this->__("[Images import process (START)] >>>");
        Mage::log($msg, null, $logFile);

        if(isset($_FILES['csv_file']['name']) && $_FILES['csv_file']['name'] != '') {
            $basePath = Mage::getBaseDir('media') . DS . 'shopgo' . DS . 'bulkprodimgs_data';
            $csvPath = $basePath . DS . 'csv';
            $imagesPath = $basePath . DS . 'images';
            $extendedMsg = '';
            $processDone = false;
            $success = ' successfully';

            if (isset($request['images_archive']) && !empty($request['images_archive'])) {
                $zipFile = $imagesPath . DS . $request['images_archive'] . '.zip';
                $zip = new ZipArchive;
                $res = $zip->open($zipFile);
                if ($res === true) {
                    $zip->extractTo($imagesPath);
                    $zip->close();
                } else {
                    $msg = $this->__("Couldn't unzip images.");
                    Mage::log($msg, null, $logFile);
                    $session->addError($msg);
                    header("Location: {$redirect}");
                    exit;
                }
            }

            try {
                $csv      = new Varien_File_Csv();
                $uploader = new Varien_File_Uploader('csv_file');
                $uploader->setAllowedExtensions(array('csv'));
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);

                $csvName = $_FILES['csv_file']['name'];
                $fullCsvPath = $csvPath . DS . $csvName;
                $uploader->save($csvPath, $csvName);

                $csvData = $csv->getData($fullCsvPath);

                for ($i = 1; $i < count($csvData); $i++) {
                    $product = Mage::getModel('catalog/product');
                    $csvRow = $i + 1;
                    if (empty($csvData[$i][0])) {
                        $msg = $this->__("Product SKU was empty. (Check row {$csvRow})");
                        Mage::log($msg, null, $logFile);
                        $session->addError($msg);
                        $success = '';
                        continue;
                    }
                    $productId = $product->getIdBySku($csvData[$i][0]);
                    if ($productId) {
                        $product->load($productId);
                    } else {
                        $msg = $this->__("Product \"{$csvData[$i][0]}\" wasn't found. (Check row {$csvRow})");
                        Mage::log($msg, null, $logFile);
                        $session->addError($msg);
                        $success = '';
                        continue;
                    }

                    $validAttr = array('image', 'small_image', 'thumbnail');
                    $attr = array();
                    $move = false;
                    $exclude = false;

                    $img = $csvData[$i][1];
                    $fullImgPath = $imagesPath . $img;

                    $extendedMsg = ' ' . $this->__('Image') . ': ' . $img
                                 . ' ' . $this->__('Row') . ': ' . $csvRow;

                    if (empty($img)) {
                        $msg = $this->__("Empty image entry at row {$csvRow}.");
                        $session->addError($msg);
                        $success = '';
                        continue;
                    }

                    if (!file_exists($fullImgPath)) {
                        $msg = $this->__("Image \"{$img}\" wasn't added, because it doesn't exist in images import directory. (Check row {$csvRow})");
                        Mage::log($msg, null, $logFile);
                        $session->addError($msg);
                        $success = '';
                        continue;
                    }

                    if (isset($csvData[$i][2])) {
                        $attr = array_map('trim', explode('-', $csvData[$i][2]));
                        if (!empty($attr[0])) {
                            foreach ($attr as $ai => $at) {
                                if (!in_array($at, $validAttr)) {
                                    $msg = $this->__("Unknown attribute entry at row {$csvRow}.");
                                    Mage::log($msg, null, $logFile);
                                    $session->addError($msg);
                                    $success = '';
                                    unset($attr[$ai]);
                                }
                            }
                        }
                    }

                    if (isset($csvData[$i][3]) && $csvData[$i][3]) {
                        $exclude = true;
                    }

                    $imagesToRemove = array();

                    foreach ($product->getMediaGalleryImages() as $mgImg) {
                        $mgImgFileName = substr($mgImg->getFile(), strrpos($mgImg->getFile(), '/') + 1, strlen($mgImg->getFile()));
                        $mgImgFileName = substr($mgImgFileName, 0, strrpos($mgImgFileName, '_'));
                        //if (substr($img, 1, strrpos($img, '.') - 1) == $mgImgFileName) {
                        if (md5_file($fullImgPath) == md5_file($mgImg->getPath())) {
                            $imagesToRemove[] = $mgImg;
                            //Mage::getModel("catalog/product_attribute_media_api")->remove($product->getId(), $mgImg->getFile());

                            //$_store = (Mage::getSingleton('api/session')->hasData('store_id')
                                           //? Mage::getSingleton('api/session')->getData('store_id') : 0);
                            //$_storeId = Mage::app()->getStore($_store)->getId();
                            //$mgProduct = Mage::helper('catalog/product')->getProduct($product->getId(), $_storeId, null);

                            //if ($mgProduct->getId()) {
                                //$attributes = $mgProduct->getTypeInstance(true)
                                    //->getSetAttributes($mgProduct);

                                //if (isset($attributes[Mage_Catalog_Model_Product_Attribute_Media_Api::ATTRIBUTE_CODE])) {
                                    //$_gallery = $attributes[Mage_Catalog_Model_Product_Attribute_Media_Api::ATTRIBUTE_CODE];

                                    //if ($_gallery->getBackend()->getImage($mgProduct, $mgImg->getFile())) {
                                        //$_gallery->getBackend()->removeImage($mgProduct, $mgImg->getFile());
                                    //}
                                //}
                            //}
                        }
                    }

                    $product->addImageToMediaGallery($fullImgPath, $attr, $move, $exclude);

                    //$product->save();
                    $product->getResource()->save($product);

                    //if (empty($imagesToRemove)) {
                        //$product->getResource()->save($product);
                    //}

                    foreach ($imagesToRemove as $i2r) {
                        Mage::getModel("catalog/product_attribute_media_api")->remove($product->getId(), $i2r->getFile());
                    }

                    $extendedMsg = '';

                    $msg = $this->__("Image \"{$img}\" was added for product \"{$csvData[$i][0]}\". (@ row {$csvRow})");
                    Mage::log($msg, null, $logFile);
                }

                $processDone = true;
                $extendedMsg = empty($success) && isset($request['purge_images']) && $request['purge_images'] ?
                               ' ' . $this->__('Images purging was dropped due to some errors.') : '';
                $msg = $this->__("Import process is done{$success}.{$extendedMsg}");
                Mage::log($msg, null, $logFile);
                $session->addSuccess($msg);
            } catch (Exception $e) {
                $msg = $e->getMessage() . $extendedMsg;
                Mage::log($msg, null, $logFile);
                $session->addError($msg);
            }

            unlink($fullCsvPath);

            if (isset($request['purge_images']) &&
                $request['purge_images'] &&
                $processDone && !empty($success)) {
                $this->emptyDir($imagesPath);
                $msg = $this->__("Images were purged.");
                Mage::log($msg, null, $logFile);
            }
        } else {
            $msg = $this->__("Couldn't import. No CSV file was provided.");
            Mage::log($msg, null, $logFile);
            $session->addError($msg);
        }

        $msg = $this->__("<<< [Images import process (END)]");
        Mage::log($msg, null, $logFile);

        header("Location: {$redirect}");
        exit;
    }

    public function removeAction()
    {
        $request = $this->getRequest()->getPost();
        $session = $this->_getSession();
        $redirect = Mage::helper('adminhtml')->getUrl('adminhtml/shopgo_bpi_index');
        $logFile = 'bpi.log';
        $logFilePath = Mage::getBaseDir('var') . DS . 'log' . DS . $logFile;
        $msg = '';

        if (file_exists($logFilePath)) {
            unlink($logFilePath);
        }

        $msg = $this->__("[Media gallery images removal process (START)] >>>");
        Mage::log($msg, null, $logFile);

        if(isset($_FILES['csv_file']['name']) && $_FILES['csv_file']['name'] != '') {
            $basePath = Mage::getBaseDir('media') . DS . 'shopgo' . DS . 'bulkprodimgs_data';
            $csvPath = $basePath . DS . 'csv';
            $success = ' successfully';

            try {
                $csv      = new Varien_File_Csv();
                $uploader = new Varien_File_Uploader('csv_file');
                $uploader->setAllowedExtensions(array('csv'));
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);

                $csvName = $_FILES['csv_file']['name'];
                $fullCsvPath = $csvPath . DS . $csvName;
                $uploader->save($csvPath, $csvName);

                $csvData = $csv->getData($fullCsvPath);

                $product = Mage::getModel('catalog/product');
                $mediaApi = Mage::getModel("catalog/product_attribute_media_api");

                $processedProds = array();

                for ($i = 1; $i < count($csvData); $i++) {
                    $csvRow = $i + 1;
                    if (empty($csvData[$i][0])) {
                        $msg = $this->__("Product SKU was empty. (Check row {$csvRow})");
                        Mage::log($msg, null, $logFile);
                        $session->addError($msg);
                        $success = '';
                        continue;
                    }
                    if (in_array($csvData[$i][0], $processedProds)) {
                        $msg = $this->__("Product \"{$csvData[$i][0]}\" is already processed. (Check row {$csvRow})");
                        Mage::log($msg, null, $logFile);
                        $session->addError($msg);
                        $success = '';
                        continue;
                    }
                    $productId = $product->getIdBySku($csvData[$i][0]);
                    if ($productId) {
                        $product->load($productId);
                    } else {
                        $msg = $this->__("Product \"{$csvData[$i][0]}\" wasn't found. (Check row {$csvRow})");
                        Mage::log($msg, null, $logFile);
                        $session->addError($msg);
                        $success = '';
                        continue;
                    }

                    $mgItems = $mediaApi->items($productId);
                    foreach($mgItems as $item) {
                        $mediaApi->remove($productId, $item['file']);
                    }

                    /*$entityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
                    $gallery = Mage::getModel('catalog/resource_eav_attribute')->loadByCode($entityTypeId, 'media_gallery');
                    $galleryImages = $product->getMediaGalleryImages();
                    foreach($galleryImages as $image) {
                        $gallery->getBackend()->removeImage($product, $image->getFile());
                    }

                    $product->save();*/

                    $processedProds[] = $csvData[$i][0];

                    $msg = $this->__("Product \"{$csvData[$i][0]}\" media gallery images were deleted. (@ row {$csvRow})");
                    Mage::log($msg, null, $logFile);
                }

                $msg = $this->__("Media gallery images removal process is done{$success}.");
                Mage::log($msg, null, $logFile);
                $session->addSuccess($msg);
            } catch (Exception $e) {
                $msg = $e->getMessage();
                Mage::log($msg, null, $logFile);
                $session->addError($msg);
            }

            unlink($fullCsvPath);
        } else {
            $msg = $this->__("Couldn't remove media gallery images. No CSV file was provided.");
            Mage::log($msg, null, $logFile);
            $session->addError($msg);
        }

        $msg = $this->__("<<< [Media gallery images removal process (END)]");
        Mage::log($msg, null, $logFile);

        header("Location: {$redirect}");
        exit;
    }

    public function purgeAction()
    {
        $session = $this->_getSession();
        $imagesPath = Mage::getBaseDir('media') . DS . 'shopgo' . DS . 'bulkprodimgs_data' . DS . 'images';
        $redirect = Mage::helper('adminhtml')->getUrl('adminhtml/shopgo_bpi_index');
        $msg = '';

        $this->emptyDir($imagesPath);
        $msg = $this->__("Import images were purged successfully.");
        $session->addSuccess($msg);

        header("Location: {$redirect}");
        exit;
    }

    private function emptyDir($path)
    {
        $debugStr = '';
        $debugStr .= "Deleting Contents Of: $path<br /><br />";
        if ($handle = opendir($path)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    if(is_file($path . "/" . $file)) {
                        if(unlink($path . "/" . $file)) {
                            $debugStr .= "Deleted File: ".$file."<br />";
                        }
                    } else {
                        if($handle2 = opendir($path . "/" . $file)) {
                            while (false !== ($file2 = readdir($handle2))) {
                                if ($file2 != "." && $file2 != "..") {
                                    if(unlink($path . "/" . $file . "/" . $file2)) {
                                        $debugStr .= "Deleted File: $file/$file2<br />";
                                    }
                                }
                            }
                        }
                        if(rmdir($path . "/" . $file)) {
                            $debugStr .= "Directory: " . $file . "<br />";
                        }
                    }
                }
            }
        }
        return $debugStr;
    }

    protected function _isAllowed()
    {
        return true;
    }

}
