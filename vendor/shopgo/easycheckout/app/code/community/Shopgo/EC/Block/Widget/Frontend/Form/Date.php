<?php
class Shopgo_EC_Block_Widget_Frontend_Form_Date
    extends AW_Orderattributes_Block_Widget_Frontend_Form_Date
{
//     public function getHtml($isForEdit = false)
//     {
//         $dateHtml = "";
//         $value = $this->_getValue();
//         if ($isForEdit) {
//             //DATETIME PICKER
//             $className = 'datetime-picker input-text';
//             if ($this->getProperty('validation_rules/is_required')) {
//                 $className .= " required-entry";
//             }
//             $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
//             $calendar = Mage::app()->getLayout()
//                 ->createBlock('core/html_date')
//                 ->setId($this->_getCode())
//                 ->setName($this->_getCode())
//                 ->setClass($className)
//                 ->setImage(Mage::getDesign()->getSkinUrl('images/calendar.gif'))
//                 ->setFormat($format)
//                 ->setExtraParams("style='width:auto'")
//             ;
//             if ($value instanceof Zend_Date) {
//                 $calendar->setValue($value->toString($format));
//             } else if (is_string($value)) {
//                 $calendar->setValue($value);
//             }
//             $dateHtml = $calendar->getHtml();
//         } else {
//             $dateHtml = $value;
//             if (!is_null($value)) {
//                 $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_FULL);
//                 $dateHtml = $value->toString($format);
//             } else {
//                 return '';
//             }
//         }

//         //LABEL
//         $labelHtml = "<label for=\"{$this->_getCode()}\"";
//         if ($this->getProperty('validation_rules/is_required')) {
//             $labelHtml .= "class=\"required\"><em>*</em>";
//         } else {
//             $labelHtml .= ">";
//         }
//         $labelHtml .= "{$this->_getLabel()}</label>";
//         $html = "
//             {$labelHtml}
//             <div class=\"input-box field-row\">
//                 {$dateHtml}
//             </div>
//         ";
//         return $html;
//     }

//     protected function _getValue()
//     {
//         if (is_null($this->_value)) {
//             $value = $this->getProperty('default_value');
//             if (!is_null($value) && strlen(trim($value)) > 0) {
//                 try {
//                     return Mage::app()->getLocale()->date($value, Zend_Date::ISO_8601);
//                 } catch(Exception $e) {
//                     return $value;
//                 }
//             } else {
//                 return null;
//             }
//         }
//         return Mage::app()->getLocale()->date($this->_value, Zend_Date::ISO_8601);
//     }

	public function getHtml($isForEdit = false)
	{
		if (!$isForEdit && !$this->_getValue()) {
			return '';
		}
		$labelHtml = $this->_getLabelHtml();
		$inputHtml = $this->_getInputHtml($isForEdit);
		$html = "
		{$labelHtml}
		<div class=\"input-box\">
		{$inputHtml}
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
	
				protected function _getInputHtml($isForEdit = false)
				{
				$inputHtml = "";
				if ($isForEdit) {
				$inputData = array(
				'type'  => 'text',
				'name'  => $this->_getCode(),
				'id'    => $this->_getCode(),
				'title' => $this->_getLabel(),
					'class' => 'datetime-picker',
					'value' => htmlspecialchars($this->_getValue())
				);
				if ($classNames = $this->_getValidateCssClassNames()) {
				$inputData['class'] .= ' ' . $classNames;
				}
	
				$inputHtml = "<input ";
						foreach($inputData as $key => $value) {
						$inputHtml .= "{$key}=\"{$value}\"";
				}
				$inputHtml .= " />";
				} else {
				$inputHtml = htmlspecialchars($this->_getValue());
				}
						return $inputHtml;
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
        switch($this->getProperty('validation_rules/input_validation')) {
				case AW_Orderattributes_Model_Source_Validation::ALPHANUMERIC_CODE:
						$result .= "validate-alphanum-with-spaces ";
								break;
								case AW_Orderattributes_Model_Source_Validation::NUMERIC_CODE:
								$result .= "validate-number ";
										break;
					case AW_Orderattributes_Model_Source_Validation::ALPHA_CODE:
					$result .= "validate-alpha ";
					break;
					case AW_Orderattributes_Model_Source_Validation::URL_CODE:
						$result .= "validate-url ";
						break;
						case AW_Orderattributes_Model_Source_Validation::EMAIL_CODE:
								$result .= "validate-email ";
						break;
						default:
					}
					return trim($result);
					}
}