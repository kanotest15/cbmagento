<?php
 
$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('kano_advertisementpackage')};
CREATE TABLE {$this->getTable('kano_advertisementpackage')} (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `package_name` varchar(255) NOT NULL,
  `package_validity` int(40) NOT NULL ,
  `package_information`  varchar(255) NULL ,
  `package_amount`  varchar(255) NULL ,
  `package_email_confirmation` TEXT NOT NULL default '',
  `status`  int(2) NULL ,
  
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
");

$installer->endSetup();
