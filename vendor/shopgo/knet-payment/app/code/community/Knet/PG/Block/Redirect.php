<?php
/**
 * @category   Knet
 * @package    Knet_PG
 * @author     ali@shopgo.me
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Knet_PG_Block_Redirect extends Mage_Core_Block_Abstract
{
	protected function _toHtml()
	{
		$standard 	= $this->getOrder()->getPayment()->getMethodInstance();

        $form 		= new Varien_Data_Form();
        $form->setAction($standard->getPGUrl())
            ->setId('knet_payment_gateway')
            ->setName('knet_payment_gateway')
            ->setMethod('POST')
            ->setUseContainer(true);

		foreach ($standard->getFormFields() as $field => $value) {
            $form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
        }

        // $merchant_id = Mage::getStoreConfig('payment/pg/merchant_id');
        $formFields = $standard->getFormFields();
        // session id -> order id
        $session_id = $reference_number = $formFields['reference_number'];

        $html = '<html><body>';

        $html.= $this->__('You will be redirected to Knet payment gateway in a few seconds.');
		$html.= $form->toHtml();
		// die($html);
        $html.= '<script type="text/javascript">document.getElementById("knet_payment_gateway").submit();</script>';
        $html.= '</body></html>';

		return $html;
    }
}