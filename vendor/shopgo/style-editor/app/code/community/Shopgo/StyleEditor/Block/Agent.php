<?php

class Shopgo_StyleEditor_Block_Agent extends Mage_Core_Block_Template
{
    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $routeName = $this->getRequest()->getRouteName();
        $referer_url = $this->getRequest()->getServer('HTTP_REFERER');

        $styleeditor_enabled = (boolean) $this->getRequest()->getParam('styleeditor_enabled');

        $cookie = Mage::getSingleton('core/cookie');

        if(strstr($referer_url, 'styleeditor_admin')) {
            //
            if(!$cookie->get('styleeditor_enabled')) {
                // set for 5 min
                $cookie->set('styleeditor_enabled', true, 60 * 5);
            }
            return parent::_toHtml();
        }

        //
        if($cookie->get('styleeditor_enabled'))
            return parent::_toHtml();
        
        return;
    }
}