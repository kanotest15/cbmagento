<?php
$installer = $this;
 
$installer->startSetup();
$installer->getConnection()
    ->addColumn($installer->getTable('zeon_artist'), 'artist_event', array(
            'nullable' => false,
            'length' => 255,
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'comment' => 'artist_event'
        ));
    
  
 
 
$installer->endSetup();