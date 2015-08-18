<?php
class Kano_Advertisementpackage_Block_Adminhtml_Advertisementpackage_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("advertisementpackage_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("advertisementpackage")->__("Advertisement Package Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("advertisementpackage")->__("Advertisement Package Information"),
				"title" => Mage::helper("advertisementpackage")->__("Advertisement Package Information"),
				"content" => $this->getLayout()->createBlock("advertisementpackage/adminhtml_advertisementpackage_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
