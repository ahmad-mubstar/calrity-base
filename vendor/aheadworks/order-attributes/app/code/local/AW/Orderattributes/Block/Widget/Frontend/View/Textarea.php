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

class AW_Orderattributes_Block_Widget_Frontend_View_Textarea
    extends AW_Orderattributes_Block_Widget_Frontend_ViewAbstract
{
    public function getHtml()
    {
        if (!$this->_getValue()) {
            return '';
        }
        $labelHtml = $this->_getLabelHtml();
        $valueHtml = $this->_getValueHtml();
        $attributeCode = $this->_getCode();
        $html = "
            <tr id=\"{$attributeCode}\">
                <td class=\"label\">{$labelHtml}</td>
                <td class=\"value\">{$valueHtml}</td>
            </tr>
        ";
        return $html;
    }

    protected function _getLabelHtml()
    {
        return "<label for=\"{$this->_getCode()}\">{$this->_getLabel()}</label>";
    }

    protected function _getValueHtml()
    {
        $value = nl2br(htmlspecialchars($this->_getValue()));
        return "<strong>{$value}</strong>";
    }
}