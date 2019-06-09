<?php
/**
 * @category   Knet
 * @package    Knet_PG
 * @author     ali@shopgo.me
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Knet_PG_Model_System_Config_Source_Modes
{
    public function toOptionArray()
    {
        return array(
            0    => Mage::helper('pg')->__('Test'),
            1    => Mage::helper('pg')->__('Live'),
        );
    }
}