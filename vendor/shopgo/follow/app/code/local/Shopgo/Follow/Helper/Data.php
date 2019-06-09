<?php
//$themeCfg = Mage::helper('nova/data');
//$themeCfg->getField
class Shopgo_Follow_Helper_Data extends Mage_Core_Helper_Abstract {
	public function __construct(){
		$this->defaults = array();
	}

	public function get($attributes=array()) {
		$data 						= $this->defaults;

		$config = array();

		foreach(Mage::getStoreConfig("shopgo_follow_cfg") as $k => $group){
			$groupName = $k;
			foreach($group as $key => $value){
				$config[$groupName.'_'.$key] = $value;
			}
		}

		if (is_array($config)) 				$data = array_merge($data, $config);

		return $data;
	}
	public function getField($field) {
		$data = $this->get();
		return $data[$field];
	}

}