<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento community edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento community edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Orderattributes
 * @version    1.0.1
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Orderattributes_Block_Widget_Backend_Pdf_Dropdown
    extends AW_Orderattributes_Block_Widget_Backend_PdfAbstract
{
    public function getLabel()
    {
        return $this->_getLabel();
    }

    public function getValue()
    {
        $label = '';
        foreach($this->_getOptionsForSelect() as $value => $valueLabel) {
            if ($this->_getValue() == $value) {
                $label = $valueLabel;
                continue;
            }
        }
        return $label;
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
        $options = Mage::helper('aw_orderattributes/options')->getOptionsForAttributeByStoreId($attribute, $storeId, false);
        foreach($options as $key => $value) {
            $options[$key] = $value;
        }
        return $options;
    }
}