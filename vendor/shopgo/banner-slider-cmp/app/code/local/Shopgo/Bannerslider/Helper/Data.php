<?php
class Shopgo_Bannerslider_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getConfig($path)
    {
        return Mage::getStoreConfig('bannerslider/' . $path);
    }

    public function getLink($link, $type)
    {
        if ($type == 1) { // Internal
            $link = Mage::getUrl($link);
        }

        return $link;
    }

    public function getImage($imgSrc)
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $imgSrc;
    }

    public function getIsHomePage()
    {
        $result = false;
        $route = Mage::app()->getRequest()->getRouteName();
        $pageIdentifier = Mage::getSingleton('cms/page')->getIdentifier();

        if($route == 'cms' && $pageIdentifier == 'home') {
            $result = true;
        }

        return $result;
    }

    public function getSliderHtml()
    {
        return Mage::app()->getLayout()
               ->createBlock('bannerslider/bannerslider')
               ->setTemplate('shopgo/bannerslider.phtml')
               ->toHtml();
    }

    public function getSliderSettings($setting = '')
    {
        $settings = array(
            'jquery' => $this->getConfig('general/jquery'),
            'flexslider' => $this->getConfig('general/flexslider'),
            'easing' => $this->getConfig('general/easing'),
            'mousewheel' => $this->getConfig('general/mousewheel'),
            'modernizr' => $this->getConfig('general/modernizr'),
            'position' => $this->getConfig('slider/position'),
            'type' => $this->getConfig('slider/type'),
            'animation' => $this->getConfig('slider/animation'),
            'easing' => $this->getConfig('slider/easing'),
            'timeout' => $this->getConfig('slider/timeout'),
            'speed' => $this->getConfig('slider/speed'),
            'pause' => $this->getConfig('slider/pause'),
            'loop' => $this->getConfig('slider/loop')
        );

        if (isset($settings[strtolower($setting)])) {
            return $settings[strtolower($setting)];
        }

        return $settings;
    }
}
