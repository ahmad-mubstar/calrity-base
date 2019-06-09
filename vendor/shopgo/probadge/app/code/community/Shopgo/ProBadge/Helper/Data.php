<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension
 * to newer versions in the future.
 *
 * @category   
 * @package    Shopgo
 * @copyright  Copyright (c) 2015 ShopGo
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Shopgo_ProBadge_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_product = null;

    protected function getProduct() {
        if (!$this->_product) {
            $this->_product = Mage::registry('product');
        }
        return $this->_product;
    }

    public function getNow() {
        return strtotime(date('Y-m-d'));
    }

    public function getIsNew($product) {
        if(!$product)
            $product = $this->getProduct();

        $product = Mage::getModel('catalog/product')->load($product->getId());

        $now = $this->getNow();
        $newsFrom = $product->getNewsFromDate();
        $newsTo = $product->getNewsToDate();

        if (($now >= strtotime($newsFrom) && $now <= strtotime($newsTo)) || ($now >= strtotime($newsFrom) && is_null($newsTo) && !is_null($newsFrom)))
            return true;

        return false;
    }

    public function getHasSpecialPrice($product) {
        if(!$product)
            $product = $this->getProduct();

        $product = Mage::getModel('catalog/product')->load($product->getId());

        $specialPrice = $product->getSpecialPrice();
        if( $specialPrice !== null && !empty($specialPrice) && $specialPrice != 0 )
            return $specialPrice; 

        return false;
    }

    public function getIsOnSale($product) {
        if(!$product)
            $product = $this->getProduct();

        $product = Mage::getModel('catalog/product')->load($product->getId());

        $now = $this->getNow();
        $specialPrice = $product->getSpecialPrice();

        $specialFrom = $product->getSpecialFromDate();
        $specialTo = $product->getSpecialToDate();

        if (($now >= strtotime($specialFrom) && $now <= strtotime($specialTo)) || ($now >= strtotime($specialFrom) && is_null($specialTo) && !is_null($specialFrom)))
            return true;

        return false;
    }

    public function getIsOutOfStock($product, $useQty = false) {
        if(!$product)
            $product = $this->getProduct();

        $product = Mage::getModel('catalog/product')->load($product->getId());

        $out_of_stock = !( $product->isSaleable() );
        $stocklevel = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
        if($useQty) {
            if( !$out_of_stock && $stocklevel > 0 )
                return false;
            return true;
        } 
        else {
            return $out_of_stock;
        }
    }

    /*public function isOutOfStock($product) {
        $outOfStock = false;
        $stock = $product->getStockItem();

        if(!$stock->getIsInStock()){
            $outOfStock = true;
        }

        return $outOfStock;
    }*/
  
    public function isHot($product = null) {
        if(!$product)
            $product = $this->getProduct();

        $product = Mage::getModel('catalog/product')->load($product->getId());

        if($product->getIsHot())
            return true;

        return false;
    }

    public function getBadgeImageUrl($type = 'new') {
        switch ($type):
        case 'new':
        return Mage::getDesign()->getSkinUrl('probadge/images/new.png');
        break;
        case 'sale':
        return Mage::getDesign()->getSkinUrl('probadge/images/sale.png');
        break;
        default:
        return Mage::getDesign()->getSkinUrl('probadge/images/new.png');
        break;
        endswitch;
    }

    /*
    * 
    * ToDo : handle assinged to rule price products
    * 
    */
}

