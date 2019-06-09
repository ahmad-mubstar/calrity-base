<?php

class TM_EasyTabs_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function setDefaultThemeConfigurations($theme = '')
    {
        if (empty($theme)) {
            $theme = Mage::getDesign()->getTheme('template');
        }

        // Prepare theme configurations
        switch ($theme) {
            case 'homeappliances':
                $configurations = array(
                    'descriptiontabbed'       => 1,
                    'additionaltabbed'        => 1,
                    'upsellproductstabbed'    => 0,
                    'relatedtabbed'           => 0,
                    'tagstabbed'              => 0,
                    'reviewtabbed'            => 1
                );
                break;
        }

        if (!empty($configurations)) {
            $configurations['enabled'] = 1;
            $this->_saveConfigurations($configurations);
        }
    }

    private function _saveConfigurations($configurations)
    {
        foreach ($configurations as $key => $val) {
            Mage::getConfig()->saveConfig('easy_tabs/general/' . $key, $val);
        }
    }
}
