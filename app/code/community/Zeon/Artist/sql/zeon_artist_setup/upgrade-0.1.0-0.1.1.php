<?php
$installer = $this;
 
$installer->startSetup();
$installer->getConnection()
    ->addColumn($installer->getTable('zeon_artist'), 'artist_image', array(
            'nullable' => false,
            'length' => 255,
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'comment' => 'artist_image'
        ));
   $installer->getConnection() ->addColumn($installer->getTable('zeon_artist'), 'artist_image1', array(
            'nullable' => false,
            'length' => 255,
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'comment' => 'artist_image1'
        ));
   $installer->getConnection() ->addColumn($installer->getTable('zeon_artist'), 'artist_image2', array(
            'nullable' => false,
            'length' => 255,
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'comment' => 'artist_image2'
        ));
     $installer->getConnection() ->addColumn($installer->getTable('zeon_artist'), 'artist_image3', array(
            'nullable' => false,
            'length' => 255,
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'comment' => 'artist_image3'
        ));
        $installer->getConnection() ->addColumn($installer->getTable('zeon_artist'), 'artist_image4', array(
            'nullable' => false,
            'length' => 255,
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'comment' => 'artist_image4'
        ));
           $installer->getConnection() ->addColumn($installer->getTable('zeon_artist'), 'artist_image5', array(
            'nullable' => false,
            'length' => 255,
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'comment' => 'artist_image5'
        ));
 
 
$installer->endSetup();