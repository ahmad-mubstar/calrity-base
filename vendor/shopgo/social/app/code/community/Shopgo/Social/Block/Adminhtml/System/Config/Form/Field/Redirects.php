<?php

abstract class Shopgo_Social_Block_Adminhtml_System_Config_Form_Field_Redirects
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function getAuthProvider()
    {
        return '';
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        //
        $storeId = 1;
        return sprintf(
            '<pre>%ssocial/%s/connect/</pre>',
            Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK),
            $this->getAuthProvider()
        );
    }

}