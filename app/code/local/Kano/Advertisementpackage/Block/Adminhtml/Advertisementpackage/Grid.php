<?php

class Kano_Advertisementpackage_Block_Adminhtml_Advertisementpackage_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("advertisementpackageGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("catalog/product")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("advertisementpackage")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
				));
                
				$this->addColumn("package_name", array(
				"header" => Mage::helper("advertisementpackage")->__("Package Name"),
				"index" => "package_name",
				));
				$this->addColumn("package_validity", array(
				"header" => Mage::helper("advertisementpackage")->__("Package Validity"),
				"index" => "package_validity",
				));
				$this->addColumn("package_amount", array(
				"header" => Mage::helper("advertisementpackage")->__("Package Amount"),
				"index" => "package_amount",
				));
				$this->addColumn("package_email_confirmation", array(
				"header" => Mage::helper("advertisementpackage")->__("package email Confirmation"),
				"index" => "package_email_confirmation",
				));				
				$this->addColumn("package_information", array(
				"header" => Mage::helper("advertisementpackage")->__("Package Information"),
				"index" => "package_information",
				));
				$this->addColumn('status', array(
				'header' => Mage::helper('advertisementpackage')->__('Status'),
				'index' => 'status',
				'type' => 'options',
				'options'=>Kano_Advertisementpackage_Block_Adminhtml_Advertisementpackage_Grid::getOptionArray12(),				
				));
						
			$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
			$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return $this->getUrl("*/*/edit", array("id" => $row->getId()));
		}


		
		protected function _prepareMassaction()
		{
			$this->setMassactionIdField('id');
			$this->getMassactionBlock()->setFormFieldName('ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_advertisementpackage', array(
					 'label'=> Mage::helper('advertisementpackage')->__('Remove Advertisement Package'),
					 'url'  => $this->getUrl('*/adminhtml_advertisementpackage/massRemove'),
					 'confirm' => Mage::helper('advertisementpackage')->__('Are you sure?')
				));
			return $this;
		}
			
		static public function getOptionArray12()
		{
            $data_array=array(); 
			$data_array[0]='Enable';
			$data_array[1]='Disable';
            return($data_array);
		}
		static public function getValueArray12()
		{
            $data_array=array();	
			foreach(Kano_Advertisementpackage_Block_Adminhtml_Advertisementpackage_Grid::getOptionArray12() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		

}