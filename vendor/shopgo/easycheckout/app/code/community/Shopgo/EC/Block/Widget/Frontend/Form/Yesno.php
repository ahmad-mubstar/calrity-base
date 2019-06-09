<?php
class Shopgo_EC_Block_Widget_Frontend_Form_Yesno
    extends AW_Orderattributes_Block_Widget_Frontend_Form_Yesno
{
    public function getHtml($isForEdit = false)
    {
        if (!$isForEdit && !$this->_getValue()) {
            return '';
        }
        $labelHtml = $this->_getLabelHtml();
        $selectHtml = $this->_getSelectHtml($isForEdit);
        $html = "
            {$labelHtml}
            <div class=\"input-box\">
                {$selectHtml}
            </div>
        ";
        return $html;
    }

    protected function _getLabelHtml()
    {
        $labelHtml = "<label for=\"{$this->_getCode()}\"";
        if ($this->getProperty('validation_rules/is_required')) {
            $labelHtml .= "class=\"required\"><em>*</em>";
        } else {
            $labelHtml .= ">";
        }
        $labelHtml .= "{$this->_getLabel()}</label>";
        return $labelHtml;
    }

    protected function _getSelectHtml($isForEdit = false)
    {
        $selectHtml = "";
        if ($isForEdit) {
            $selectData = array(
                'name'  => $this->_getCode(),
                'id'    => $this->_getCode(),
                'title' => $this->_getLabel(),
                'class' => 'ec-select '
            );
            if ($this->getProperty('validation_rules/is_required')) {
                $selectData['class'] .= "required-entry";
            }

            $selectHtml = "<select ";
            foreach($selectData as $key => $value) {
                $selectHtml .= "{$key}=\"{$value}\"";
            }
            $selectHtml .= ">";
            $optionSource = Mage::helper('aw_orderattributes/options')->getOptionsForYesnoAttribute(true, true);
            foreach($optionSource as $value => $label) {
                $selectHtml .= "<option value=\"{$value}\"";
                if ($this->_getValue() == $value) {
                    $selectHtml .= " selected";
                }
                $selectHtml .= ">{$label}</option>";
            }
            $selectHtml .= "</select>";
        } else {
            $optionSource = Mage::helper('aw_orderattributes/options')->getOptionsForYesnoAttribute(true, true);
            foreach($optionSource as $value => $label) {
                if ($this->_getValue() == $value) {
                    $selectHtml = $label;
                    break;
                }
            }
        }
        return $selectHtml;
    }
}