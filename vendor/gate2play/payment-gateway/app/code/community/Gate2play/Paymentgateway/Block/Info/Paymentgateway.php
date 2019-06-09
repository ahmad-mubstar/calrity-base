<?php
/**
 * Gate2play Payment payment method model.
 *
 * @category   Gate2play
 * @package    Gate2play_Paymentgateway
 * @author     gate2play.com
 */
class Gate2play_Paymentgateway_Block_Info_Paymentgateway extends Mage_Payment_Block_Info
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('gate2play/paymentgateway/info.phtml');
    }
/*
    protected function _prepareSpecificInformation($transport = null)
    {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }
        $info = $this->getInfo();
        $transport = new Varien_Object();
        $transport = parent::_prepareSpecificInformation($transport);
        $transport->addData(array(
            Mage::helper('payment')->__('Check No#') => $info->getCheckNo(),
            Mage::helper('payment')->__('Check Date') => $info->getCheckDate()
        ));
        return $transport;
    }*/
}
