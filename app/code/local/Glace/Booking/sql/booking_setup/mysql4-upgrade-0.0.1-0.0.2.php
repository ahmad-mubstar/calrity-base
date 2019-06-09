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

$fieldList = array(
		'price',
		'special_price',
		'tax_class_id',
		'tier_price',
		'group_price',
		'special_from_date',
		'special_to_date',
		'msrp_enabled',
		'msrp_display_actual_price_type',
		'msrp',
		'enable_googlecheckout'
);

foreach ($fieldList as $field) {
	$applyTo = explode(',', $installer->getAttribute('catalog_product', $field, 'apply_to'));
	if (!in_array('booking', $applyTo)) {
		$applyTo[] = 'booking';
		$installer->updateAttribute('catalog_product', $field, 'apply_to', join(',', $applyTo));
	}
}

$table = $installer->getConnection()
->newTable($installer->getTable('booking/book_excludeday'))
->addColumn('exday_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
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
->addColumn('period_type', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
		'nullable'  => false
), 'period type')
->addColumn('from_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(), 'from date')
->addColumn('to_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(), 'to date')
->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(), 'value')
->addForeignKey($installer->getFkName('booking/book_excludeday', 'entity_id', 'catalog/product', 'entity_id'),
				'entity_id', $installer->getTable('catalog/product'), 'entity_id',
				Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
				->setComment('Product ID');
$installer->getConnection()->createTable($table);

$table = $installer->getConnection()
->newTable($installer->getTable('booking/book_pricerule'))
->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'identity'  => true,
		'unsigned'  => true,
		'nullable'  => false,
		'primary'   => true,
), 'Price rule ID')
->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned'  => true,
		'nullable'  => false,
		'default'   => '0',
), 'Product ID')
->addColumn('type', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Type')
->addColumn('value', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Value')
->addColumn('value_type', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Value Type')
->addColumn('move', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Move')
->addColumn('amount', Varien_Db_Ddl_Table::TYPE_DOUBLE, null, array(), 'amount')
->addColumn('amount_type', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Amount Type')
->addForeignKey($installer->getFkName('booking/book_pricerule', 'entity_id', 'catalog/product', 'entity_id'),
		'entity_id', $installer->getTable('catalog/product'), 'entity_id',
		Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
		->setComment('Product ID');
$installer->getConnection()->createTable($table);

$installer->endSetup();
