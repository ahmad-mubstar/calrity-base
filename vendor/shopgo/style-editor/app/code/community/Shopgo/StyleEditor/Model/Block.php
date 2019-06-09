<?php

class Shopgo_StyleEditor_Model_Block extends Mage_Core_Model_Abstract
{

    public function toOptionArray()
    {

        //Mage::log(Mage::getModel('cms/block')->getCollection(), Zend_Log::DEBUG, 'test.log');
        //echo $this->getLayout()->getBlock('cms/block')->toHtml();
        //$this->getLayout()->createBlock("cms/block")->setBlockId("shop_now_2")->toHtml();

        $collection = Mage::getModel('cms/block')->getCollection()
            ->addFieldToFilter('identifier', array('like'=>''.'%'))
            ->addFieldToFilter('is_active', 1);

        //Mage::log($collection, Zend_Log::DEBUG, 'test.log');
        $options = array();
        foreach($collection as $key => $value) {
            $block = $value->getData();
            //Mage::log($block['title'], Zend_Log::DEBUG, 'test.log');
            //Mage::log($block['identifier'], Zend_Log::DEBUG, 'test.log');
            array_unshift($options, array('value'=>$block['identifier'], 'label'=>$block['title']));
        }


        Mage::log($options, Zend_Log::DEBUG, 'test.log');


        return $options;

        //$blockNum = 1;
        //foreach($collection as $key => $value){
        //    $_blockId = $this->getIdentifier();
        //    $block_ID = $_blockId . $blockNum;
        //
        //    $_block = $this->getLayout()->createBlock('cms/block')->setBlockId($block_ID);
        //    Mage::log($_block->getTitle(), Zend_Log::DEBUG, 'test.log');
        //
        //    //echo "Key: " . $key . " - " . "Block ID: " . $block_ID . "<br />";
        //    $blockNum++;
        //}

//        return array(
//            array(
//                'value' => 'full',
//                'label' => 'Full',
//            ),
//            array(
//                'value' => 'left',
//                'label' => 'Left',
//            ),
//            array(
//                'value' => 'middle',
//                'label' => 'Middle',
//            ),
//            array(
//                'value' => 'right',
//                'label' => 'Right',
//            ),
//        );
    }

}