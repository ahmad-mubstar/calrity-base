<?php
class Shopgo_EC_Model_Resources_Activpayment
{
    public function toOptionArray()
    {
        //$methods = array(array('value'=>'', 'label'=>''));
        $payments = Mage::getSingleton('payment/config')->getActiveMethods();
        foreach ($payments as $paymentCode=>$paymentModel) {
            $paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
            $methods[$paymentCode] = array(
                'label'   => $paymentTitle,
                'value' => $paymentCode,
            );
        }
        return $methods;
    }
}
