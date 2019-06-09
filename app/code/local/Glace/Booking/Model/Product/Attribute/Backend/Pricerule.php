<?php
/*
 * Developer: Rene Voorberg
* Team site: http://cmsideas.net/
* Support: http://support.cmsideas.net/
*
*
*/
class Glace_Booking_Model_Product_Attribute_Backend_Pricerule
extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract{
	
	/**
	 * Retrieve resource instance
	 *
	 */
	protected function _getResource()
	{
		return Mage::getResourceSingleton('booking/product_attribute_backend_pricerule');
	}
	
	public function afterSave($object)
	{
	
		$priceRows = $object->getData($this->getAttribute()->getName());
		$types = array();
		foreach ($priceRows as $row){
			$rule = Mage::getModel('booking/book_pricerule');
				
			if(isset($row['rule_id'])){
				$rule = $rule->load($row['rule_id']);
				if(!$rule->getId()){
					unset($row['rule_id']);
					$rule = Mage::getModel('booking/book_pricerule');
				}else{
					if($row['deleted'] == 1){
						$rule->delete();
						continue;
					}
				}
			}
			if($row['deleted'] == 1){
				continue;
			}
			
			$rule->setProduct($object);
			$rule->setType($row['type']);
			if($row['qty'] == null)
				Mage::throwException(Mage::helper('booking')->__("Please specify a quantity in price rule."));
			$rule->setValue($row['qty']);
			if(isset($row['qtytype']))
				$rule->setValueType($row['qtytype']);
			$rule->setMove($row['move']);
			$rule->setAmountType($row['amount_type']);
			$rule->setAmount($row['amount']);
			if($row['amount'] == null)
				Mage::throwException(Mage::helper('booking')->__("Please specify an amount in price rule."));

			if(!isset($types[$row['type']]))
				$types[$row['type']] = true;
			else
				Mage::throwException(Mage::helper('booking')->__("Product can't have more price rules of the same type."));
			
			$rule->save();
		}
	
		return $this;
	}
	
	public function afterLoad($object){
	
		$data = array();
		$collection = Mage::getModel('booking/book_pricerule')->getCollection()->addFilter('entity_id', $object->getId());
		foreach ($collection as $item){
			$pom = array();
			foreach ($item->getData() as $key => $value){
				if($value)
					$pom[$key] = $value;
			}
			$data[] = $pom;
		}
		$object->setData($this->getAttribute()->getAttributeCode(), $data);
		$object->setOrigData($this->getAttribute()->getAttributeCode(), $data);
	
		return $this;
	}
}