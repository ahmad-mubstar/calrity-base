<?php
/*
 * Developer: Rene Voorberg
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 *
*/
class Glace_Booking_Block_Adminhtml_Catalog_Product_Edit_Tab_Customsession
extends Mage_Adminhtml_Block_Widget
implements Varien_Data_Form_Element_Renderer_Interface
{

	/**
	 * Initialize block
	 */
	public function __construct()
	{
		$this->setTemplate('booking/edit/session/custom.phtml');
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
				'label' => Mage::helper('booking')->__('Add day definition'),
				'onclick' => 'return sessionItems.add()',
				'class' => 'add'
		));
		$button->setName('add_session_button');
		
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


	public function getAttrData($attrcode){
		$data =  Mage::registry('current_product')->getData($attrcode);
		
		if($data)
			return $data;
		else {
			$data = array('time_id' => null, 'hour' => 0, 'minute' => 0, 'seconds' => 0);
			return $data;
		}
	}
	
	public function getAddButtonHtml(){
		return $this->getChildHtml('add_button');
	}
	
	public function getValues(){
		return Mage::registry('current_product')->getData('custom_session');
	}
	
	public function getDateInFormat($date){
			
		if($date){
			$data = explode(" ", $date);
			$date = $data[0];
			$date = Mage::helper('core')->formatDate($date, 'short', false);
		}
		return $date;
	}


}