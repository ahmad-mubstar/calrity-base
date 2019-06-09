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
 * @category   ShopGo
 * @package    Shopgo_StyleEditor
 * @copyright  Copyright (c) 2014 ShopGo http://www.shopgo.me
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Shopgo_StyleEditor_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function isAdmin() {
        if(Mage::app()->getStore()->isAdmin()) {
            return true;
        }

        if(Mage::getDesign()->getArea() == 'adminhtml') {
            return true;
        }

        return false;
    }

    // Retrieve current installed version
    public function getExtensionVersion() {
      return (string) Mage::getConfig()->getNode()->modules->Shopgo_StyleEditor->version;
    }

    public function getSelectedLogo() {
        return 'logo...';
        //return (string) Mage::getConfig()->getNode()->modules->Shopgo_StyleEditor->version;
    }
	
}

