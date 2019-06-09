<?php
/*
 * Developer: Rene Voorberg
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 *
*/
class Glace_Booking_Block_Order_Items_Order_Booking extends Mage_Sales_Block_Order_Item_Renderer_Default
{
public function getItemOptions()
    {
        $result = array();
        if ($options = $this->getItem()->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }
        
        for ($i = 0; $i < count($result); $i++)
        {
            if($result[$i]['option_type'] == 'multidate_type'){
        		$value = $this->getBookValue($result[$i]['value']);
        		$result[$i]['value'] = $value;
        		$result[$i]['print_value'] = $value;
        		$result[$i]['option_value'] = $value;
        	}
        }
		
        return $result;
    }
    
    
    public function getBookValue($data){
    	date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
    	
    	$values = explode(",", $data);
    	$from = date("d-m-Y", $values[0]/1000);
    	
    	if(count($values) == 1 || strtotime($from) < $values[0] / 1000){
    		$out = '';
    		for($i = 0; $i < count($values) - 1; $i++)
    			$out .= Mage::helper('core')->formatDate(date("d-m-Y H:i:s", $values[$i]/1000), 'medium', true).'<br />';
    		
    		return $out;
    	}else{
    		$to = date("d-m-Y", $values[count($values)-2]/1000);
    		return  Mage::helper('core')->formatDate($from, 'medium', false).' - '.Mage::helper('core')->formatDate($to, 'medium', false);
    	}
    }
}
