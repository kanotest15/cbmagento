<?php

class Kano_Advertisementpackage_Adminhtml_AdvertisementpackageController extends Mage_Adminhtml_Controller_Action
{
    
      public function stateAction() {
        $countrycode = $this->getRequest()->getParam('country');
        $state = "<option value=''>--Please Select--</option>";
        if ($countrycode != '') {
            $statearray = Mage::getModel('directory/region')->getResourceCollection()->addCountryFilter($countrycode)->load();
            foreach ($statearray as $_state) {
                $state .= "<option value='" . $_state->getCode() . "'>" . $_state->getDefaultName() . "</option>";
            }
        }
        echo $state;
    }
    protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("advertisementpackage/advertisementpackage")->_addBreadcrumb(Mage::helper("adminhtml")->__("Tender Package Manager"),Mage::helper("adminhtml")->__("Advertisement Package Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Advertisement Package"));
			    $this->_title($this->__("Manager Advertisement Package"));

				$this->_initAction();
				$this->renderLayout(); 
		}
		
		public function editAction() {
			$id     = $this->getRequest()->getParam('id');
			$model  = Mage::getModel('advertisementpackage/advertisementpackage')->load($id);
		 
			if ($model->getId() || $id == 0) {
				$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
				if (!empty($data)) {
					$model->setData($data);
				}

				Mage::register('advertisementpackage_data', $model);
				
				$this->_title($this->__('Advertisement Package'))
					->_title($this->__('Manage Advertisement Package'));
				if ($model->getId()){  
					$this->_title($model->getTitle());
				}else{  
					$this->_title($this->__('New Advertisement Package'));
				}

				$this->loadLayout();
				$this->_setActiveMenu('advertisementpackage/items');
				 
				$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Advertisement Package Manager'), Mage::helper('adminhtml')->__('Advertisement Package Manager'));
				$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Advertisement Package News'), Mage::helper('adminhtml')->__('Advertisement Package News'));

				$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
				
				$this->_addContent($this->getLayout()->createBlock('tenderpackage/adminhtml_advertisementpackage_edit'));
				
				$this->_addLeft($this->getLayout()->createBlock('tenderpackage/adminhtml_advertisementpackage_edit_tabs'));
				
				$this->renderLayout();
			} else { 
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tenderpackage')->__('Advertisement Package does not exist'));
				$this->_redirect('*/*/');
			}
		}

		public function newAction()
		{
		$this->_title($this->__("New Advertisement Package"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("catalog/product")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("advertisementpackage_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("advertisementpackage/advertisementpackage");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Advertisement Package Manager"), Mage::helper("adminhtml")->__("Advertisement Package Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Advertisement Package Description"), Mage::helper("adminhtml")->__("Advertisement Package Description"));


		$this->_addContent($this->getLayout()->createBlock("advertisementpackage/adminhtml_advertisementpackage_edit"))->_addLeft($this->getLayout()->createBlock("advertisementpackage/adminhtml_advertisementpackage_edit_tabs"));

		$this->renderLayout();

		}
		
		public function saveAction() {
			if ($data = $this->getRequest()->getPost()) {
				if($data['filename']['delete']==1){
					$data['filename']='';
				}
				elseif(is_array($data['filename'])){
					$data['filename']=$data['filename']['value'];
				}
				
				if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
					try {	
						/* Starting upload */	
						$uploader = new Varien_File_Uploader('filename');
						
						// Any extention would work
						$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
						$uploader->setAllowRenameFiles(false);
						
						// Set the file upload mode 
						// false -> get the file directly in the specified folder
						// true -> get the file in the product like folders 
						//	(file.jpg will go in something like /media/f/i/file.jpg)
						$uploader->setFilesDispersion(false);
								
						// We set media as the upload dir
						$path = Mage::getBaseDir('media') . DS ;
						$result = $uploader->save($path, $_FILES['filename']['name'] );
						$data['filename'] = $result['file'];
					} catch (Exception $e) {
						$data['filename'] = $_FILES['filename']['name'];
					}
				}
					
					
				$model = Mage::getModel('advertisementpackage/advertisementpackage');					
				//$model->setData($data);
				$model->setPackageName($data['package_name']);
				$model->setPackageInformation($data['package_information']);
				$model->setPackageValidity($data['package_validity']);
				$model->setPackageAmount($data['package_amount']);
				$model->setPackageEmailConfirmation($data['package_email_confirmation']);
				$model->setStatus($data['status']);
				$model->setId($this->getRequest()->getParam('id'));
				
				try {
					if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
						$model->setCreatedTime(now())
							->setUpdateTime(now());
					} else {
						$model->setUpdateTime(now());
					}
					
					$model->setStores(implode(',',$data['stores']));
					if(Mage::app()->isSingleStoreMode()){
						$model->setStores(Mage::app()->getStore(true)->getId());
					}
					
					
					$model->save(); 
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('advertisementpackage')->__('Advertisement Package was successfully saved'));
					Mage::getSingleton('adminhtml/session')->setFormData(false);

					if ($this->getRequest()->getParam('back')) {
						$this->_redirect('*/*/edit', array('id' => $model->getId()));
						return;
					}
					$this->_redirect('*/*/');
					return;
				} catch (Exception $e) {
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					Mage::getSingleton('adminhtml/session')->setFormData($data);
					$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
					return;
				}
			}
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('advertisementpackage')->__('Unable to find Advertisement Package to save'));
			$this->_redirect('*/*/');
		}



		public function deleteAction()
		{
				if( $this->getRequest()->getParam("id") > 0 ) {
					try {
						$model = Mage::getModel("tender/tender");
						$model->setId($this->getRequest()->getParam("id"))->delete();
						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
						$this->_redirect("*/*/");
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					}
				}
				$this->_redirect("*/*/");
		}

		
		public function massRemoveAction()
		{
			try {
				$ids = $this->getRequest()->getPost('ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("tender/tender");
					  $model->setId($id)->delete();
				}
				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully removed"));
			}
			catch (Exception $e) {
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			}
			$this->_redirect('*/*/');
		}
			
		/**
		 * Export order grid to CSV format
		 */
		public function exportCsvAction()
		{
			$fileName   = 'tender.csv';
			$grid       = $this->getLayout()->createBlock('tender/adminhtml_tender_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'tender.xml';
			$grid       = $this->getLayout()->createBlock('tender/adminhtml_tender_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
       
}
