<?php

class Shopgo_StyleEditor_Block_Adminhtml_System_Config_Styleeditor_Banner extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /*
     * Set template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('shopgo/styleeditor/system/config/styleeditor/banner.phtml');
    }

    /**
     * Remove scope label
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Return element html
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }

    /**
     * Generate update button html
     *
     * @return string
     */
//    public function getButtonHtml()
//    {
//        /** @var $button Mage_Adminhtml_Block_Widget_Button */
//        $button = $this->getLayout()->createBlock('adminhtml/widget_button');
//        $button->setData(array(
//            'id'        => 'styleeditor_button',
//            'label'     => $this->helper('adminhtml')->__('Open StyleEditor'),
//            'onclick'   => "window.open('" . Mage::helper('adminhtml')->getUrl('adminhtml/styleeditor/index') . "')"
//        ));
//
//        return $button->toHtml();
//    }
}