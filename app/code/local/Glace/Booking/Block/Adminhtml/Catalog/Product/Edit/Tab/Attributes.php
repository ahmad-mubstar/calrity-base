<?php
/*
 * Developer: Rene Voorberg
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 *
*/

class Glace_Booking_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes
extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes{
	
	
	protected function _prepareForm(){
		
		parent::_prepareForm();
		$group = $this->getGroup();
		if ($group) {
			$form = $this->getForm();
			
			$attributes = Mage::getModel('booking/attributes');
			
			foreach ($attributes->getAllOptions() as $code => $block)
			{
				$element = $form->getElement($code);
				if ($element) {
					$element->setRenderer(
							$this->getLayout()->createBlock($block)
					);
				}
			}
		}
		
	}
}