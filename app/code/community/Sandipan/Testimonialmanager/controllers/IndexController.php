<?php
class Sandipan_Testimonialmanager_IndexController extends Mage_Core_Controller_Front_Action{
    public function indexAction()
    {
      $this->loadLayout();
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Contact"));
	  $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home"),
                "title" => $this->__("Home"),
                "link"  => Mage::getBaseUrl()
		   ));
      $breadcrumbs->addCrumb("testimonials", array(
                "label" => $this->__("contact"),
                "title" => $this->__("contact")
		   ));
      $this->renderLayout();
    }
    public function formAction()
    {
        $this->loadLayout();
		  $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
		  $breadcrumbs->addCrumb("home", array(
					"label" => $this->__("Home"),
					"title" => $this->__("Home"),
					"link"  => Mage::getBaseUrl()
			   ));
		  $breadcrumbs->addCrumb("write_testimonials", array(
					"label" => $this->__("contact "),
					"title" => $this->__("contact ")
			   ));
        $this->renderLayout();
    }
    public function submitAction()
    {
		if ($this->getRequest()->getPost()) {
//                    zend_debug::dump($this->getRequest()->getPost());exit;
			try {
				$data = $this->getRequest()->getParams();
				//echo '<pre>'; var_dump($data); echo '</pre>'; exit;
				if (isset($_FILES['testimonial_img']['name']) && (file_exists($_FILES['testimonial_img']['tmp_name']))) {
					$uploader = new Varien_File_Uploader('testimonial_img');
					$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					$uploader->setFilesDispersion(false);
					$path = Mage::getBaseDir('media') . DS . 'testimonial_user' . DS ;
					$uploader->save($path, $_FILES['testimonial_img']['name']);
					$data['testimonial_img'] = 'testimonial_user/' . $_FILES['testimonial_img']['name'];
				}
                                if (isset($_FILES['testimonial_img2']['name']) && (file_exists($_FILES['testimonial_img2']['tmp_name']))) {
					$uploader = new Varien_File_Uploader('testimonial_img2');
					$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					$uploader->setFilesDispersion(false);
					$path = Mage::getBaseDir('media') . DS . 'testimonial_user' . DS ;
					$uploader->save($path, $_FILES['testimonial_img2']['name']);
					$data['testimonial_img2'] = 'testimonial_user/' . $_FILES['testimonial_img2']['name'];
				}

				$model = Mage::getModel('testimonialmanager/testimonialmanager');
				$model->setData($data);
                                $model->setTestimonialOrder($data['testimonial_order']);
                                $model->setData($data);
                                $model->setTestimoniaSubject($data['testimonia_subject']);
                               
                               
                                try{
                                    $model->save();
                                }catch(Exception $e){
                                    echo "hiiii";
                                }
                                     //   $model = Mage::getModel('testimonialmanager/testimonialmanager');
                                      //  $model->setData($data);
                                        
				 
//                                   $rurl= $this->getUrl('testimonialmanager/index/form/');
				Mage::getModel('core/session')->addSuccess(Mage::helper('testimonialmanager')->__('Contact   successfully submitted '));
				$this->_redirect('testimonialmanager/index/form/');
			} catch (Exception $e) {
				Mage::getModel('core/session')->addError($e->getMessage());
				$this->_redirect('*/*');
				//return;
			}
		}
    }
}