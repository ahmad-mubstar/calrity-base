<?php

class Cashu_Prepaid_PaymentController extends Mage_Core_Controller_Front_Action
{
    
    public function getConfigData($key)
    {
        return Mage::getStoreConfig('payment/cashu_prepaid/' . $key);
    }
    
    public function redirectAction()
    {
        $cashu['order']    = new Mage_Sales_Model_Order();
        $cashu['order_id'] = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        $cashu['order']->loadByIncrementId($cashu['order_id']);
        
        $cashu['display_text'] = '';
        $items                 = $cashu['order']->getAllItems();
        foreach ($items as $item)
        {
            $cashu['display_text'] .= $item->getName();
        }
        
        $cashu['encryption_keyword'] = $this->getConfigData('encryption_keyword');
        $cashu['testmode']           = $this->getConfigData('test');
        
        if ($cashu['testmode'])
        {
            $cashu['payment_gateway_url'] = 'https://sandbox.cashu.com/cgi-bin/payment/pcashu.cgi';
        }
        else
        {
            $cashu['payment_gateway_url'] = 'https://www.cashu.com/cgi-bin/payment/pcashu.cgi';
        }
        
        $cashu['language']    = substr(Mage::app()->getLocale()->getLocaleCode(), 0, 2);
        $cashu['currency']    = strtolower($cashu['order']->getOrderCurrencyCode());
        $cashu['amount']      = round($cashu['order']->getBaseGrandTotal(), 2);
        $cashu['merchant_id'] = $this->getConfigData('merchant_id');
        $cashu['token']       = md5(strtolower($cashu['merchant_id']) . ':' . $cashu['amount'] . ':' . $cashu['currency'] . ':' . $cashu['order_id'] . ':' . $cashu['encryption_keyword']);
        $cashu['session_id']  = $cashu['txt1'] = $cashu['order_id'];
        $cashu['txt2']        = $cashu['txt3'] = $cashu['txt4'] = $cashu['txt5'] = $cashu['service_name'] = '';
        
        //ini_set("soap.wsdl_cache_enabled", "0");
        //ini_set('user_agent', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0');
        
        if ($cashu['testmode'])
        {
            $client = new SoapClient("https://sandbox.cashu.com/secure/payment.wsdl", array(
                'trace' => true
            ));
        }
        else
        {
            $client = new SoapClient("https://secure.cashu.com/payment.wsdl", array(
                'trace' => true
            ));
        }
        
        $request                   = $client->DoPaymentRequest($cashu['merchant_id'], $cashu['token'], $cashu['display_text'], $cashu['currency'], $cashu['amount'], $cashu['language'], $cashu['session_id'], $cashu['txt1'], $cashu['txt2'], $cashu['txt3'], $cashu['txt4'], $cashu['txt5'], $cashu['testmode'], $cashu['service_name']);
        $tmp                       = strstr($request, '=');
        $cashu['transaction_code'] = substr($tmp, 1);
        
        Mage::register('cashu', $cashu);
        
        $this->loadLayout();
        $block = $this->getLayout()->createBlock('Mage_Core_Block_Template', 'cashu_prepaid', array(
            'template' => 'cashu/prepaid/redirect.phtml'
        ));
        $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
    }
    
    
    public function responseAction()
    {
        if ($this->getRequest()->isPost())
        {
            $merchant_id        = $this->getConfigData('merchant_id');
            $encryptionKey      = $this->getConfigData('encryption_keyword');
            $token              = $this->getRequest()->getPost('token');
            $trn_id             = $this->getRequest()->getPost('trn_id');
            $session_id         = $this->getRequest()->getPost('session_id');
            $verificationString = $this->getRequest()->getPost('verificationString');
            
            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($session_id);
            
            $originalAmount               = round($order->getBaseGrandTotal(), 2);
            $originalCurrency             = $order->getOrderCurrencyCode();
            $calculatedVerificationString = sha1(strtolower($merchant_id) . ':' . $trn_id . ':' . $encryptionKey);
            $calculatedtoken              = md5(strtolower($merchant_id) . ':' . $originalAmount . ':' . strtolower($originalCurrency) . ':' . $session_id . ':' . $encryptionKey);
            
            if ($calculatedVerificationString != $verificationString)
            {
                Mage::log('CashU: This request is not from CashU side, please contact your store owner for a refund. Error Code #01. Cancelled Order ID: ' . $session_id);
                Mage::getSingleton('checkout/session')->setErrorMessage('This request is not from CASHU side, please contact your store owner for a refund. Error Code #01');
                $this->cancelAction();
                Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array(
                    '_secure' => true
                ));
            }
            else if ($calculatedtoken != $token)
            {
                Mage::log('CashU: This request is not from CashU side, please contact your store owner for a refund. Error Code #02. Cancelled Order ID: ' . $session_id);
                Mage::getSingleton('checkout/session')->setErrorMessage('This request is not from CASHU side, please contact your store owner for a refund. Error Code #02');
                $this->cancelAction();
                Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array(
                    '_secure' => true
                ));
            }
            else
            {
                //Mage::log('CashU: Success Response/Order ID: ' . $session_id);
                $this->_redirect('checkout/onepage/success', array(
                    '_secure' => true
                ));
                $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'CashU has authorized the payment.');
                $order->sendNewOrderEmail();
                $order->setEmailSent(true);
                $order->save();
            }
        }
        else
            Mage_Core_Controller_Varien_Action::_redirect('');
    }
    
    
    public function notificationAction()
    {
        $encryptionKey      = $this->getConfigData('encryption_keyword');
        $isSecure           = Mage::app()->getStore()->isCurrentlySecure();
        $testmode           = $this->getConfigData('test');
        $sRequest           = $this->getRequest()->getPost('sRequest');
        $successTransaction = new SimpleXMLElement($sRequest);
        $merchant_id        = $successTransaction->merchant_id;
        $token              = $successTransaction->token;
        $cashU_trnID        = $successTransaction->cashU_trnID;
        $session_id         = $successTransaction->session_id;
        $cashUToken         = $successTransaction->cashUToken;
        $responseCode       = $successTransaction->responseCode;
        
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($session_id);
        
        $originalAmount       = round($order->getBaseGrandTotal(), 2);
        $originalCurrency     = $order->getOrderCurrencyCode();
        $calculatedCashuToken = md5(strtolower($merchant_id) . ':' . $cashU_trnID . ':' . strtolower($encryptionKey));
        $calculatedtoken      = md5(strtolower($merchant_id) . ':' . $originalAmount . ':' . strtolower($originalCurrency) . ':' . $session_id . ':' . $encryptionKey);
        
        if ($isSecure)
        {
            if ($calculatedCashuToken != $cashUToken)
            {
                Mage::log('CashU: This request is not from CashU side, please contact your store owner for a refund. Error Code #03. Cancelled Order ID: ' . $session_id);
                Mage::getSingleton('checkout/session')->setErrorMessage('This request is not from CASHU side, please contact your store owner for a refund. Error Code #03');
                $this->cancelAction();
                Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array(
                    '_secure' => true
                ));
            }
            else if ($calculatedtoken != $token)
            {
                Mage::log('CashU: This request is not from CashU side, please contact your store owner for a refund. Error Code #04. Cancelled Order ID: ' . $session_id);
                Mage::getSingleton('checkout/session')->setErrorMessage('This request is not from CASHU side, please contact your store owner for a refund. Error Code #04');
                $this->cancelAction();
                Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array(
                    '_secure' => true
                ));
            }
            else
            {
                //Mage::log('CashU: Success Notification/Order ID: ' . $session_id);
                $this->_redirect('checkout/onepage/success', array(
                    '_secure' => true
                ));
                $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'CashU has authorized the payment.');
                $order->sendNewOrderEmail();
                $order->setEmailSent(true);
                $order->save();
            }
        }
        else
        {
            Mage::log('CashU: You must use HTTPS in your store and/or CashU Notification URL');
            Mage::getSingleton('checkout/session')->setErrorMessage('You must use HTTPS in your store and/or CashU Notification URL');
            $this->cancelAction();
            Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array(
                '_secure' => true
            ));
        }
        
        if ($responseCode == 'OK')
        {
            $sRequest = "sRequest=<cashUTransaction><merchant_id>" . $merchant_id . "</merchant_id><cashU_trnID>" . $cashU_trnID . "</cashU_trnID><cashUToken>" . $cashUToken . "</cashUToken><responseCode>" . $responseCode . "</responseCode><responseDate>" . date("Y-m-d H:i:s") . "</responseDate></cashUTransaction>";
            $ch       = curl_init();
            if ($testmode)
            {
                curl_setopt($ch, CURLOPT_URL, 'https://sandbox.cashu.com/cgi-bin/notification/MerchantFeedBack.cgi');
            }
            else
            {
                curl_setopt($ch, CURLOPT_URL, 'https://www.cashu.com/cgi-bin/notification/MerchantFeedBack.cgi');
            }
            curl_setopt($ch, CURLOPT_VERBOSE, 0);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $sRequest);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Connection: close'
            ));
            $result = curl_exec($ch);
            curl_close($ch);
        }
    }
    
    
    public function sorryAction()
    {
        $errorCode  = $this->getRequest()->getPost('errorCode');
        $txt1       = $this->getRequest()->getPost('txt1');
        $session_id = $this->getRequest()->getPost('session_id');
        
        switch ($errorCode)
        {
            case 2:
                Mage::log('CashU: Inactive Merchant ID');
                Mage::getSingleton('checkout/session')->setErrorMessage('CashU: Inactive Merchant ID');
                $this->cancelAction();
                Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array(
                    '_secure' => true
                ));
                break;
            
            case 4:
                Mage::log('CashU: Inactive Merchant Account');
                Mage::getSingleton('checkout/session')->setErrorMessage('CashU: Inactive Merchant Account');
                $this->cancelAction();
                Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array(
                    '_secure' => true
                ));
                break;
            
            case 6:
                Mage::log('CashU: Insufficient funds');
                Mage::getSingleton('checkout/session')->setErrorMessage('CashU: Insufficient funds');
                $this->cancelAction();
                Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array(
                    '_secure' => true
                ));
                break;
            
            case 7:
                Mage::log('CashU: Incorrect Merchant account details');
                Mage::getSingleton('checkout/session')->setErrorMessage('CashU: Incorrect Merchant account details');
                $this->cancelAction();
                Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array(
                    '_secure' => true
                ));
                break;
            
            case 8:
                Mage::log('CashU: Invalid Merchant account');
                Mage::getSingleton('checkout/session')->setErrorMessage('CashU: Invalid Merchant account');
                $this->cancelAction();
                Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array(
                    '_secure' => true
                ));
                break;
            
            case 15:
                Mage::log('CashU: Merchant Account password has expired');
                Mage::getSingleton('checkout/session')->setErrorMessage('CashU: Merchant Account password has expired');
                $this->cancelAction();
                Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array(
                    '_secure' => true
                ));
                break;
            
            case 17:
                Mage::log('CashU: Failed transaction');
                Mage::getSingleton('checkout/session')->setErrorMessage('CashU: Failed transaction');
                $this->cancelAction();
                Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array(
                    '_secure' => true
                ));
                break;
            
            case 20:
                Mage::log('CashU: Merchant has limited CashU sales to some countries, your country is not allowed to pay via CashU');
                Mage::getSingleton('checkout/session')->setErrorMessage('CashU: Merchant has limited CashU sales to some countries, your country is not allowed to pay via CashU');
                $this->cancelAction();
                Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array(
                    '_secure' => true
                ));
                break;
            
            case 21:
                Mage::log('CashU: Transaction value is more than the limit. This limitation is applied to payment accounts that do not comply with KYC rules');
                Mage::getSingleton('checkout/session')->setErrorMessage('CashU: Transaction value is more than the limit. This limitation is applied to payment accounts that do not comply with KYC rules');
                $this->cancelAction();
                Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array(
                    '_secure' => true
                ));
                break;
            case 22:
                
                Mage::log('CashU: The Merchant has limited his sales to only KYC-compliant payment accounts; the purchase attempt is coming from a Payment account that is NOT KYC-compliant');
                Mage::getSingleton('checkout/session')->setErrorMessage('CashU: The Merchant has limited his sales to only KYC-compliant payment accounts; the purchase attempt is coming from a Payment account that is NOT KYC-compliant');
                $this->cancelAction();
                Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array(
                    '_secure' => true
                ));
                break;
            
            case 23:
            case 70:
                Mage::log('CashU: The transaction has been cancelled by the customer');
                Mage::getSingleton('checkout/session')->setErrorMessage('CashU: The transaction has been cancelled by the customer');
                $this->cancelAction();
                Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array(
                    '_secure' => true
                ));
                break;
            
            default:
                Mage::log('CashU: Unknown Error/Sorry Case');
                Mage::getSingleton('checkout/session')->setErrorMessage('CashU: Unknown Error/Sorry Case');
                $this->cancelAction();
                Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array(
                    '_secure' => true
                ));
                break;
        }
    }
    
    public function cancelAction()
    {
        if (Mage::getSingleton('checkout/session')->getLastRealOrderId())
        {
            $order = Mage::getModel('sales/order')->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());
            if ($order->getId())
            {
                $order->cancel()->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, 'CashU has declined the payment.')->save();
            }
        }
    }
    
    
}