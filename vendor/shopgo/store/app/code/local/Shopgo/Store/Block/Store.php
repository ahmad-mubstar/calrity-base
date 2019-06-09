<?php
/**
 * Google Tag Manager Block
 *
 * @category    ShopGo
 * @package     Shopgo_GTM
 * @author      Ali Halabyah <ali@shopgo.me>
 * @copyright   Copyright (c) 2014 ShopGo
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License 3.0 (OSL-3.0)
 */
class Shopgo_Store_Block_Store extends Mage_Core_Block_Template
{
	/**
	 * Get visitor data for use in the data layer.
	 *
	 * @link https://developers.google.com/tag-manager/reference
	 * @return array
	 */
	protected function _getStoreData($storeName)
	{
		$data = array();
		return $data;
	}

}
