<?php
class Shopgo_FacebookApi_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {	/*
    	$this->loadLayout();
    	$this->renderLayout();   
    	*/
    	/*
    	$app_info = array(
    			'appId'  => '125781187563696',
    			'secret' => 'd9d5a4f85419c603ef8d36d90343b2da',
    	);
    	*/
    	//$facebook = Mage::getModel('facebookapi/api_facebook', $app_info); 
    	

    	// Get User ID
    	//$user = $facebook->getUser();
    	
    	//var_dump($user);
    	

    	// We may or may not have this data based on whether the user is logged in.
    	//
    	// If we have a $user id here, it means we know the user is logged into
    	// Facebook, but we don't know if the access token is valid. An access
    	// token is invalid if the user logged out of Facebook.
    	/*
    	if ($user) {
    		try {
    			// Proceed knowing you have a logged in user who's authenticated.
    			$user_profile = $facebook->api('/me');
    		} catch (FacebookApiException $e) {
    			error_log($e);
    			$user = null;
    		}
    	}
    	
    	// Login or logout url will be needed depending on current user state.
    	if ($user) {
    		$logoutUrl = $facebook->getLogoutUrl();
    		//var_dump($logoutUrl);
    	} else {
    		$loginUrl = $facebook->getLoginUrl();
    	}
    	
    	// This call will always work since we are fetching public data.
    	//$naitik = $facebook->api('/naitik');
    	
    	$attachment =  array(
    			//'access_token' => $access_token,
    			'message' => 'test',
    			'name' => 'test',
    			'description' => 'test',
    			'link' => '',
    			'picture' => '',
    			//'actions' => array('name'=>'Try it now', 'link' => "$appUrl")
    	);
    	*/
    	/*
    	try{
    		$post_id = $facebook->api("me/feed","POST",$attachment);
    	}catch(Exception $e){
    		error_log($e->getMessage());
    	}
    	*/
    	// Get the current access token
    	//$access_token = $facebook->getAccessToken();
    	
    	//var_dump($access_token);
    	/*
    	$order = Mage::getModel('sales/order');
		$order->load(Mage::getSingleton('sales/order')->getLastOrderId());
		$lastOrderId = $order->getIncrementId();
		*/
		//var_dump($lastOrderId);
		//var_dump(Mage::getSingleton('sales/order')->getLastOrderId());
    }
    
   
    
    public function testAction()
    {
		/*
		$order = Mage::getModel('sales/order')->load(2);
		$items = $order->getAllItems();
		
		$customer_name = $order->getCustomerName();
		
		//var_dump($customer_name);
		
		foreach($items as $item){
		
			//echo $item->getName();
			$product = $item->getProduct();
			$product = Mage::getModel('catalog/product')->load($product->getId());
			//var_dump($product->getName());
			//var_dump($product->getDescription());
			var_dump((string)Mage::helper('catalog/image')->init($product, 'image')->resize(200));
			//var_dump($product->getProductUrl());
			
		}
		//var_dump($items);
		//var_dump($order->getData());
    	*/
    }


	protected function _isAllowed() {
		return true;
		//return Mage::getSingleton('admin/session')->isAllowed('admin/system/config/carriers');
	}
}
