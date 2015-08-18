<?php
$installer = $this;
 
$installer->startSetup();
$installer->getConnection()
    ->addColumn($installer->getTable('testimonialmanager'), 'testimonial_order', array(
            'nullable' => false,
            'length' => 255,
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'comment' => 'testimonial_order'
        ));
   $installer->getConnection() ->addColumn($installer->getTable('testimonialmanager'), 'testimonia_subject', array(
            'nullable' => false,
            'length' => 255,
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'comment' => 'testimonia_subject'
        ));
   $installer->getConnection() ->addColumn($installer->getTable('testimonialmanager'), 'testimonial_img2', array(
            'nullable' => false,
            'length' => 255,
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'comment' => 'testimonial_img2'
        ));
 
//$table = $installer->getTable('testimonialmanager');
//      $table->addColumn('testimonial_order', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
//        'nullable'  => false,
//        ), 'testimonial_order')
//      ->addColumn('testimonial_subject', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
//        'nullable'  => false,
//        ), 'testimonial_subject')
//      ->addColumn('testimonial_img2', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
//        'nullable'  => false,
//        ), 'testimonial_img2');
//   $installer->getConnection()->createTable($table);
$installer->endSetup();