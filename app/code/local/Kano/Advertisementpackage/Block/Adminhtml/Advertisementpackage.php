<?php


class Kano_Advertisementpackage_Block_Adminhtml_Advertisementpackage extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_advertisementpackage";
	$this->_blockGroup = "advertisementpackage";
	$this->_headerText = Mage::helper("advertisementpackage")->__("Advertisement Package Manager");
	$this->_addButtonLabel = Mage::helper("advertisementpackage")->__("Add New Advertisement Package");
	parent::__construct();
	
	}

}