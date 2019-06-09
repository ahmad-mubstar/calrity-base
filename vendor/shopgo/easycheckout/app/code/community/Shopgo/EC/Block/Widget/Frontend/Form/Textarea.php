<?php
class Shopgo_EC_Block_Widget_Frontend_Form_Textarea
    extends AW_Orderattributes_Block_Widget_Frontend_Form_Textarea
{
    public function getHtml($isForEdit = false)
    {
        if (!$isForEdit && !$this->_getValue()) {
            return '';
        }
        $labelHtml = $this->_getLabelHtml();
        $textareaHtml = $this->_getTextareaHtml($isForEdit);
        $html = "
        		<div class=\"ui form\">
  <div class=\"field\">
  {$labelHtml}
  {$textareaHtml}
  </div>
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

    protected function _getTextareaHtml($isForEdit = false)
    {
        $textareaHtml = "";
        if ($isForEdit) {
            $textareaData = array(
                'name'  => $this->_getCode(),
                'id'    => $this->_getCode(),
                'title' => $this->_getLabel(),
                'class' => ''
            );
            if ($classNames = $this->_getValidateCssClassNames()) {
                $textareaData['class'] .= ' ' . $classNames;
            }

            $textareaHtml = "<textarea ";
            foreach($textareaData as $key => $value) {
                $textareaHtml .= "{$key}=\"{$value}\"";
            }
            $value = htmlspecialchars($this->_getValue());
            $textareaHtml .= ">{$value}</textarea>";
        } else {
            $textareaHtml = nl2br(htmlspecialchars($this->_getValue()));
        }
        return $textareaHtml;
    }

    protected function _getValidateCssClassNames()
    {
        $result = "";
        if ($this->getProperty('validation_rules/is_required')) {
            $result .= "required-entry ";
        }
        if ($this->getProperty('validation_rules/minimum_text_length') ||
            $this->getProperty('validation_rules/maximum_text_length')
        ) {
            $result .= "aw-oa-validate-length ";
            if ($length = $this->getProperty('validation_rules/minimum_text_length')) {
                $result .= "minimum-length-" . (int)$length . " ";
            }
            if ($length = $this->getProperty('validation_rules/maximum_text_length')) {
                $result .= "maximum-length-" . (int)$length . " ";
            }
        }
        return trim($result);
    }
}