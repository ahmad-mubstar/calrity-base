<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Grid
 *
 * @author eadesignpc
 */
class EaDesign_PdfGenerator_Block_Adminhtml_Block_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{

    protected function _prepareMassaction()
    {
        
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('order_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);


        $templateCollection = Mage::getModel('eadesign/pdfgenerator')->getCollection();
        $templateCollection->addFieldToSelect('*')
                ->addFieldToFilter('pdft_is_active', 1);

        $templates[0] = Mage::helper('sales')->__('Select');
        foreach ($templateCollection as $collection)
        {
            $templates[$collection->getData('pdftemplate_id')] = $collection->getData('pdftemplate_name');
        }

        $this->getMassactionBlock()->addItem('pdforders_orderea', array(
            'label' => Mage::helper('sales')->__('EaDesgin PDF Invoices'),
            'url' => $this->getUrl('adminhtml/pdfgeneratorpdf/orderpdfmass', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'template',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('catalog')->__('Template to print'),
                    'values' => $templates,
                )
            )
        ));
        
        parent::_prepareMassaction();

        return $this;
    }

}
