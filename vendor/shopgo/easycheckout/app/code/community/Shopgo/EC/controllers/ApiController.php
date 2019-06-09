<?php

ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 0);

class Shopgo_EC_ApiController extends Mage_Checkout_Controller_Action {

    public function indexAction() {
        $this->_redirect();
        return;
    }

    public function fetchCitiesAction() {

        if(!$this->getRequest()->isPost()) {
            $this->_redirect();
            return;
        }

        if(!$this->getRequest()->isXmlHttpRequest()) {
            $this->_redirect();
            return;
        }
        
        $cache = Mage::app()->getCache();

        $CountryCode = $this->getRequest()->getParam('countryCode');
        $NameStartsWith = $this->getRequest()->getParam('nameStartsWith');
        $State = $this->getRequest()->getParam('state');
        
        $regionModel = Mage::getModel('directory/region')->load($State);
        $regionCode = $regionModel->getCode();
        
        if($regionCode) $State = $regionCode;

        if(empty($CountryCode))
            $CountryCode = Mage::getModel('geo/country')->getCountry();
        
        //
        if($CountryCode != 'US')
        	$State = '';

        $wsdlPath = Mage::getModuleDir('etc', 'Shopgo_EC') . DS . 'wsdl';
        $wsdl = $wsdlPath . DS . 'Location-API-WSDL.wsdl';

        $soapClient = new SoapClient($wsdl);

        $params = array();

        if(Mage::helper('core')->isModuleEnabled('Shopgo_AramexShipping') && Mage::getStoreConfig('ec/address/cities_data_source') == 'aramex_api_shipping_module') {
            $aramexAccount = Mage::helper('aramexshipping')->getClientInfo();
            $aramexAccount['Version'] = 'v1.0';
            $aramexAccount['Source'] = NULL;
            $params = array(
                'ClientInfo'            => $aramexAccount,
                'Transaction'           => array(
                    'Reference1'            => '001',
                    'Reference2'            => '002',
                    'Reference3'            => '003',
                    'Reference4'            => '004',
                    'Reference5'            => '005'
                ),
                'CountryCode'           => $CountryCode,
                'State'             => $State,
                'NameStartsWith'        => $NameStartsWith
            );
        } else {
            $params = array(
                'ClientInfo'    => array(
                    'AccountCountryCode'        => 'JO',
                    'AccountEntity'         => 'AMM',
                    'AccountNumber'         => '20016',
                    'AccountPin'            => '331421',
                    'UserName'          => Mage::getStoreConfig('ec/address/aramex_username'),
                    'Password'          => Mage::getStoreConfig('ec/address/aramex_password'),
                    'Version'           => 'v1.0',
                    'Source'            => NULL         
                ),
                'Transaction'           => array(
                    'Reference1'            => '001',
                    'Reference2'            => '002',
                    'Reference3'            => '003',
                    'Reference4'            => '004',
                    'Reference5'            => '005'
                ),
                'CountryCode'       => $CountryCode,
                'State'             => $State,
                'NameStartsWith'    => $NameStartsWith
            );   
        }
        
        

        // calling the method and printing results
        try {
        	$cache_data = $cache->load(hash('sha1', $CountryCode . '_' . $State . '_' . $NameStartsWith));
        	if($cache_data) {
        		echo json_encode(array(
        				'error' => false,
        				'data' => $cache_data
        		));
        		return;
        	}
            $auth_call = $soapClient->FetchCities($params);
            if($auth_call->HasErrors) {
                echo json_encode(array(
                    'error' => true,
                    'message' => $auth_call->Notifications->Notification->Message
                ));
                return;
            } else {
            	$data = $auth_call->Cities->string;
            	// save cache data
            	$cache->save(hash('sha1', $CountryCode . '_' . $State . '_' . $NameStartsWith), $data);
                echo json_encode(array(
                    'error' => false,
                    'data' => $data
                ));
                return;
            }
        } catch (SoapFault $fault) {
            $faultString = $fault->faultstring;
            echo json_encode(array(
                'error' => true,
                'message' => $faultString
            ));
            return;
        }
    }

    public function translateAction() {
        $text = $this->getRequest()->getParam('text');
        $from = $this->getRequest()->getParam('from');
        $to = $this->getRequest()->getParam('to');
        if (empty($text)) {
            $data = array();
            $data['status'] = 'error';
            $data['message'] = 'Text param cannot be empty.';
            print json_encode($data);
            return;
        }
        $data = array();
        if (!empty($from) && !empty($to)) {
            $data['original_text'] = $text;
            $data['translated_text'] = $this->translate($text, $from . '_to_' . $to);
        } else {
            $data['original_text'] = $text;
            $data['translated_text'] = $this->translate($text);
        }
        print json_encode($data);
        return;
    }

    protected function curl($url, $params = array(), $is_coockie_set = false) {
        if (!$is_coockie_set) {
            $ckfile = tempnam("/tmp", "CURLCOOKIE");
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $output = curl_exec($ch);
        }
        $str = '';
        $str_arr = array();
        foreach ($params as $key => $value) {
            $str_arr[] = urlencode($key) . "=" . urlencode($value);
        }
        if (!empty($str_arr))
            $str = '?' . implode('&', $str_arr);
        $Url = $url . $str;
        $ch = curl_init($Url);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $ckfile);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        return $output;
    }

    protected function translate($text, $conversion = 'en_to_ar') {
        $text = urlencode($text);
        $arr_langs = explode('_to_', $conversion);
        $url = "http://translate.google.com/translate_a/t?client=t&text=$text&hl=" . $arr_langs[1] . "&sl=" . $arr_langs[0] . "&tl=" . $arr_langs[1] . "&ie=UTF-8&oe=UTF-8&multires=1&otf=1&pc=1&trs=1&ssel=3&tsel=6&sc=1";
        $name_en = $this->curl($url);
        $name_en = explode('"', $name_en);
        return $name_en[1];
    }

}