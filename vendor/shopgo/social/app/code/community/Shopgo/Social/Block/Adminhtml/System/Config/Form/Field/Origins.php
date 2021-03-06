<?php

abstract class Shopgo_Social_Block_Adminhtml_System_Config_Form_Field_Origins
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $origins = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);

        return sprintf('<pre>%s</pre>', rtrim($origins, '/'));
    }

}