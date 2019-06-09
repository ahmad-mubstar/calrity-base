<?php
class Shopgo_Quickview_Helper_Data extends Mage_Core_Helper_Abstract {
	public function getJSQuickview(){
		if (Mage::getStoreConfigFlag('quickview/general/enable')){
			if (null == Mage::registry('shopgo.quickview')){
				Mage::register('shopgo.quickview', 1);
				return 'shopgo/quickview/js/quickview.js';
			}
		}
		return;
	}
	public function getJSFancybox(){
		if (null == Mage::registry('shopgo.fancybox')){
			Mage::register('shopgo.fancybox', 1);
			return 'shopgo/quickview/js/jquery.fancybox-1.3.4.pack.js';
		}
		return;
	}
}