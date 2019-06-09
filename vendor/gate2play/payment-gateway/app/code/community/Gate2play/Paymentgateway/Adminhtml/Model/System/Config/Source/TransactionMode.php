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
class Gate2play_Paymentgateway_Adminhtml_Model_System_Config_Source_TransactionMode
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'CONNECTOR_TEST',
                'label' => Mage::helper('adminhtml')->__('CONNECTOR_TEST')
            ),
            array(
                'value' => 'INTEGRATOR_TEST',
                'label' => Mage::helper('adminhtml')->__('INTEGRATOR_TEST')
            ),
            array(
                'value' => 'LIVE',
                'label' => Mage::helper('adminhtml')->__('LIVE')
            ),            
        );
    }
}
