<?php

class Onecard_Onecardpay_Adminhtml_Model_System_Config_Source_Currencyoptions {
	/***/
	
	/**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'usd', 'label'=>Mage::helper('adminhtml')->__('USD')),
            array('value' => 'aed', 'label'=>Mage::helper('adminhtml')->__('AED')),
            array('value' => 'eur', 'label'=>Mage::helper('adminhtml')->__('EUR')),
            array('value' => 'egp', 'label'=>Mage::helper('adminhtml')->__('EGP')),
            array('value' => 'sar', 'label'=>Mage::helper('adminhtml')->__('SAR')),
            array('value' => 'kwd', 'label'=>Mage::helper('adminhtml')->__('KWD')),
            array('value' => 'syp', 'label'=>Mage::helper('adminhtml')->__('SYP')),
        );
    }
	
	}
