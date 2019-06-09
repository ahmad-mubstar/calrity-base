<?php
class Gate2play_Paymentgateway_PaymentController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {             
        try {
            $session = $this->_getCheckout();

            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($session->getLastRealOrderId());
            if (!$order->getId()) {
                Mage::throwException('No order for processing found');
            }
            
            $order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                Mage::helper('gate2play')->__('The customer was redirected to Gate2play.')
            );
            $order->save();

            $session->setGate2playQuoteId($session->getQuoteId());
            $session->setGate2playRealOrderId($session->getLastRealOrderId());
            $session->getQuote()->setIsActive(false)->save();
            $session->clear();

            $this->loadLayout();
            $this->renderLayout();
        } catch (Exception $e){
            Mage::logException($e);
            parent::_redirect('checkout/cart');
        }
    }    
    
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }     
    
    public function statusAction()
    { 
        $response = Mage::getModel('Gate2play_Paymentgateway_Model_Paymentgateway_Response')
            ->setResponseData($this->getRequest()->getParams());
        $status = $response->processStatusResponse();

        $redirect_url = Mage::getUrl('checkout/cart');
        if($status == 1) {
            try {
                $quoteId = $response->successResponse();
                $this->_getCheckout()->setLastSuccessQuoteId($quoteId);
                $redirect_url = Mage::getUrl('checkout/onepage/success');
            } catch (Mage_Core_Exception $e) {
                $this->_getCheckout()->addError($e->getMessage());
            } catch(Exception $e) {
                Mage::logException($e);
            }           
        }
        else {
            $message = $response->cancelResponse();

            $session = $this->_getCheckout();
            if ($quoteId = $session->getGate2playQuoteId()) {
                $quote = Mage::getModel('sales/quote')->load($quoteId);
                if ($quote->getId()) {
                    $quote->setIsActive(true)->save();
                    $session->setQuoteId($quoteId);
                }
            }

            $session->addError($message);     
        }

        $this->loadLayout();
        
        $layout  = $this->getLayout();
        $block = $layout->getBlock('gate2play.payment.status');
        
        $block->setRedirectURL($redirect_url);
        
        $this->renderLayout();        
    }    
    
    public function saveSessInfoAction()
    {
        $payment = $this->getRequest()->getPost();

        if (!empty($payment) && isset($payment['payment']) && !empty($payment['payment'])) {
            $payment = $payment['payment'];
            if ($payment['method']=='gate2play') {
                Mage::getSingleton('core/session')->setGate2playnfo($payment);
            }
        }

        //return;
    }

    public function unsetSessInfoAction()
    {
        if ($this->getRequest()->isPost()) {
            Mage::getSingleton('core/session')->unsetData('gate2play_info');
        }
        return;       
    }    
    
    protected function _getState()
    {
        return Mage::getSingleton('checkout/type_multishipping_state');
    }    
}
?>
