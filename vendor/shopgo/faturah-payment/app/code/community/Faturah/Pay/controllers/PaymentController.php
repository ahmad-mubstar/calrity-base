<?php

class Faturah_Pay_PaymentController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {

        $is_active = Mage::getStoreConfig('payment/faturah/active');
        $merchant_code = Mage::getStoreConfig('payment/faturah/merchant_code');
        $test_mode = Mage::getStoreConfig('payment/faturah/sandbox_mode');

        $action_gateway = '';

        if (!$test_mode) {
            //https://gateway.faturah.com/TransactionRequestHandler.aspx
            $action_gateway = 'https://gateway.faturah.com/TransactionRequestHandler_Post.aspx';
        } else {
            //https://gatewaytest.faturah.com/TransactionRequestHandler.aspx
            $action_gateway = 'https://gatewaytest.faturah.com/TransactionRequestHandler_Post.aspx';
        }


        //var_dump($merchant_code);

        $client = new SoapClient('https://Services.faturah.com/TokenGeneratorWS.asmx?wsdl');

        $faturahParams = array('GenerateNewMerchantToken' => array("merchantCode" => $merchant_code));

        $result = $client->__soapCall('GenerateNewMerchantToken', $faturahParams);

        $tokenGUID = $result->GenerateNewMerchantTokenResult;

        //Loading current layout
        $this->loadLayout();
        //Creating a new block
        $block = $this->getLayout()->createBlock(
                'Mage_Core_Block_Template', 'test_block_name', array('template' => 'faturah/pay/redirect.phtml')
        )
        ->setData('merchant_code', $merchant_code)
        ->setData('tokenGUID', $tokenGUID)
        ->setData('action_gateway', $action_gateway);

        $this->getLayout()->getBlock('content')->append($block);

        //Now showing it with rendering of layout
        $this->renderLayout();
    }

    // The redirect action is triggered when someone places an order
    public function payAction() {

        $data = array();
        if (!$this->getRequest()->isPost()) {

            $data['error'] = 'method not allowed';
            print json_encode($data);
            return;
        }
    }

    public function responseAction() {

        /*
         * $order = Mage::getModel('sales/order')->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());
         * $order->getGrandTotal();
         *
         * */

         $error = false;
         $status = "";




         $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
         $order = Mage::getModel('sales/order')->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());

         if (!$this->getRequest()->getParam('token')) {
            $token = $this->getRequest()->getParam('token');
         }

         if ($this->getRequest()->getParam('Response') == "1")
         {
            if($this->getRequest()->getParam('ignore') == "0"){
                switch($this->getRequest()->getParam("status"))
                {
                    case '15': //**** Transaction is accepted by bank
                    case '30': //**** Transaction is approved/accepted by Faturah Team
                        $status = "approved";
                        $error = false;
                        /** trying to create invoice **/
                        try {
                            if(!$order->canInvoice()):
                            //Mage::throwException(Mage::helper('core')->__('cannot create invoice !'));
                            Mage::throwException(Mage::helper('core')->__('cannot create an invoice !'));
                        else:
                        /** create invoice  **/
                        //$invoiceId = Mage::getModel('sales/order_invoice_api')->create($order->getIncremenetId(), array());
                        $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();

                        if(!$invoice->getTotalQty()):
                            Mage::throwException(Mage::helper('core')->__('cannot create an invoice without products !'));
                        endif;

                        $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
                        $invoice->register();
                        $transactionSave = Mage::getModel('core/resource_transaction')->addObject($invoice)->addObject($invoice->getOrder());
                        $transactionSave->save();

                        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'Gateway has authorized the payment.');
                        /** load invoice **/
                        //$invoice = Mage::getModel('sales/order_invoice')->loadByIncrementId($invoiceId);
                        /** pay invoice **/
                        //$invoice->capture()->save();
                        endif;
                        }
                        catch(Mage_Core_Exception $e){
                            Mage::throwException(Mage::helper('core')->__('cannot create an invoice !'));
                        }

                        $order->sendNewOrderEmail();
                        $order->setEmailSent(true);
                        $order->save();
                        break;
                    case '1': //**** Transaction is pending on bank processing
                    case '18': //**** Transaction is pending on Faturah Team for approval/acceptance
                        $error = false;
                        $status = "pending";
                        //die('Transaction Pending');
                        $order->setState(Mage_Sales_Model_Order::STATE_PENDING, true, 'Transaction is pending on Faturah Team for approval/acceptance');
                        $order->save();
                        break;
                    case '22': //**** Transaction is rejected
                    case '6': //**** Transaction is NOT approved/accepted by Faturah Team
                        $error = true;
                        $status = "rejected";
                        //$this->delete_order($order_id);
                        //die('Transaction Rejected -> order deleted');
                        break;
                     default:
                        die('Wrong Status');
                        break;
                }

                //if(in_array($this->request->get["status"], array(15, 30, 1, 18)))
                /*if(in_array($this->getRequest()->getParam("status"), array(15, 30, 1, 18, 22, 6)))
                {

                }*/
                //die();
            }
            else if($this->getRequest()->getParam('ignore') == "1"){
                if(in_array($this->getRequest()->getParam("status"), array(15, 30, 1, 18)))
                {
                    // frontend success
                    $block = $this->getLayout()->createBlock('Mage_Core_Block_Template','faturah_payment_block',array('template' => 'faturah/pay/success.phtml'))->setData('order', $orderId);
                    $this->loadLayout()->getLayout()->getBlock('root')->setTemplate('page/2columns-left.phtml');
                    $this->loadLayout()->getLayout()->getBlock('content')->append($block);
                    $this->renderLayout();

                }
                else
                {
                    // frontend failure
                    $block = $this->getLayout()->createBlock('Mage_Core_Block_Template','faturah_payment_block',array('template' => 'faturah/pay/error.phtml'))->setData('response_text', 'Transaction is rejected');
                    $this->loadLayout()->getLayout()->getBlock('root')->setTemplate('page/2columns-left.phtml');
                    $this->loadLayout()->getLayout()->getBlock('content')->append($block);
                    $this->renderLayout();
                }


            }

        }
        elseif($this->getRequest()->getParam('Response') == "0") {

            $response_text = $this->getRequest()->getParam('ResponseText');
            $block = $this->getLayout()->createBlock('Mage_Core_Block_Template','faturah_payment_block',array('template' => 'faturah/pay/failure.phtml'))->setData('response_text', $response_text);
            //$this->loadLayout()->getLayout()->getBlock('root')->setTemplate('page/2columns-left.phtml');
            $this->loadLayout()->getLayout()->getBlock('content')->append($block);
            $this->renderLayout();
            return;


        }

        else // error occured
        {
            // There is a problem in the response we got
            $this->cancelAction();
            Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array('_secure'=>true));
        }
    }




    // The cancel action is triggered when an order is to be cancelled
    public function cancelAction() {
        if (Mage::getSingleton('checkout/session')->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());
            if ($order->getId()) {
                // Flag the order as 'cancelled' and save it
                $order->cancel()->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, 'Gateway has declined the payment.')->save();
            }
        }
    }

    public function successAction() {
        /**/
    }

    public function testAction() {

    }

}
