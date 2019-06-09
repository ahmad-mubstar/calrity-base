<?php

class Shopgo_NewSale_Helper_Data extends  Mage_Core_Helper_Abstract {

	public function getProductLabels ($_product, $type) {
 	switch ($type) {
		case 'new':
				$from = $_product->getNewsFromDate();
				$to = new Zend_Date($_product->getNewsToDate());
				$now = new Zend_Date(Mage::getModel('core/date')->timestamp(time()));
				if (isset($from) && $to->isLater($now)): 
					return '<span class="label-new">'.$this->__('New').'</span>';
				else:
					return false;
				endif;
			break;
		case 'sale':
				$_finalPrice = MAGE::helper('tax')->getPrice($_product, $_product->getFinalPrice());
				$_regularPrice = MAGE::helper('tax')->getPrice($_product, $_product->getPrice());
				if ($_regularPrice != $_finalPrice):
					$getpercentage = number_format($_finalPrice / $_regularPrice * 100, 2);
					$finalpercentage = 100 - $getpercentage;
					return '<div class="label-sale percentage">'.number_format($finalpercentage, 0).'% <span>'.$this->__('off').'</span></div>';
				else:
					return false;
				endif;

			break;
	}
 }

}