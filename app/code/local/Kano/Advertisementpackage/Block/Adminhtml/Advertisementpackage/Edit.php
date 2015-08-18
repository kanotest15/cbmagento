<?php
	
class Kano_Advertisementpackage_Block_Adminhtml_Advertisementpackage_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "advertisementpackage";
				$this->_controller = "adminhtml_advertisementpackage";
				$this->_updateButton("save", "label", Mage::helper("advertisementpackage")->__("Save Advertisement Package"));
				$this->_updateButton("delete", "label", Mage::helper("advertisementpackage")->__("Delete Advertisement Package"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("advertisementpackage")->__("Save And Continue Edit"),
					"onclick"   => "saveAndContinueEdit()",
					"class"     => "save",
				), -100);



				$this->_formScripts[] = "

							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}
						";
		}

		public function getHeaderText()
		{
				if( Mage::registry("advertisementpackage_data") && Mage::registry("advertisementpackage_data")->getId() ){

				    return Mage::helper("advertisementpackage")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("advertisementpackage_data")->getId()));

				} 
				else{

				     return Mage::helper("advertisementpackage")->__("Add Advertisement Package");

				}
		}
}