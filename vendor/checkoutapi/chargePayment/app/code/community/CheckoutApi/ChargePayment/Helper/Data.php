<?php

class CheckoutApi_ChargePayment_Helper_Data  extends Mage_Core_Helper_Abstract
{
    public function getConfigData($field,$section,$storeId = null)
    {
        if (null === $storeId) {
            $storeId = Mage::app()->getStore();
        }
        $path = "payment/$section/".$field;
        return Mage::getStoreConfig($path, $storeId);
    }

    public function getJsPath()
    {
       $mode =  $this->getConfigData('mode','creditcard');
        $js = '<script src="https://cdn3.checkout.com/js/checkout.js" async ></script>';
       if($mode == 'sandbox') {
           $js ='<script src="https://cdn3.checkout.com/sandbox/js/checkout.js" async ></script>';
       }
        return $js;
    }
    
    public function replace_between($str, $needle_start, $needle_end, $replacement) 
    {
      $pos = strpos($str, $needle_start);
      $start = $pos === false ? 0 : $pos + strlen($needle_start);

      $pos = strpos($str, $needle_end, $start);
      $end = $start === false ? strlen($str) : $pos;

      return substr_replace($str,$replacement,  $start, $end - $start);
  }
}