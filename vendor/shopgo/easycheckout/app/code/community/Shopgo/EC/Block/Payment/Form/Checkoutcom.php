<?php

class Shopgo_EC_Block_Payment_Form_Checkoutcom extends Mage_Payment_Block_Form_Cc
{
    function _construct()  
	{
		parent::_construct();
		$this->setTemplate('shopgo/ec/payment/form/checkoutcom.phtml');
	}
}
?>