<?php
class Shopgo_Bannerslider_Block_Adminhtml_Bannerslider_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('bannerslider_form', array('legend'=>Mage::helper('bannerslider')->__('General information')));
      $data = null;

      if ( Mage::registry('bannerslider_data') ) {
          $data = Mage::registry('bannerslider_data')->getData();
      }

      $fieldset->addField('identifier', 'text', array(
          'label'     => Mage::helper('bannerslider')->__('Identifier'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'identifier'
      ));

      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('bannerslider')->__('Title'),
          'name'      => 'title'
      ));

      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('bannerslider')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('bannerslider')->__('Enabled'),
              ),
              array(
                  'value'     => 2,
                  'label'     => Mage::helper('bannerslider')->__('Disabled'),
              ),
          )
      ));

      if (!Mage::app()->isSingleStoreMode()) {
          $fieldset->addField('store_id', 'multiselect', array(
              'name'      => 'stores[]',
              'label'     => Mage::helper('cms')->__('Store View'),
              'title'     => Mage::helper('cms')->__('Store View'),
              'required'  => true,
              'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true)
          ));
      } else {
          $fieldset->addField('store_id', 'hidden', array(
              'name'      => 'stores[]',
              'value'     => Mage::app()->getStore(true)->getId()
          ));
          $model->setStoreId(Mage::app()->getStore(true)->getId());
      }

      $image = '';
      if (isset($data['image']) && !empty($data['image'])) {
          $image = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $data['image'];
      }

      $fieldset->addField('image', 'image', array(
          'label'     => Mage::helper('bannerslider')->__('Image'),
          'value'     => $image,
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'image'
      ));

      $fieldset->addField('link', 'text', array(
          'label'     => Mage::helper('bannerslider')->__('Link'),
          'name'      => 'link'
      ));

      $fieldset->addField('link_type', 'select', array(
          'label'     => Mage::helper('bannerslider')->__('Link Type'),
          'name'      => 'link_type',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('bannerslider')->__('Internal'),
              ),
              array(
                  'value'     => 2,
                  'label'     => Mage::helper('bannerslider')->__('External'),
              ),
          )
      ));

      $fieldset->addField('link_target', 'select', array(
          'label'     => Mage::helper('bannerslider')->__('Link Target'),
          'name'      => 'link_target',
          'values'    => array(
              array(
                  'value'     => '_self',
                  'label'     => Mage::helper('bannerslider')->__('Self'),
              ),
              array(
                  'value'     => '_blank',
                  'label'     => Mage::helper('bannerslider')->__('Blank'),
              ),
          )
      ));

      $checked = isset($data['add_text']) && $data['add_text'] ? 1 : 0;
      $fieldset->addField('add_text', 'checkbox', array(
          'label'     => Mage::helper('bannerslider')->__('Add Text'),
          'checked'   => $checked,
          'onclick'   => 'this.value = this.checked ? 1 : 0;',
          //'value'     => $checked,
          'name'      => 'add_text'
      ));

      $fieldset->addField('description', 'textarea', array(
          'label'     => Mage::helper('bannerslider')->__('Description'),
          'name'      => 'description'
      ));

      $fieldset->addField('sort_order', 'text', array(
          'label'     => Mage::helper('bannerslider')->__('Sort Order'),
          'name'      => 'sort_order'
      ));

      if ( Mage::getSingleton('adminhtml/session')->getBannerSliderData() ) {
          $data = Mage::getSingleton('adminhtml/session')->getBannerSliderData();
          Mage::getSingleton('adminhtml/session')->setBannerSliderData(null);
      } elseif ( Mage::registry('bannerslider_data') ) {
          $data = Mage::registry('bannerslider_data')->getData();
      }
      $data['store_id'] = explode(',', $data['stores']);
      $form->setValues($data);

      return parent::_prepareForm();
  }
}
