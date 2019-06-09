<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('shopgo_bannerslider')};
CREATE TABLE {$this->getTable('shopgo_bannerslider')} (
  `bs_id` int(11) unsigned NOT NULL auto_increment,
  `identifier` varchar(255) NOT NULL default '',
  `title` varchar(255) NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `image` varchar(255) NOT NULL default '',
  `link` varchar(255) NULL,
  `link_type` smallint(6) NULL default '1',
  `link_target` varchar(255) NULL default '_self',
  `add_text` tinyint(1) NULL default '0',
  `description` text NULL default '',
  `sort_order` int(11) unsigned NULL,
  `stores` text default '',
  PRIMARY KEY (`bs_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup();
