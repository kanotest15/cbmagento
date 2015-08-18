<?php
class Kano_Salesgridorder_Model_Sales_Mysql4_Order_Collection extends Mage_Sales_Model_Mysql4_Order_Collection
{
    public function getSelectCountSql()
{
      
 /* @var $countSelect Varien_Db_Select */
 $countSelect = parent::getSelectCountSql();

 $countSelect->resetJoinLeft();
 $countSelect->reset(Zend_Db_Select::GROUP); // add this to reset the GROUP
 return $countSelect;
}
}
		