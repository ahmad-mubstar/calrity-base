<?php

class Faturah_Pay_Adminhtml_Model_System_Config_Source_Languageoptions {
    /*     * */

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array('value' => 'en', 'label' => Mage::helper('adminhtml')->__('en')),
            array('value' => 'ar', 'label' => Mage::helper('adminhtml')->__('ar')),
        );
    }

}
