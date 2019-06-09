<?php

class Shopgo_Bannerslider_Model_System_Config_Source_Easing
{
    public function toOptionArray()
    {
        $helper = Mage::helper('bannerslider');

        return array(
            //Defaults
            array('value' => 'swing',   'label' => $helper->__('Swing')),
            array('value' => 'linear',   'label' => $helper->__('Linear')),
            //Ease in-out
            array('value' => 'easeInOutSine',   'label' => $helper->__('easeInOutSine')),
            array('value' => 'easeInOutQuad',   'label' => $helper->__('easeInOutQuad')),
            array('value' => 'easeInOutCubic',  'label' => $helper->__('easeInOutCubic')),
            array('value' => 'easeInOutQuart',  'label' => $helper->__('easeInOutQuart')),
            array('value' => 'easeInOutQuint',  'label' => $helper->__('easeInOutQuint')),
            array('value' => 'easeInOutExpo',   'label' => $helper->__('easeInOutExpo')),
            array('value' => 'easeInOutCirc',   'label' => $helper->__('easeInOutCirc')),
            array('value' => 'easeInOutElastic','label' => $helper->__('easeInOutElastic')),
            array('value' => 'easeInOutBack',   'label' => $helper->__('easeInOutBack')),
            array('value' => 'easeInOutBounce', 'label' => $helper->__('easeInOutBounce')),
            //Ease out
            array('value' => 'easeOutSine',     'label' => $helper->__('easeOutSine')),
            array('value' => 'easeOutQuad',     'label' => $helper->__('easeOutQuad')),
            array('value' => 'easeOutCubic',    'label' => $helper->__('easeOutCubic')),
            array('value' => 'easeOutQuart',    'label' => $helper->__('easeOutQuart')),
            array('value' => 'easeOutQuint',    'label' => $helper->__('easeOutQuint')),
            array('value' => 'easeOutExpo',     'label' => $helper->__('easeOutExpo')),
            array('value' => 'easeOutCirc',     'label' => $helper->__('easeOutCirc')),
            array('value' => 'easeOutElastic',  'label' => $helper->__('easeOutElastic')),
            array('value' => 'easeOutBack',     'label' => $helper->__('easeOutBack')),
            array('value' => 'easeOutBounce',   'label' => $helper->__('easeOutBounce')),
            //Ease in
            array('value' => 'easeInSine',      'label' => $helper->__('easeInSine')),
            array('value' => 'easeInQuad',      'label' => $helper->__('easeInQuad')),
            array('value' => 'easeInCubic',     'label' => $helper->__('easeInCubic')),
            array('value' => 'easeInQuart',     'label' => $helper->__('easeInQuart')),
            array('value' => 'easeInQuint',     'label' => $helper->__('easeInQuint')),
            array('value' => 'easeInExpo',      'label' => $helper->__('easeInExpo')),
            array('value' => 'easeInCirc',      'label' => $helper->__('easeInCirc')),
            array('value' => 'easeInElastic',   'label' => $helper->__('easeInElastic')),
            array('value' => 'easeInBack',      'label' => $helper->__('easeInBack')),
            array('value' => 'easeInBounce',    'label' => $helper->__('easeInBounce')),
            //No easing
            array('value' => 'null',            'label' => $helper->__('Disabled'))
        );
    }
}
