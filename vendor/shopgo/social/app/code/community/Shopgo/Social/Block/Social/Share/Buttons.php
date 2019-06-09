<?php

class Shopgo_Social_Block_Social_Share_Buttons extends Mage_Core_Block_Template 
{
    //  
    public function _toHtml() {
        //
        if($this->helper('shopgo_social')->getConfig('share_btns/enabled')) {
            return parent::_toHtml();
        }
    }
}