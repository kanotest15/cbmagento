<?php
/* @var $installer Zeon_Artist_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();
$installer->run(
    "
DROP TABLE IF EXISTS {$this->getTable('zeon_artist/artist')};
CREATE TABLE {$this->getTable('zeon_artist/artist')} (
    `artist_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Artist Id',
    `artist` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Artist Option Id',
    `status` smallint(6) NOT NULL COMMENT 'Status',
    `is_display_home` smallint(6) NOT NULL COMMENT 'Is Display Home',
    `identifier` varchar(255) DEFAULT NULL COMMENT 'Identifier',
    `artist_logo` varchar(255) DEFAULT NULL COMMENT 'Artist Logo',
    `artist_banner` varchar(255) DEFAULT NULL COMMENT 'Artist Banner',
    `description` text NOT NULL COMMENT 'Description',
    `description1` text NOT NULL COMMENT 'Description1',
    `description2` text NOT NULL COMMENT 'Description2',
    `sort_order` smallint(6) DEFAULT NULL COMMENT 'Sort Order',
    `meta_keywords` text COMMENT 'Meta Keywords',
    `meta_description` text COMMENT 'Meta Description',
    `creation_time` datetime DEFAULT NULL COMMENT 'Creation Time',
    `update_time` datetime DEFAULT NULL COMMENT 'Update Time',
    PRIMARY KEY (`artist_id`),
    KEY `IDX_ZEON_Artist_OPTION_ID` (`artist`),
    CONSTRAINT `FK_ZEON_Artist_OPT_ID_EAV_ATTR_OPT_OPT_ID` FOREIGN KEY (`artist`) 
    REFERENCES {$this->getTable('eav_attribute_option')} (`option_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Zeon Artist';

DROP TABLE IF EXISTS {$this->getTable('zeon_artist/store')};
CREATE TABLE {$this->getTable('zeon_artist/store')} (
    `artist_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Artist Id',
    `store_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Store Id',
    PRIMARY KEY (`artist_id`,`store_id`),
    KEY `IDX_ZEON_Artist_STORE_Artist_ID` (`artist_id`),
    KEY `IDX_ZEON_Artist_STORE_STORE_ID` (`store_id`),
    CONSTRAINT `FK_ZEON_ARTIST_STORE_ARTIST_ID_ZEON_ARTIST_ARTIST_ID` FOREIGN KEY (`artist_id`) 
    REFERENCES {$this->getTable('zeon_artist/artist')} (`artist_id`) 
    ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_ZEON_ARTIST_STORE_STORE_ID_CORE_STORE_STORE_ID` FOREIGN KEY (`store_id`) 
    REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Zeon Artist Store';
");

$installer->endSetup();