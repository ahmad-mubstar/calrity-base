<?php

class Shopgo_FacebookApi_Model_Observer {
	
	public function __construct(){
		
	}
	
	public function placeOrder($observer)
	{
		if(Mage::getStoreConfig('shopgo_simplesharer/facebook/share_orders') && !Mage::helper('facebookapi')->isAdmin()):
	
		$order = $observer->getEvent()->getOrder();
	
		$customer_name = $order->getCustomer()->getName();
	
		$items = $order->getAllItems();
	
		$customer_name = $order->getCustomerName();
	
		$appId = Mage::getStoreConfig('shopgo_simplesharer/facebook/appid');
		$appSecret = Mage::getStoreConfig('shopgo_simplesharer/facebook/secret');
	
		if( empty($appId) || empty($appSecret) )
			return;
	
		$app_info = array(
				'appId'  => $appId,
				'secret' => $appSecret,
		);
			
		$facebook = Mage::getModel('facebookapi/api_facebook', $app_info);
	
		// Get the current access token
		$access_token = $facebook->getAccessToken();
	
		// Get User ID
		$user = $facebook->getUser();
		if(!$user)
			return;
	
		foreach($items as $item){
	
			//echo $item->getName();
			$product = $item->getProduct();
			$product = Mage::getModel('catalog/product')->load($product->getId());
				
			$pname = $product->getName();
			$pdesc = $product->getDescription();
			$pimage = (string)Mage::helper('catalog/image')->init($product, 'image')->resize(200);
			$purl = $product->getProductUrl();
				
			$attachment =  array(
					'access_token' => $access_token,
					//'message' => $customer_name . ' has just ordered ' . $pname,
					'message' => 'I just purchased ' . $pname . ' from ' . Mage::getStoreConfig('general/store_information/name'),
					'name' => $pname,
					'description' => $pdesc,
					'link' => $purl,
					'picture' => $pimage,
					//'actions' => array('name'=>'Try it now', 'link' => "$appUrl")
			);
	
			try{
				$post_id = $facebook->api("me/feed","POST",$attachment);
			}catch(Exception $e){
				error_log($e->getMessage());
			}
				
		}
		endif;
	}
}