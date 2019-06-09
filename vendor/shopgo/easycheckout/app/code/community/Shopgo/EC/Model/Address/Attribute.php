<?php
class Shopgo_EC_Model_Address_Attribute extends Mage_Customer_Model_Attribute
{
  public function getIsRequired()
  {
    // ignore postal code validation
    if($this->getName() == 'postcode') {
      return false;
    }
    return $this->_getScopeValue('is_required');
  }
}