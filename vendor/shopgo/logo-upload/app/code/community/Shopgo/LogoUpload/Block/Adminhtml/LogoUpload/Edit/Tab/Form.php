<?php

class Shopgo_LogoUpload_Block_Adminhtml_LogoUpload_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('pslider_form', array('legend' => Mage::helper('pslider')->__('General information')));

        $fieldset->addField('fileinputname', 'file', array(
            'label' => Mage::helper('logoupload')->__('File label'),
            'required' => false,
            'name' => 'fileinputname',
        ));


        return parent::_prepareForm();
    }

}