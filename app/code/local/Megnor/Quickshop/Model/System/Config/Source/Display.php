<?php

class Megnor_Quickshop_Model_System_Config_Source_Display
{
     public function toOptionArray()
    {
        return array(
            array('value'=>'yes', 'label'=>Mage::helper('quickshop')->__('Yes')),
            array('value'=>'no', 'label'=>Mage::helper('quickshop')->__('No'))
        );
    }}