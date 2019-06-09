<?php
class Shopgo_Bannerslider_Model_Mysql4_Bannerslider extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('bannerslider/bannerslider', 'bs_id');
    }
}
