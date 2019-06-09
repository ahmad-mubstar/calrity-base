<?php

class Shopgo_Geo_Model_Observer
{
	public function controllerFrontInitBefore($observer) {
          $request =  Mage::app()->getRequest();
		if(Mage::helper('geo')->isEnabled() && Mage::getStoreConfig('geo/general/store_switcher') && !$request->getParam('___store') && ($request->getRequestUri() == '/')) {
			$geoModel = Mage::getModel('geo/country');
			$currentCountry = $geoModel->getCountry();

               $helper = Mage::helper('geo');
        	      //$allowedLocales = Mage::app()->getLocale()->getAllowLocales();
			$allowedLocales = $helper->getLocaleList();

			foreach ($allowedLocales as $key => $value) {

        		     //$locale = explode('_', $value);
				$locale = explode('_', $key);
                    
				$country = isset($locale[1]) ? $locale[1] : false;
				$languageCode = $locale[0];

				if($currentCountry == $country && $helper->getStoreByCode($languageCode)) {
					Mage::app()->setCurrentStore($languageCode);
                         Mage::getModel('core/cookie')->set('store', $languageCode, true);
					break;
				}
			}
		}
	}
}