<?php
class Shopgo_Bannerslider_Block_Bannerslider extends Mage_Core_Block_Template
{
    private $_display = '0';

    public function _prepareLayout()    {
        return parent::_prepareLayout();
    }

    public function getBannerslider() {
        if (!$this->hasData('bannerslider')) {
            $this->setData('bannerslider', Mage::registry('bannerslider'));
        }
        return $this->getData('bannerslider');
    }

    public function setDisplay($display){
        $this->_display = $display;
    }

    public function getSliderConfig($setting = '')
    {
        return Mage::helper('bannerslider')->getSliderSettings($setting);
    }

    public function getBannersliderCollection($position = '', $ignore = false)
    {
        $helper = Mage::helper('bannerslider');

        if (!$ignore) {
            if (!$helper->getConfig('general/enabled')
                || !$helper->getIsHomePage()
                || ($position != $helper->getSliderSettings('position'))
            ) {
                return;
            }
        }

        $collection = Mage::getModel('bannerslider/bannerslider')->getCollection()
            ->addFieldToFilter('status', 1)
            ->setOrder('sort_order', 'ASC');

        $current_store = Mage::app()->getStore()->getId();
        $banners = array();

        foreach ($collection as $banner) {
            $stores = explode(',',$banner->getStores());
            if (in_array(0,$stores) || in_array($current_store,$stores))
                $banners[] = $banner;
        }

        return $banners;
    }
}
