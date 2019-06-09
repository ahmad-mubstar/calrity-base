<?php
$installer = new Mage_Catalog_Model_Resource_Eav_Mysql4_Setup('core_setup');
$installer->startSetup();

$installer->run("
        DROP TABLE IF EXISTS {$this->getTable('shopgo_product_flipper')};

        CREATE TABLE `shopgo_product_flipper` (
            `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Flipper ID',
            `product_id` INT(11) NOT NULL COMMENT 'Product ID',
            `store_id` VARCHAR(200) NOT NULL COMMENT 'Store ID',
            `status` TINYINT(1) NOT NULL DEFAULT '1' COMMENT 'Enable or disable on frontend',
            PRIMARY KEY (`id`)
        ) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
	");

$installer->endSetup();