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

$table = $installer->getConnection()
->newTable($installer->getTable('booking/book_session'))
->addColumn('session_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'identity'  => true,
		'unsigned'  => true,
		'nullable'  => false,
		'primary'   => true,
), 'Exclude Specific Day ID')
->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned'  => true,
		'nullable'  => false,
		'default'   => '0',
), 'Product ID')
->addColumn('session_day', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'nullable'  => false
), 'session day')
->addColumn('spec_day', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(), 'specific day')
->addForeignKey($installer->getFkName('booking/book_session', 'entity_id', 'catalog/product', 'entity_id'),
		'entity_id', $installer->getTable('catalog/product'), 'entity_id',
		Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
		->setComment('Product ID');
$installer->getConnection()->createTable($table);


$table = $installer->getConnection()
->newTable($installer->getTable('booking/book_session_time'))
->addColumn('time_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'identity'  => true,
		'unsigned'  => true,
		'nullable'  => false,
		'primary'   => true,
), 'Session Time ID')
->addColumn('session_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned'  => true,
		'nullable'  => false,
		'default'   => '0',
), 'Session ID')
->addColumn('hour', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'session hours')
->addColumn('minute', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'session minute')
->addForeignKey($installer->getFkName('booking/book_session_time', 'session_id', 'booking/book_session', 'session_id'),
		'session_id', $installer->getTable('booking/book_session'), 'session_id',
		Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
		->setComment('Session ID');
$installer->getConnection()->createTable($table);

$installer->endSetup();