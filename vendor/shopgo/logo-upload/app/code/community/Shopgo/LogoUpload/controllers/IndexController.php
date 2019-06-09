<?php
class Shopgo_LogoUpload_IndexController extends Mage_Core_Controller_Front_Action {
	public function indexAction(){
		
	}
    public function saveAction(){
    	/*
    	Mage::register('isSecureArea', true);
		
		$website = Mage::app()->getStore()->getWebsiteId();
		$store = Mage::app()->getStore()->getStoreId();
		
		Mage::helper('logoupload/data')->saveConfig('design/header/logo_src', 'test3', 'stores', '4');
		
		$allStores = Mage::app()->getStores();
		foreach ($allStores as $_eachStoreId => $val)
		{
			$_storeCode = Mage::app()->getStore($_eachStoreId)->getCode();
			$_storeName = Mage::app()->getStore($_eachStoreId)->getName();
			$_storeId = Mage::app()->getStore($_eachStoreId)->getId();
			echo $_storeId;
			echo $_storeCode;
			echo $_storeName;
		}
		*/
	}
	
	public function testAction(){
		/*
		$store = Mage::getModel('core/store')->load('german');
		
		var_dump(Mage::app()->getStore('3')->getConfig('design/theme/skin'));
		
		var_dump(Mage::getStoreConfig('design/theme/skin'));
		*/
		//echo $store->getId();
	}
}