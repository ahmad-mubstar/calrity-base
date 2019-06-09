<?php
/**
 * @category   Knet
 * @package    Knet_PG
 * @author     ali@shopgo.me
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Knet_PG_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getConfig($field, $default = null)
	{
        $value = Mage::getStoreConfig('payment/pg/' . $field);
        if(!isset($value) or trim($value) == ''){
            return $default;
        }else{
            return $value;
        }
    }

    public function log($data)
	{
		if(!$this->getConfig('enable_log')){
			return;
		}
		$separator = "===================================================================";
        Mage::log($separator, null, 'knet_pg.log', true);
        Mage::log($data, null, 'knet_pg.log', true);
    }
}