<?php
require_once 'Mage/Checkout/controllers/OnepageController.php';
class Shopgo_EC_IndexController extends Mage_Checkout_OnepageController {

    protected function _ajaxRedirectResponse() {
        $this->getResponse()
                ->setHeader('HTTP/1.1', '403 Session Expired')
                ->setHeader('Login-Required', 'true')
                ->sendResponse();
        return $this;
    }

    protected function _expireAjax() {
        if (!$this->getOnepage()->getQuote()->hasItems() || $this->getOnepage()->getQuote()->getHasError() || $this->getOnepage()->getQuote()->getIsMultiShipping()) {
            $this->_ajaxRedirectResponse();
            return true;
        }
        $action = $this->getRequest()->getActionName();
        if (Mage::getSingleton('checkout/session')->getCartWasUpdated(true) && !in_array($action, array('index'))) {
            $this->_ajaxRedirectResponse();
            return true;
        }

        return false;
    }

    protected function _filterPostData($data) {
        $data = $this->_filterDates($data, array('dob'));
        return $data;
    }

    public function indexAction() {
        if (!Mage::helper('ec')->isActive()) {
            $this->_redirect('checkout/onepage');
            return;
        }

        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->_redirect('checkout/cart');
            return;
        }

        if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message');
            Mage::getSingleton('checkout/session')->addError($error);
            $this->_redirect('checkout/cart');
            return;
        }

        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
        	if(!Mage::getStoreConfig('ec/general/guest_checkout')) {
        		$this->getOnepage()->saveCheckoutMethod(Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER);
        	} else {
        		$this->getOnepage()->saveCheckoutMethod(Mage_Sales_Model_Quote::CHECKOUT_METHOD_GUEST);
        	}
        }

        if (!count(Mage::getSingleton('customer/session')->getCustomer()->getAddresses())) {
            $defaultCountry = Mage::getStoreConfig('general/country/default');
            if (!$quote->getBillingAddress()->getCountryId()) {
                $quote->getBillingAddress()->setCountryId($defaultCountry)->save();
            }

            if (!$quote->getShippingAddress()->getCountryId()) {
                $quote->getShippingAddress()->setCountryId($defaultCountry)->save();
            }
        }

        Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
        Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_secure' => true)));
        $this->getOnepage()->initCheckout();
        //save default Zip/Postal Code
        if ($defaultPostcode = Mage::helper('ec')->defaultZipCode()) {
            if (!$quote->getBillingAddress()->getPostcode()) {
                $quote->getBillingAddress()->setPostcode($defaultPostcode)->save();
            }
            if (!$quote->getShippingAddress()->getPostcode()) {
                $quote->getShippingAddress()->setPostcode($defaultPostcode)->save();
            }
        }
        //save default shipping method if the shipping method of cart is null
        if ($defaultShippingMethod = Mage::helper('ec')->defaultShippingMethod()) {
            if (!$quote->getShippingAddress()->getShippingMethod()) {
                $quote->getShippingAddress()->setShippingMethod($defaultShippingMethod)->save();
                $this->getOnepage()->saveShippingMethod($defaultShippingMethod);
            }
        }
        //save default payment method if the payment method of cart is null
        if ($defaultPaymentMethod = Mage::helper('ec')->defaultPaymentMethod()) {
            if (!$quote->getPayment()->getMethod()) {
                $quote->getPayment()->setMethod($defaultPaymentMethod)->save();
                $quote->collectTotals()->save();
            }
        }
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        //$this->getLayout()->getBlock('head')->setTitle($this->__('One Step Checkout'));
        $this->renderLayout();
    }

    public function loginPostAction() {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }

        $session = Mage::getSingleton('customer/session');
        $message = '';
        $result = array();

        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');
            if (!empty($login['email']) && !empty($login['password'])) {
                try {
                    $session->login($login['email'], $login['password']);
                    if ($session->getCustomer()->getIsJustConfirmed()) {
                        $this->_welcomeCustomer($session->getCustomer(), true);
                    }
                } catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $message = Mage::helper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', Mage::helper('customer')->getEmailConfirmationUrl($login['email']));
                            break;
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $message = $e->getMessage();
                            break;
                        default:
                            $message = $e->getMessage();
                    }
                    $session->setUsername($login['email']);
                } catch (Exception $e) {
                    $message = $e->getMessage();
                }
            } else {
                $message = $this->__('Login and password are required');
            }
        }

        if ($message) {
            $result['error'] = true;
            $result['message'] = $message;
        } else {
            $result['redirect'] = 1;
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function reloadReviewAction() {
        if ($this->_expireAjax()) {
            return;
        }

        $result = array();
        try {
            $result['review'] = $this->_getReviewHtml();
        } catch (Exception $e) {
        	$result['error'] = true;
            $result['message'] = $e->getMessage();
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function switchMethodAction() {
        if ($this->_expireAjax()) {
            return;
        }

        $method = $this->getRequest()->getPost('method');
        if ($this->getRequest()->isPost() && $method)
            $this->getOnepage()->saveCheckoutMethod($method);
    }

    public function reloadPaymentAction() {
        if ($this->_expireAjax()) {
            return;
        }

        $result = array();
        try {
            $result['payment'] = $this->_getPaymentMethodsHtml();
        } catch (Exception $e) {
        	$result['error'] = true;
            $result['message'] = $e->getMessage();
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function getAddressAction() {
        if ($this->_expireAjax()) {
            return;
        }

        $addressId = $this->getRequest()->getParam('address', false);

        if ($addressId) {
            $address = $this->getOnepage()->getAddress($addressId);

            if (Mage::getSingleton('customer/session')->getCustomer()->getId() == $address->getCustomerId()) {
                $this->getResponse()->setHeader('Content-type', 'application/x-json');
                $this->getResponse()->setBody($address->toJson());
            } else {
                $this->getResponse()->setHeader('HTTP/1.1', '403 Forbidden');
            }
        } else {
            $data = array();
            $this->getResponse()->setBody(json_encode($data));
        }
    }

    /**
     * save checkout billing address
     */
    public function saveBillingAction() {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
        	
            $data = $this->getRequest()->getPost('billing', array());

            $data = $this->_filterPostData($data);

            //
            $data['month'] = '12';
            $data['day'] = '12';
            $data['year'] = '1980';
            $data['dob'] = '12/12/1980';
            $data['gender'] = '123';
            $data['taxvat'] = '123';

            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);

            if (isset($data['email'])) {
                $data['email'] = trim($data['email']);
            }
            
            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);
            
            if (!isset($result['error'])) {
                /* check quote for virtual */
                if ($this->getOnepage()->getQuote()->isVirtual()) {
                    $result['goto_section'] = 'payment';
                    $result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
                    );
                } elseif (isset($data['use_for_shipping'])) {
                    $result['goto_section'] = 'shipping_method';
                    $result['update_section'] = array(
                        'name' => 'shipping-method',
                        'html' => $this->_getShippingMethodsHtml()
                    );

                    $result['allow_sections'] = array('shipping');
                    $result['duplicateBillingInfo'] = 'true';
                } else {
                    $result['goto_section'] = 'shipping';
                }
            }

            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    public function saveShippingAction() 
    {
        if ($this->_expireAjax()) {
            return;
        }
        Mage::getSingleton('core/session')->setData('use_for_shipping', true);
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping', array());
            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            $result = $this->getOnepage()->saveShipping($data, $customerAddressId);

            if (!isset($result['error'])) {
                $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
                $result['shippingMethod'] = $this->_getShippingMethodsHtml();
                $result['payment'] = $this->_getPaymentMethodsHtml();
            }
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    public function saveShippingMethodAction() 
    {
        if ($this->_expireAjax()) {
            return;
        }
        $result = array();
        $data = $this->getRequest()->getPost();
        if ($data) {
            try {
                $return = $this->getOnepage()->saveShippingMethod($data['shipping_method']);
                if (!$return) {
                    Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method', array('request' => $this->getRequest(), 'quote' => $this->getOnepage()->getQuote()));
                    //this is so important in order to update shipping method
                    $this->getOnepage()->getQuote()->collectTotals()->save();
                }
                $result['payment'] = $this->_getPaymentMethodsHtml();
                $result['review'] = $this->_getReviewHtml();
            } catch (Exception $e) {
            	$result['error'] = true;
                $result['message'] = $e->getMessage();
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Save payment ajax action
     *
     * Sets either redirect or a JSON response
     */
    public function savePaymentAction() {
        if ($this->_expireAjax()) {
          return;
       	}
       	try {
        	if (!$this->getRequest()->isPost()) {
          		$this->_ajaxRedirectResponse();
          		return;
          	}

          	// set payment to quote
          	$result = array();
          	$data = $this->getRequest()->getPost('payment', array());
          	$result = $this->getOnepage()->savePayment($data);

          	// get section and redirect data
          	$redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
          	// if (empty($result['error']) && !$redirectUrl) {
          		// $this->loadLayout('checkout_onepage_review');
          		// $result['goto_section'] = 'review';
          		// $result['update_section'] = array(
          		// 	'name' => 'review',
          		// 	'html' => $this->_getReviewHtml()
          		// );
          	// }
            $result['goto_section'] = 'review';
            $result['update_section'] = array(
                'name' => 'review',
                'html' => $this->_getReviewHtml()
            );
          	if ($redirectUrl) {
          		$result['redirect'] = $redirectUrl;
          	}
		} catch (Mage_Payment_Exception $e) {
          	if ($e->getFields()) {
          		$result['fields'] = $e->getFields();
          	}
          	$result['error'] = true;
          	$result['message'] = $e->getMessage();
      	} catch (Mage_Core_Exception $e) {
      		$result['error'] = true;
          	$result['message'] = $e->getMessage();
       	} catch (Exception $e) {
          	Mage::logException($e);
          	$result['error'] = true;
          	$result['message'] = $this->__('Unable to set Payment Method.');
      	}
      	$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result)); 
    }

    public function couponAction() {
        $result = array();
        if (!$this->getQuote()->getItemsCount()) {
            $result['redirect'] = Mage::getUrl('checkout/cart');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            return;
        }

        $couponCode = (string) $this->getRequest()->getParam('coupon_code');
        if ($this->getRequest()->getParam('remove') == 1) {
            $result['coupon'] = 'remove';
            $couponCode = '';
        } else {
            $result['coupon'] = 'add';
        }

        $oldCouponCode = $this->getQuote()->getCouponCode();

        if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            return;
        }

        try {
            $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
                    ->collectTotals()
                    ->save();

            if ($couponCode) {
                if ($couponCode != $this->getQuote()->getCouponCode()) {
                	$result['error'] = true;
                    $result['message'] = $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode));
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                    return;
                }
            }
            $result['review'] = $this->_getReviewHtml();
        } catch (Mage_Core_Exception $e) {
        	$result['error'] = true;
            $result['message'] = $e->getMessage();
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        } catch (Exception $e) {
        	$result['error'] = true;
            $result['message'] = $this->__('Can not apply coupon code.');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    //Update 1.4.1.4
    public function updateQtyAction() {
        $result = array();
        try {
            $this->_getSession()->setCartWasUpdated(true);
            $cartData = $this->getRequest()->getParam('cart');
            if (is_array($cartData)) {

                /*
                 * Zend_Filter_LocalizedToNormalized filter will break the update action in the arabic store view.
                 */
                /*
                  $filter = new Zend_Filter_LocalizedToNormalized(
                  array('locale' => Mage::app()->getLocale()->getLocaleCode())
                  );
                  foreach ($cartData as $index => $data) {
                  if (isset($data['qty'])) {
                  $cartData[$index]['qty'] = $filter->filter($data['qty']);
                  }
                  }
                 */
                $cart = $this->_getCart();
                if (!$cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
                    $cart->getQuote()->setCustomerId(null);
                }
                $cart->updateItems($cartData)
                        ->save();
            }
            $this->_getSession()->setCartWasUpdated(false);
            if (!$cart->getItemsQty()) {
                $result['redirect'] = Mage::getUrl('checkout/cart');
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                return;
            }
            $result['totalQty'] = $cart->getItemsQty();
        } catch (Mage_Core_Exception $e) {
        	$result['error'] = true;
            $result['message'] = $e->getMessage();
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            return;
        } catch (Exception $e) {
        	$result['error'] = true;
            $result['message'] = $this->__('Cannot update shopping cart.');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            return;
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
    
    /**
     * Create order action
     */
    public function saveOrderAction()
    {
    	if ($this->_expireAjax()) {
    		return;
    	}
    
    	$result = array();

    	try {
    		if ($requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds()) {
    			$postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
    			if ($diff = array_diff($requiredAgreements, $postedAgreements)) {
    				$result['success'] = false;
    				$result['error'] = true;
    				$result['message'] = $this->__('Please agree to all the terms and conditions before placing the order.');
    				$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    				return;
    			}
    		}

    		if ($data = $this->getRequest()->getPost('payment', false)) {
    			$this->getOnepage()->getQuote()->getPayment()->importData($data);
    		}
            
            // Handle Paypal express redirect.
            if($this->getOnepage()->getQuote()->getPayment()->getMethod() == 'paypal_express') {
                //
                $result['redirect'] = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
                $result['success'] = true;
                $result['error']   = false;
                // $result['message'] = $this->__('');
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                return;
            }
            
            $this->getOnepage()->saveOrder();

            $redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();
            
            // Add the comment and save the order
            $lastOrderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
            $order = Mage::getModel('sales/order')->load($lastOrderId, 'increment_id');
    		
    		$order->addStatusToHistory($order->getStatus(), 'Shipping date: ' . $this->getRequest()->getParam('deliveryDate'), false);
    		$order->save();
            
            $result['order_id'] = $lastOrderId;
    		$result['success'] = true;
    		$result['error']   = false;
    	} catch (Mage_Payment_Model_Info_Exception $e) {
    		$message = $e->getMessage();
    		if( !empty($message) ) {
    			$result['message'] = $message;
    		}
    		$result['goto_section'] = 'payment';
    		$result['update_section'] = array(
    				'name' => 'payment-method',
    				'html' => $this->_getPaymentMethodsHtml()
    		);
    	} catch (Mage_Core_Exception $e) {
    		Mage::logException($e);
    		Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
    		$result['success'] = false;
    		$result['error'] = true;
    		$result['message'] = $e->getMessage();
    
    		if ($gotoSection = $this->getOnepage()->getCheckout()->getGotoSection()) {
    			$result['goto_section'] = $gotoSection;
    			$this->getOnepage()->getCheckout()->setGotoSection(null);
    		}
    
    		if ($updateSection = $this->getOnepage()->getCheckout()->getUpdateSection()) {
    			if (isset($this->_sectionUpdateFunctions[$updateSection])) {
    				$updateSectionFunction = $this->_sectionUpdateFunctions[$updateSection];
    				$result['update_section'] = array(
    						'name' => $updateSection,
    						'html' => $this->$updateSectionFunction()
    				);
    			}
    			$this->getOnepage()->getCheckout()->setUpdateSection(null);
    		}
    	} catch (Exception $e) {
    		Mage::logException($e);
            Mage::log($e->getMessage(), null, 'shopgo_easycheckout.log');
    		Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
    		$result['success']  = false;
    		$result['error']    = true;
    		$result['message'] = $this->__('There was an error processing your order. Please contact us or try again later.');
    	}
    	$this->getOnepage()->getQuote()->save();
    	/**
    	 * when there is redirect to third party, we don't want to save order yet.
    	 * we will save the order in return action.
    	*/
    	if (isset($redirectUrl)) {
    		$result['redirect'] = $redirectUrl;
    	}
    	
    	//
    	
    	$lastOrderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
    	$order = Mage::getModel('sales/order')->load($lastOrderId, 'increment_id');
    	
    	//
    	
    	$EcCheckoutData = array(
    		'post_data' => $this->getRequest()->getPost(),
    		'order_data' => $order->getData()
    	);
    	
    	
    	$EcData = array(
    		'url' => Mage::getBaseUrl(),
    		'browser' => $_SERVER['HTTP_USER_AGENT'],
    		'device' => $_SERVER['HTTP_USER_AGENT'],
   			'data' => json_encode($EcCheckoutData)
    	);
    	
    	
    	$client = new Varien_Http_Client('http://litegrid.devshopgo.me/index.php/api/post');
    	$client->setMethod(Varien_Http_Client::POST);
    	$client->setParameterPost('EcData', $EcData);
       
       	//more parameters
       	try{
       		$response = $client->request();
    	   	if ($response->isSuccessful()) {
           		$data = $response->getBody();
    		}
    	} catch (Exception $e) {
    		//
    	}
    
    	$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    protected function _getShippingMethodsHtml() {
    	// Clear layout cache
    	Mage::app()->getCacheInstance()->cleanType('layout');
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_shippingmethod');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }

    protected function _getPaymentMethodsHtml() {
    	// Clear layout cache
    	Mage::app()->getCacheInstance()->cleanType('layout');
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_paymentmethod');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }

    protected function _getReviewHtml() {
        // Clear layout cache
        Mage::app()->getCacheInstance()->cleanType('layout');
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_review');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
        //return $this->getLayout()->createBlock('checkout/onepage_review_info')->setTemplate('shopgo/ec/review/info.phtml')->toHtml();
    }

    public function getOnepage() {
        return Mage::getSingleton('checkout/type_onepage');
    }

    public function getCheckout() {
        return $this->getOnepage()->getCheckout();
    }

    public function getQuote() {
        return $this->getCheckout()->getQuote();
    }

    //update on version 1.4.1.4
    protected function _getSession() {
        return Mage::getSingleton('checkout/session');
    }

    protected function _getCart() {
        return Mage::getSingleton('checkout/cart');
    }

    /**
     * Check can page show for unregistered users
     *
     * @return boolean
     */
    protected function _canShowForUnregisteredUsers()
    {
        return true;
        // return Mage::getSingleton('customer/session')->isLoggedIn()
        //     || $this->getRequest()->getActionName() == 'index'
        //     || $this->getRequest()->getActionName() == 'reloadPayment'
        //     || $this->getRequest()->getActionName() == 'reloadReview'
        //     || $this->getRequest()->getActionName() == 'savePayment'
        //     || $this->getRequest()->getActionName() == 'saveShipping'
        //     || $this->getRequest()->getActionName() == 'saveBilling'
        //     || Mage::helper('checkout')->isAllowedGuestCheckout($this->getOnepage()->getQuote())
        //     || !Mage::helper('checkout')->isCustomerMustBeLogged();
    }
}
