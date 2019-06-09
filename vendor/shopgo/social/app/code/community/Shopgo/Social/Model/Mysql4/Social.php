<?php
class Shopgo_Social_Model_Mysql4_Themeoptions extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('shopgo_social/shopgo_social', 'id');
    }
}
