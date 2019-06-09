<?php

$installer = $this;

$installer->startSetup();

if (class_exists('Mage_Admin_Model_Block')) {
    $hasBlock = Mage::getModel('admin/block')
        ->getCollection()
        ->addFieldToSelect('block_id')
        ->addFieldToFilter('block_name', array('eq' => 'megamenu/menu'))
        ->getSize();

    if (!$hasBlock) {
        Mage::getModel('admin/block')
            ->setBlockName('megamenu/menu')
            ->setIsAllowed(1)
            ->save();
    }
}

$installer->endSetup();
