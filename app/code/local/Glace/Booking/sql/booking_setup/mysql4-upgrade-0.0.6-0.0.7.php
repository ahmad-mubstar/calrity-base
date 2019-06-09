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


$installer->addAttribute('catalog_product', 'display_timezone', array(
		'group' => 'Book Setup',
		'input' => 'select',
		'type' => 'int',
		'label' => 'Display Server Timezone',
		'source' => 'eav/entity_attribute_source_table',
		'visible' => 1,
		'required' => 0,
		'user_defined' => 1,
		'searchable' => 0,
		'filterable' => 0,
		'comparable' => 0,
		'visible_on_front' => 0,
		'visible_in_advanced_search' => 0,
		'is_html_allowed_on_front' => 0,
		'apply_to' => 'booking',
		'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
		'option'     => array (
				'values' => array(
						0 => 'enabled',
						1 => 'disabled'
				)
		),
));


$installer->endSetup();