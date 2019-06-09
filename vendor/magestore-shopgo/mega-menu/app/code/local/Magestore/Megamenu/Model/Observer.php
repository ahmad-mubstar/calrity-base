<?php

class Magestore_Megamenu_Model_Observer {

    // give event when save product
    public function saveProduct($observer) {
        if (Mage::getStoreConfig('megamenu/general/cache_auto_flush')) {
            Mage::helper('megamenu')->saveCacheHtml();
            return;
        }

        $product = $observer->getEvent()->getProduct();
        if ($product->getId()) {
             Mage::getModel('core/config')->saveConfig('megamenu/general/reindex',1);
        }
        Mage::app()->getCacheInstance()->cleanType('config');
        return;
    }

    // give event when delete product
    public function deleteProduct() {
        if (Mage::getStoreConfig('megamenu/general/cache_auto_flush')) {
            Mage::helper('megamenu')->saveCacheHtml();
            return;
        }

        $products = Mage::app()->getRequest()->getParams('product');
        if ($products) {
            Mage::getModel('core/config')->saveConfig('megamenu/general/reindex',1);
        } else {
            $product_id = Mage::app()->getRequest()->getParams('id');
            if ($product_id) {
                 Mage::getModel('core/config')->saveConfig('megamenu/general/reindex',1);
            }
        }
        Mage::app()->getCacheInstance()->cleanType('config');
        return;
    }

    //give event when save category

    public function saveCategory($observer) {
        if (Mage::getStoreConfig('megamenu/general/cache_auto_flush')) {
            Mage::helper('megamenu')->saveCacheHtml();
            return;
        }

        $category = $observer->getEvent()->getCategory();
        if ($category->getId()) {
             Mage::getModel('core/config')->saveConfig('megamenu/general/reindex',1);
             Mage::app()->getCacheInstance()->cleanType('config');
        }
        return;
    }

    //give event when delete category
    public function deleteCategory($observer) {
        if (Mage::getStoreConfig('megamenu/general/cache_auto_flush')) {
            Mage::helper('megamenu')->saveCacheHtml();
            return;
        }

        $category = $observer->getEvent()->getCategory();
        if ($category->getId()) {
            Mage::getModel('core/config')->saveConfig('megamenu/general/reindex',1);
            Mage::app()->getCacheInstance()->cleanType('config');
        }
        return;
    }

    public function megamenu_item_save_after($observer){
        Mage::helper('megamenu')->saveCacheHtml();
    }

    /**
     * event after save config in admin
     */
    public function admin_system_config_changed_section_megamenu($observer){
        Mage::helper('megamenu')->saveCacheHtml();
    }

}

