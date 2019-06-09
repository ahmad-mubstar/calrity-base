<?php
/*
 * Developer: Rene Voorberg
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 *
*/

class Glace_Booking_Block_Adminhtml_Catalog_Product_Edit_Tab_Price_Rule
extends Mage_Adminhtml_Block_Widget
implements Varien_Data_Form_Element_Renderer_Interface
{
	
	/**
	 * Initialize block
	 */
	public function __construct()
	{
		$this->setTemplate('booking/edit/price/rule.phtml');
	}
	
	/**
	 * Prepare global layout
	 * Add "Add tier" button to layout
	 *
	 * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Tier
	 */
	protected function _prepareLayout()
	{
		$button = $this->getLayout()->createBlock('adminhtml/widget_button')
		->setData(array(
				'label' => Mage::helper('booking')->__('Add Rule'),
				'onclick' => 'return priceItems.add()',
				'class' => 'add'
		));
		$button->setName('add_priceItems_button');
	
		$this->setChild('add_button', $button);
		return parent::_prepareLayout();
	}
	
	/**
	 * Render HTML
	 *
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		$this->setElement($element);
		return $this->toHtml();
	}
	
	/**
	 * Retrieve 'add exclude day item' button HTML
	 *
	 * @return string
	 */
	public function getAddButtonHtml()
	{
		return $this->getChildHtml('add_button');
	}
	
	public function getValues(){
		return Mage::registry('current_product')->getData('price_rule');
	}
	
	public function getDateInFormat($date){
		 
		if($date){
			$data = explode(" ", $date);
			$date = $data[0];
			$date = Mage::helper('core')->formatDate($date, 'medium', false);
		}
		return $date;
	}
}