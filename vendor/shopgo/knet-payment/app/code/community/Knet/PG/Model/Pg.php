<?php
/**
 * @category   Knet
 * @package    Knet_PG
 * @author     ali@shopgo.me
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Knet_PG_Model_Pg extends Mage_Payment_Model_Method_Abstract
{
    const PAYMENT_LIVE_URL       = '';
    const PAYMENT_TEST_URL       = '';

    protected $_code 			= 'pg';
    protected $_formBlockType 	= 'pg/form';
    protected $_infoBlockType 	= 'pg/info';

	protected $_isGateway               = false;
    protected $_canAuthorize            = false;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;

	//protected $_allowCurrencyCode = array('EUR', 'USD');

	public function validate()
    {
        parent::validate();
        $paymentInfo = $this->getInfoInstance();
        if ($paymentInfo instanceof Mage_Sales_Model_Order_Payment) {
            $currencyCode = $paymentInfo->getOrder()->getBaseCurrencyCode();
        } else {
            $currencyCode = $paymentInfo->getQuote()->getBaseCurrencyCode();
        }
        if (!$this->canUseForCurrency($currencyCode)) {
            Mage::throwException(Mage::helper('pg')->__('Selected currency code ('.$currencyCode.') is not compatabile with this payment.'));
        }
        return $this;
    }

	public function canUseForCurrency($currencyCode)
    {
//        if (!in_array($currencyCode, $this->_allowCurrencyCode)) {
//            return false;
//        }
        return true;
    }

    public function canCapture()
    {
        return true;
    }

    public function capture(Varien_Object $payment, $amount)
    {
        $payment->setStatus(self::STATUS_APPROVED)
            	->setLastTransId($this->getTransactionId());

        return $this;
    }

	public function getIssuerUrls()
	{
		return array("live" => self::PAYMENT_LIVE_URL,
					 "test" => self::PAYMENT_TEST_URL);

	}

	public function getPGUrl()
	{
		$setIssuerUrls 	= $this->getIssuerUrls();
		if($this->getConfigData('mode')){
			return $setIssuerUrls["live"];
		}else{
			return $setIssuerUrls["test"];
		}
	}

    public function getOrderPlaceRedirectUrl()
    {
          return Mage::getUrl('knet/process/redirect');
    }

    public function getSuccessUrl()
	{
		return Mage::getUrl('knet/process/success', array('_secure' => true));
	}

	public function getFailureUrl()
    {
        return Mage::getUrl('knet/process/failure', array('_secure' => true));
    }

    public function getCancelUrl()
    {
        return Mage::getUrl('knet/process/cancel', array('_secure' => true));
    }

    public function getIpnUrl()
    {
        return Mage::getUrl('knet/process/ipn', array('_secure' => true));
    }

    public function getResponseUrl()
    {
        return Mage::getUrl('knet/process/response', array('_secure' => true));
    }

	public function getCustomer()
    {
        if (empty($this->_customer)) {
            $this->_customer = Mage::getSingleton('customer/session')->getCustomer();
        }
        return $this->_customer;
    }

    public function getCheckout()
    {
        if (empty($this->_checkout)) {
            $this->_checkout = Mage::getSingleton('checkout/session');
        }
        return $this->_checkout;
    }

    public function getQuote()
    {
        if (empty($this->_quote)) {
            $this->_quote = $this->getCheckout()->getQuote();
        }
        return $this->_quote;
    }

    public function getOrder()
    {
        if (empty($this->_order)) {
            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($this->getCheckout()->getLastRealOrderId());
            $this->_order = $order;
        }
        return $this->_order;
    }

	public function getEmail()
	{
		$email = $this->getOrder()->getCustomerEmail();
		if (!$email) {
            $email = $this->getQuote()->getBillingAddress()->getEmail();
        }
		if (!$email) {
            $email = Mage::getStoreConfig('trans_email/ident_general/email');
        }
		return $email;
	}

	public function getOrderAmount()
	{
    	$amount = sprintf('%.2f', $this->getOrder()->getGrandTotal());
    	return $amount;
	}

	public function getOrderCurrency()
	{
		$currency = $this->getOrder()->getOrderCurrency();
        if (is_object($currency)) {
            $currency = $currency->getCurrencyCode();
        }
		return $currency;
		#return Mage::app()->getStore()->getCurrentCurrencyCode();
	}

	public function getFormFields()
	{
		$payment		= $this->getQuote()->getPayment();
		$order			= $this->getOrder();
		$formFields	    = array();

		return $formFields;
	}
}