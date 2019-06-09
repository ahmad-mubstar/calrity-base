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
 * Gate2play Brands Dropdown source
 *
 * @author      Gate2play.com
 */
class Gate2play_Paymentgateway_Adminhtml_Model_System_Config_Source_Styles
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'card',
                'label' => Mage::helper('adminhtml')->__('Card')
            ),
            array(
                'value' => 'plain',
                'label' => Mage::helper('adminhtml')->__('Plain')
            ),
        );
    }
}
