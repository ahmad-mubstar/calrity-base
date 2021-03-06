<?php
class Shopgo_EC_Block_Widget_Frontend_Form_Multiselect
    extends AW_Orderattributes_Block_Widget_Frontend_Form_Multipleselect
{
    public function getHtml($isForEdit = false)
    {
        $value = $this->_getValue();
        if (!$isForEdit &&
            ((count($value) === 0)  || (count($value) === 1 && isset($value[0]) && $value[0] === '0'))
        ) {
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
                'name'     => $this->_getCode() . '[]',
                'id'       => $this->_getCode(),
                'title'    => $this->_getLabel(),
                'class'    => 'ec-multi-select ',
                'multiple' => 'multiple'
            );
            if ($this->getProperty('validation_rules/is_required')) {
                $selectData['class'] .= "required-entry";
            }

            $selectHtml = "<select ";
            foreach($selectData as $key => $value) {
                $selectHtml .= "{$key}=\"{$value}\"";
            }
            $selectHtml .= ">";
            foreach($this->_getOptionsForSelect() as $value => $label) {
                $selectHtml .= "<option value=\"{$value}\"";
                if (in_array($value, $this->_getValue())) {
                    $selectHtml .= " selected";
                }
                $selectHtml .= ">{$label}</option>";
            }
            $selectHtml .= "</select>";
        } else {
            $labels = array();
            foreach($this->_getOptionsForSelect() as $value => $label) {
                if (in_array($value, $this->_getValue())) {
                    $labels[] = $label;
                }
            }
            $selectHtml = implode(', ', $labels);
        }
        return $selectHtml;
    }

    /**
     * getter
     *
     * @return mixed
     */
    protected function _getValue()
    {
        $value = parent::_getValue();
        if (is_string($value)) {
            $value = explode(',', $value);
        }
        if (!is_array($value)) {
            $value = array();
        }
        return $value;
    }

    private function _getOptionsForSelect()
    {
        if (is_null($this->getTypeModel())) {
            return null;
        }
        $attribute = $this->getTypeModel()->getAttribute();
        if (is_null($attribute)) {
            return null;
        }
        $storeId = Mage::app()->getStore()->getId();
        $options = Mage::helper('aw_orderattributes/options')
            ->getOptionsForAttributeByStoreId($attribute, $storeId, false);
        foreach($options as $key => $value) {
            $options[$key] = htmlspecialchars($value);
        }
        return $options;
    }
}