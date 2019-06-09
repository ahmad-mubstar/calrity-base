<?php
class Shopgo_Store_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getDemoExpireMessage() {

		$domain = explode('.', $_SERVER['HTTP_HOST']);

		if ($domain[1] == "myshopgo2" || $domain[2] == "myshopgo2" || ($domain[1] == "shopgo" && $domain[2] == "com")) {} else {return '';}

		$model = Mage::getModel('store/store');

		$storeName = array_shift((explode('.', $_SERVER['HTTP_HOST'])));
		$storeData = $model->getStoreData($storeName);

		$created = time();
		while ($row = mysql_fetch_assoc($storeData)) {
			$created = strtotime($row['created']);
		}

		$left = (86400 * 7) - (time() - $created);
		$locale = Mage::app()->getLocale()->getLocaleCode();
		$theme = Mage::getSingleton('core/design_package')->getTheme('frontend');
		$style = "alert alert-warning notification-global";
		$message = '';

		if ($left > 0) {
			$time = $model -> time_elapsed_B($left);

			$message = "<div style=\"font-size: 13px;\" class=\"text-left " . $style . " text-left\"><strong>" . Mage::helper('store')->__('Hello') . "</strong> .. "
				. Mage::helper('store')->__('Your demo store trial will end in')
				. " " . $time . ". "
				. Mage::helper('store')->__('Contact Sales') . " <strong style=\"font-family: Arial;\"><a href=\"mailto:sales@shopgo.me\">sales@shopgo.me</a></strong> "
				. Mage::helper('store')->__('or call') . " <strong style=\"direction:ltr;font-family: Arial;\" dir=\"ltr\"> +962 78 094 4403</strong> "
				. Mage::helper('store')->__('immediately to avoid losing the store') . ". "
				. Mage::helper('store')->__('Kindly check') . " <strong><a style=\"font-family: Arial;\" href=\"http://www.shopgo.me/pricing\">www.shopgo.me/pricing</a></strong>.</div>";
		}
		return $message;
	}
}