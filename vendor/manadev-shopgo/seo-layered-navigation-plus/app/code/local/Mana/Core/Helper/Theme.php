<?php

class Mana_Core_Helper_Theme extends Mage_Core_Helper_Abstract
{
    public function setSettings($theme = '')
    {
        if (!$theme) {
            $theme = Mage::getSingleton('core/design_package')->getTheme('frontend');
        }

        $settings = $this->_getThemeSettings($theme);

        foreach ($settings as $path => $config) {
            $this->_saveConfig($config, $path);
        }
    }

    private function _saveConfig($config, $path = '')
    {
        foreach ($config as $cfg) {
            Mage::getConfig()->saveConfig($path . $cfg['key'], $cfg['value']);
        }
    }

    private function _getThemeSettings($theme)
    {
        $settings = array(
            'techshop' => array(

                'mana_filters/colors/' => array(
                    array(
                        'key'   => 'image_width',
                        'value' => 40
                    ),
                    array(
                        'key'   => 'image_height',
                        'value' => 40
                    ),
                    array(
                        'key'   => 'image_border_radius',
                        'value' => 3
                    ),
                    array(
                        'key'   => 'state_width',
                        'value' => 20
                    ),
                    array(
                        'key'   => 'state_height',
                        'value' => 20
                    ),
                    array(
                        'key'   => 'state_border_radius',
                        'value' => 3
                    )
                ),

                'mana_filters/' => array(
                    array(
                        'key'   => 'display/attribute',
                        'value' => 'dropdown'
                    ),
                    array(
                        'key'   => 'positioning/show_state_left',
                        'value' => ''
                    ),
                    array(
                        'key'   => 'mobile/max_width',
                        'value' => 450
                    ),
                    array(
                        'key'  => 'slider/style',
                        'value' => 'style4'
                    )
                ),

                'mana/ajax/' => array(
                    array(
                        'key'  => 'mode',
                        'value' => 1
                    )
                )
            )
        );

        return $settings[$theme];
    }
}