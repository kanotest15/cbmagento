<?php 
$installer = $this;
 
$installer->startSetup();
$installer->getConnection()
    ->addColumn($installer->getTable('zeon_artist'), 'display_authorized_dealer', array(
            'nullable' => false,
            'length' => 255,
            'type' => 'smallint',
            'comment' => 'Display Authorized Dealer Image'
        ));
    
  
 
 
$installer->endSetup();