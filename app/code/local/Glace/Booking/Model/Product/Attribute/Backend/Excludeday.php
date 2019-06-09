<?php
/*
 * Developer: Rene Voorberg
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 *
*/

class Glace_Booking_Model_Product_Attribute_Backend_Excludeday
extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract{
	
	const TYPE_PERIOD = '1';
	const TYPE_DATE = '2';
	const TYPE_MONTH = '3';
	const TYPE_WEEK = '4';
	
	/**
	 * Retrieve resource instance
	 *
	 */
	protected function _getResource()
	{
		return Mage::getResourceSingleton('booking/product_attribute_backend_excludeday');
	}
	
	
	/**
	 * Error message when duplicates
	 *
	 * @return string
	 */
	protected function _getDuplicateErrorMessage()
	{
		return Mage::helper('booking')->__('Duplicate setting for exlcude days.');
	}
	
	public function validate($object){
	}

	public function afterSave($object)
	{
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		$exRows = $object->getData($this->getAttribute()->getName());
		
		foreach ($exRows as $row){
			$exObject = Mage::getModel('booking/book_excludeday');
			
			if(isset($row['exday_id'])){
				$exObject = $exObject->load($row['exday_id']);
				if(!$exObject->getId()){
					unset($row['exday_id']);
					$exObject = Mage::getModel('booking/book_excludeday');
				}else{
					if(isset($row['deleted']))
						if($row['deleted'] == 1){
							$exObject->delete();
							continue;
						}
				}
			}
			if(isset($row['deleted']))
				if($row['deleted'] == 1){
					continue;
				}
			
			$exObject->setProduct($object);
			$exObject->setPeriodType($row['period_type']);
			if($row['period_type'] == Glace_Booking_Model_Product_Attribute_Backend_Excludeday::TYPE_PERIOD){
				$exObject->setFromDate(date('Y-m-d', strtotime($row['from_date'])));
				$exObject->setToDate(date('Y-m-d', strtotime($row['to_date'])));
			}else{
				$exObject->setValue($row['value']);
			}
			
			$exObject->save();
		}
    
		return $this;
	}
	
	public function afterLoad($object){
		
		$data = array();
		$collection = Mage::getModel('booking/book_excludeday')->getCollection()->addFilter('entity_id', $object->getId());
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