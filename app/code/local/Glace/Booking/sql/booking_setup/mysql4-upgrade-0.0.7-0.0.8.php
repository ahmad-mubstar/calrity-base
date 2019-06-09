<?php
/*
 * Developer: Rene Voorberg
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 *
*/
$installer = $this;

$installer->startSetup();

$table = $installer->getTable('booking/book_item');

$installer->run("ALTER TABLE $table ADD order_item_id INT(10) AFTER product_id");


$installer->endSetup();