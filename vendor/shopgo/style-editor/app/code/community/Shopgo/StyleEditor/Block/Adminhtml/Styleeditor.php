<?php

class Shopgo_StyleEditor_Block_Adminhtml_Styleeditor extends Mage_Adminhtml_Block_Abstract
{


    public function getThemeData()
    {
        $styleEditor = Mage::getModel('styleeditor/styleeditor');
        $themeData = $styleEditor->getThemeData();
        return $themeData;
    }

    public function draw_field($field)
    {
        $fonts = $this->getThemeData()->fonts;
        $colors = $this->getThemeData()->colors;
        $yesno = array (
            'yes' => 'Yes',
            'no'  => 'No'
        );
        $html = '';
        switch ($field['type']) {
            case 'color':
                $html .= '<div class="section"><div class="field color-field">';
                $html .= '<label for="' . $field['machine_name'] . '" class="field-label">' . $field['human_name'] . '</label>';
                $html .= '<label class="field sfcolor">';
                $html .= '<input type="text" name="' . $field['less_name'] . '"role="colorpicker" id="' . $field['machine_name'] . '" value="' . $field['value'] . '" class="gui-input" />';
                $html .= '</label>';
                $html .= '</div></div>';
                $html .= '<script>';
                $html .= '$("#' . $field['machine_name'] . '").spectrum({';
                $html .= '    color: "' . $field['value'] . '",';
                $html .= '    theme: "sp-dark",';
                $html .= '    preferredFormat: "hex6",';
                $html .= '    showInput: true,';
                $html .= '    showPalette: true,';
                $html .= '    clickoutFiresChange: true,';
                $html .= '    cancelText: "",';
                $html .= '    palette: [';
                $html .= $colors;
                $html .= '      ]';
                $html .= '}).show();';
                $html .= '</script>';
                break;

            case 'setting' :
                $html .= '<div class="section"><div class="field">';
                $html .= '<label for="' . $field['machine_name'] . '" class="field-label">' . $field['human_name'] . '</label>';
                $html .= '<label class="field">';
                $html .= '<input type="text" name="' . $field['less_name'] . '" id="' . $field['machine_name'] . '" value="' . $field['value'] . '" class="gui-input" />';
                $html .= '</label>';
                $html .= '</div></div>';
                break;

            case 'yesno' :
                $html .= '<div class="section"><div class="field">';
                $html .= '<label for="' . $field['machine_name'] . '" class="field-label">' . $field['human_name'] . '</label>';
                $html .= '<label class="field select">';
                $html .= '<select name="' . $field['less_name'] . '" id="' . $field['machine_name'] . '" class="gui-input" >';
                foreach ($yesno as $yesno_key => $yesno_label) {
                    $selected = ($yesno_key == $field['value'] ? 'selected="selected"' : '');
                    $html .= "<option value='" . $yesno_key . "' " . $selected . '>' . $yesno_label . '</option>';
                }
                $html .= '</select>';
                $html .= '<i class="arrow double"></i>';
                $html .= '</label>';
                $html .= '</div></div>';
                break;

            case 'font' :
                $html .= '<div class="section"><div class="field">';
                $html .= '<label for="' . $field['machine_name'] . '" class="field-label">' . $field['human_name'] . '</label>';
                $html .= '<label class="field select">';
                $html .= '<select name="' . $field['less_name'] . '" id="' . $field['machine_name'] . '" class="gui-input" >';
                foreach ($fonts as $font => $font_key) {
                    $font_val = '"' . implode('", "', $font_key) . '"';
                    $selected = ($font_val == $field['value'] ? 'selected="selected"' : '');
                    $html .= "<option value='" . $font_val . "' " . $selected . '>' . $font . '</option>';
                }
                $html .= '</select>';
                $html .= '<i class="arrow double"></i>';
                $html .= '</label>';
                $html .= '</div></div>';
                break;
            case 'image':
                $html .= '<div class="section"><div class="field">';
                $html .= '<label for="' . $field['machine_name'] . '" class="field-label">' . $field['human_name'] . '</label>';
                $html .= '<label for="' . $field['machine_name'] . '-file" class="field file">';
                $html .= '<span class="button btn-primary">Choose</span>';
                $html .= '<input type="file" name="' . $field['less_name'] . '"  nv-file-select="" uploader="uploader" value=\'' . basename(trim($field['value'], '"')) . '\' class="gui-file" id="' . $field['machine_name'] . '-file" onChange="imagefield(\'' . $field['machine_name'] . '\');">';
                $html .= '<input type="text" class="gui-input gui-file-text ' . $field['machine_name'] . '-file" placeholder=\'' . basename(trim($field['value'], '"')) . '\' readonly>';
                $html .= '</label>';
                $html .= '</div></div>';
                break;
        }

        return $html;
    }

};