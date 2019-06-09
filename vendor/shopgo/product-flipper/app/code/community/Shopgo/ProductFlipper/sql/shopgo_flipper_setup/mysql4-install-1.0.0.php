<?php
$installer = new Mage_Catalog_Model_Resource_Eav_Mysql4_Setup('core_setup');
$installer->startSetup();

$installer->addAttribute(
    'catalog_product',
    'flipper_image',
    array(
        'group' => 'Images',
        'type' => 'varchar',
        'frontend' => 'catalog/product_attribute_frontend_image',
        'label' => 'Flipper Image',
        'input' => 'media_image',
        'class' => '',
        'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible' => true,
        'required' => false,
        'user_defined' => false,
        'default' => '',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'unique' => false,
    )
);

$installer->endSetup();