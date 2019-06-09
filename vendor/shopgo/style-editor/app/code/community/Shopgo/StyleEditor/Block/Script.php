<?php

class Shopgo_StyleEditor_Block_Script extends Mage_Core_Block_Template
{

    public function getVars()
    {
        $variables = array(
            'base_url' => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB),
            'actions' => array(
                'upload' => Mage::helper('adminhtml')->getUrl('adminhtml/styleeditor/upload'),
                'save' => Mage::helper('adminhtml')->getUrl('adminhtml/styleeditor/save'),
            ),
            'theme' => Mage::getBaseUrl('skin') . 'frontend' . DS . 'shopgo' . DS . Mage::getStoreConfig('design/theme/default', 1) . DS,
            'theme_custom' => Mage::getBaseUrl('skin') . 'frontend' . DS . 'shopgo' . DS . Mage::getStoreConfig('design/theme/default', 1) . '_custom' . DS,
        );

        return $variables;
    }

}