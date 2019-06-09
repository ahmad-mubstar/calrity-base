<?php
class Shopgo_Bannerslider_Adminhtml_Shopgo_BannersliderController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction() {
        $this->loadLayout()
            // isalem 1/12/2017
            //->_setActiveMenu('bannerslider/items')
            ->_setActiveMenu('cms/bannerslider')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Banners Manager'), Mage::helper('adminhtml')->__('Banner Manager'));

        return $this;
    }

    public function indexAction() {
        $this->_title($this->__('Banner Slider'))
            ->_title($this->__('Manage Banners'));
        $this->_initAction()
            ->renderLayout();
    }

    public function editAction() {
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('bannerslider/bannerslider')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('bannerslider_data', $model);

            $this->_title($this->__('Banner Slider'))
                ->_title($this->__('Manage Banners'));
            if ($model->getId()){
                $this->_title($model->getIdentifier());
            }else{
                $this->_title($this->__('New Banner'));
            }

            $this->loadLayout();
            $this->_setActiveMenu('bannerslider/items');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('bannerslider/adminhtml_bannerslider_edit'))
                ->_addLeft($this->getLayout()->createBlock('bannerslider/adminhtml_bannerslider_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('bannerslider')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $bsCollection = Mage::getModel('bannerslider/bannerslider')->getCollection()
                              ->addFieldToFilter('bs_id', array('neq' => $this->getRequest()->getParam('id')))
                              ->addFieldToFilter('identifier', $data['identifier']);

            if (count($bsCollection) > 0) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('There is already another image with the same "Identifier"'));
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }

            $model = Mage::getModel('bannerslider/bannerslider');
            $bsImage = $model->getImage();
            if ($bsImage) {
                $model->setImage($bsImage[1]);
            }
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));

            try {
                if (isset($data['image']['delete']) && $data['image']['delete']) {
                    Mage::throwException($this->__('"Image" cannot be deleted'));
                }

                if (isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])) {
                    $image = $_FILES['image']['name'];
                    $uploader = new Varien_File_Uploader('image');
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(false);
                    $path = Mage::getBaseDir('media') . DS . 'shopgo' . DS . 'bannerslider_images';
                    $uploader->save($path, $image);

                    $model->setImage('shopgo/bannerslider_images/' . $uploader->getUploadedFileName());
                } else {
                    if (!$model->getImage()) {
                        if (!isset($_FILES['image']['name'])) {
                            Mage::throwException($this->__('"Image" is not set'));
                        } elseif (empty($_FILES['image']['name'])) {
                            Mage::throwException($this->__('"Image" is not set'));
                        }
                    } else {
                        $model->setImage($bsImage[1]);
                    }
                }

                if (!isset($data['add_text'])) {
                    $model->setAddText(0);
                }

                $model->setStores(implode(',', $data['stores']));

                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('bannerslider')->__('Item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('bannerslider')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $model = Mage::getModel('bannerslider/bannerslider');

                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $productsliderIds = $this->getRequest()->getParam('bannerslider');
        if(!is_array($productsliderIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($productsliderIds as $productsliderId) {
                    $productslider = Mage::getModel('bannerslider/bannerslider')->load($productsliderId);
                    $productslider->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($productsliderIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        $productsliderIds = $this->getRequest()->getParam('bannerslider');
        if(!is_array($productsliderIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($productsliderIds as $productsliderId) {
                    $productslider = Mage::getSingleton('bannerslider/bannerslider')
                        ->load($productsliderId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($productsliderIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction()
    {
        $fileName   = 'bannerslider.csv';
        $content    = $this->getLayout()->createBlock('bannerslider/adminhtml_bannerslider_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'bannerslider.xml';
        $content    = $this->getLayout()->createBlock('bannerslider/adminhtml_bannerslider_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }

    protected function _isAllowed() {
        // isalem 6/12/2017
        $var = $this->getRequest()->getActionName();
        if ($var == 'index') {
            //return Mage::getSingleton('admin/session')->isAllowed('admin/shopgo/bannerslider/manage');
            return Mage::getSingleton('admin/session')->isAllowed('admin/cms/cms/bannerslider/manage');
        } else {
            //return Mage::getSingleton('admin/session')->isAllowed('admin/shopgo/bannerslider/config');
            return Mage::getSingleton('admin/session')->isAllowed('admin/cms/cms/bannerslider/config');
        }
    }
}
