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

$installer->endSetup();