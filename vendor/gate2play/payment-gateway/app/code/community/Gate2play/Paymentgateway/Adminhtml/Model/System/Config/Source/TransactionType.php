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
class Gate2play_Paymentgateway_Adminhtml_Model_System_Config_Source_TransactionType
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'DB',
                'label' => Mage::helper('adminhtml')->__('Debit')
            ),
            array(
                'value' => 'PA',
                'label' => Mage::helper('adminhtml')->__('Pre-Authorization')
            ),
        );
    }
}
