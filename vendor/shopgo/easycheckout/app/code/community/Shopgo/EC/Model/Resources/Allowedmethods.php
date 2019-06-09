<?php
class Shopgo_EC_Model_Resources_Allowedmethods
{
    public function toOptionArray()
    {
        return Mage::getModel('adminhtml/system_config_source_shipping_allmethods')->toOptionArray(true);
    }
}
