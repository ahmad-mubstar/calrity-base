<?php
class Shopgo_Social_Model_Mysql4_Simplesharer_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('shopgo_social/shopgo_social');
    }
}
