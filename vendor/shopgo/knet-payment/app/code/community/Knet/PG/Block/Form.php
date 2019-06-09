<?php
/**
 * @category   Knet
 * @package    Knet_PG
 * @author     magepsycho@gmail.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Knet_PG_Block_Form extends Mage_Payment_Block_Form
{
	protected function _construct()
    {
        $this->setTemplate('knet/pg/form.phtml');
        parent::_construct();
    }
}
