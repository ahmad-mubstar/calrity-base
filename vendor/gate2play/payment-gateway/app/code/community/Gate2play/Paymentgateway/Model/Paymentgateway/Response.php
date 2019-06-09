<?php

class Gate2play_Paymentgateway_Model_Paymentgateway_Response extends Gate2play_Paymentgateway_Model_PaymentMethod {

    const GATE2PLAY_STATUS_FAIL = 0;
    const GATE2PLAY_STATUS_SUCCESS = 1;

    protected $_responseData = array();
    protected $_order = null;
    protected $_transUniqueID = null;
    protected $_failed_msg = '';

    public function setResponseData(array $data) {
        $this->_responseData = $data;
        return $this;
    }
    
    public function getTransUniqueID(array $data) {
        return $this->_transUniqueID;
    }    

    public function processStatusResponse() {
        try {
            $result = $this->_validateResponseData();
            $msg = '';
            switch ($result) {
                case self::GATE2PLAY_STATUS_FAIL: //fail
                    $msg = $this->_failed_msg;
                    $this->_processCancel($msg);
                    break;
                case self::GATE2PLAY_STATUS_SUCCESS: //ok
                    $msg = 'Payment authorized and captured.';
                    $this->_processSale($msg);
                    break;
            }
            //return $msg;
            return $result;
        } catch (Mage_Core_Exception $e) {
            return $e->getMessage();
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return;
    }

    protected function _processCancel($msg) {
        $this->_order->cancel();
        $this->_order->addStatusToHistory(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, $msg);
        $this->_order->save();
    }

    protected function _processSale($msg) {
        //$this->_createInvoice();

        $this->_order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, $msg);
      
        $this->_order->getPayment()->setTransactionId(null)
                    ->setParentTransactionId($this->_transUniqueID)
                    ->capture(null);
                
        $this->_order->save();       
         
        // send new order email
        $this->_order->sendNewOrderEmail();
        $this->_order->setEmailSent(true);

        $this->_order->save();
    }

    protected function _validateResponseData() {
        // get request variables
        $params = $this->_responseData;
        if (empty($params)) {
            Mage::throwException('Request does not contain any elements.');
        }

        $token = $params["token"];

        $testMode = $this->getConfigData('test');
        if ($testMode == 0) {
            $url = Mage::helper('gate2play')->getStatusLiveUrl();
        } else {
            $url = Mage::helper('gate2play')->getStatusTestUrl();
        }

        $url .= $token;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $resultPayment = curl_exec($ch);
        curl_close($ch);
        $resultJson = json_decode($resultPayment, true);

        $transID = $resultJson['transaction']['identification']['transactionid'];

        // check order ID
        if (empty($transID)) {
            Mage::throwException('Missing or invalid order ID.');
        }

        $this->_order = Mage::getModel('sales/order')->loadByIncrementId($transID);
        if (!$this->_order->getId()) {
            Mage::throwException('Order not found.');
        }

        if (0 !== strpos($this->_order->getPayment()->getMethodInstance()->getCode(), 'paymentgateway')) {
            Mage::throwException('Unknown payment method.');
        }

        if (strstr($resultJson['transaction']['processing']['result'], "ACK")) {
            $this->_transUniqueID = $resultJson['transaction']['identification']['uniqueId'];
            return 1;
        } else {
            $this->_failed_msg = $resultJson['transaction']['processing']['return']['message'];
            return 0;
        }

        return $params;
    }

    protected function _getCheckout() {
        return Mage::getSingleton('checkout/session');
    }

    public function cancelResponse() {
        return $this->_failed_msg;
    }

    public function successResponse() {
        return $this->_order->getQuoteId();
    }

    protected function _createInvoice() {
        if (!$this->_order->canInvoice()) {
            return;
        }
        $invoice = $this->_order->prepareInvoice();

        $this->_order->addRelatedObject($invoice);
    }

}

?>
