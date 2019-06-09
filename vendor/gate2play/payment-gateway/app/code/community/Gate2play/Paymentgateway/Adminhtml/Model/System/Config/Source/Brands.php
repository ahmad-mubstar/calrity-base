<?php
/**
 * Gate2play Payment payment method model.
 *
 * @category   Gate2play
 * @package    Gate2play_Paymentgateway
 * @author     gate2play.com
 */

/**
 *
 * Gate2play Transcation Mode Dropdown source
 *
 * @author      Gate2play.com
 */
class Gate2play_Paymentgateway_Adminhtml_Model_System_Config_Source_Brands
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'AMEX',
                'label' => Mage::helper('adminhtml')->__('American Express')
            ),
            array(
                'value' => 'DISCOVER',
                'label' => Mage::helper('adminhtml')->__('Discover')
            ),
            array(
                'value' => 'MASTER',
                'label' => Mage::helper('adminhtml')->__('MasterCard')
            ),
            array(
                'value' => 'PAYPAL',
                'label' => Mage::helper('adminhtml')->__('PayPal')
            ),
            array(
                'value' => 'UKASH',
                'label' => Mage::helper('adminhtml')->__('Ukash')
            ),
            array(
                'value' => 'VISA',
                'label' => Mage::helper('adminhtml')->__('Visa')
            ),
 	    array(
                'value' => 'ONECARD',
                'label' => Mage::helper('adminhtml')->__('ONECARD')
            ),
  	   array(
                'value' => 'CASHU',
                'label' => Mage::helper('adminhtml')->__('CASHU')
            ),
        );
    }
}
