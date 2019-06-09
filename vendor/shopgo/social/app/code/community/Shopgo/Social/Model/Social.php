<?php
class Shopgo_Social_Model_Themeoptions extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('shopgo_social/shopgo_social');
    }
}
;