<?php
/**
 * Paytabs payment gateway
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so you can be sent a copy immediately.
 *
 *
 * @category Ras
 * @package    Ras_Paytabs
 * @copyright  Copyright (c) 2010 Paytabs
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ras_Paytabs_Helper_Data extends Mage_Core_Helper_Abstract
{
    
    public function getNewFolder($fo,$fn)
    {
        if(rename(Mage::getBaseDir().DS.$fo,Mage::getBaseDir().DS.$fn))
            return true;
        else
            return false;
    }
}
