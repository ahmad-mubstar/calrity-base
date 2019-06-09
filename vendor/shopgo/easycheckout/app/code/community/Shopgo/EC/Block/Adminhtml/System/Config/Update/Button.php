<?php

class Shopgo_EC_Block_Adminhtml_System_Config_Update_Button extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /*
     * Set template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('shopgo/ec/system/config/update/button.phtml');
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
     * Return ajax url for update button
     *
     * @return string
     */
    public function getAjaxUpdateUrl()
    {
        return Mage::getSingleton('adminhtml/url')->getUrl('*/ec/update');
    }

    /**
     * Return ajax url for update button
     *
     * @return string
     */
    public function getAjaxCheckUpdateUrl()
    {
        return Mage::getSingleton('adminhtml/url')->getUrl('*/ec/checkUpdate');
    }

    /**
     * Generate update button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        /** @var $button Mage_Adminhtml_Block_Widget_Button */
        $button = $this->getLayout()->createBlock('adminhtml/widget_button');
        $button->setData(array(
            'id'        => 'update_button',
            'label'     => $this->helper('adminhtml')->__('Update now'),
            'onclick'   => 'javascript:updateEC(); return false;'
        ));

        return $button->toHtml();
    }
}
