<?php
class Zeon_Artist_Adminhtml_Artist_ListController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Zeon Extensions'))->_title($this->__('Artist'));
        $this->loadLayout();
        $this->_setActiveMenu('zextensions/zeon_artist');
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit action
     *
     */
    public function editAction()
    {
        $id     = $this->getRequest()->getParam('id');
        $model = $this->_initArtist('id');
        $model  = Mage::getModel('zeon_artist/artist')->load($id);

        if (!$model->getId() && $id) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('zeon_artist')->__('This artist no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $this->_title($model->getId() ? $model->getTitle() : $this->__('Add Artist'));

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->loadLayout();
        $this->_setActiveMenu('zextensions/zeon_artist');

        $this->_addBreadcrumb(
            $id ? Mage::helper('zeon_artist')->__('Edit Artist') : Mage::helper('zeon_artist')->__('Add Artist'),
            $id ? Mage::helper('zeon_artist')->__('Edit Artist') : Mage::helper('zeon_artist')->__('Add Artist')
        )->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        $redirectBack = $this->getRequest()->getParam('back', false);
        if ($data = $this->getRequest()->getPost()) { 
            $id = $this->getRequest()->getParam('id');
            $model = $this->_initArtist();

            if (!$model->getId() && $id) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('zeon_artist')->__('This artist no longer exists.')
                );
                $this->_redirect('*/*/');
                return;
            }
			$artist = $this->getRequest()->getParam('artist');
			$attributeDetails = Mage::getSingleton("eav/config")->getAttribute("catalog_product", 'manufacturer');
			$optionValue = $attributeDetails->getSource()->getOptionText($artist);
			if(isset($optionValue) && $optionValue!='' && $data['artist_title']==''){
				$data['artist_title'] = $optionValue;
			}
            $artistName = $model->getArtistName($data['artist'], Mage::app()->getStore()->getId());
            $identifier = $data['identifier'] ? $data['identifier'] : $artistName;
            $data['identifier'] = Mage::getModel('zeon_artist/url')->formatUrlKey($identifier);
            $data['display_authorized_dealer'] = $data['display_authorized_dealer'];
            // save artist logo and banner
            $fieldName = array();
            $fieldName[0] =  'artist_logo';
            $fieldName[1] =  'artist_banner';
             $fieldName[2] =  'artist_image';
               $fieldName[3] =  'artist_image1';
               $fieldName[4] =  'artist_image2';
               $fieldName[5] =  'artist_image3';
               $fieldName[6] =  'artist_image4';
                $fieldName[7] =  'artist_image5';
            if ($count = count($_FILES)) {
                for ($i = 0; $i < $count; $i++) {
                    if (isset($_FILES[$fieldName[$i]]['name']) && $_FILES[$fieldName[$i]]['tmp_name'] != '') {
                        $uploader = new Varien_File_Uploader($fieldName[$i]);
                        $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                        $uploader->setAllowRenameFiles(true);
                        $uploader->setFilesDispersion(false);
                        $path = Mage::getBaseDir('media') . DS . 'artist' . DS;
                        $_FILES[$fieldName[$i]]['name'] = str_replace(' ', '-', $_FILES[$fieldName[$i]]['name']);
                        $uploader->save($path, $_FILES[$fieldName[$i]]['name']);
                        $data[$fieldName[$i]] = $_FILES[$fieldName[$i]]['name'];
                        if ($_FILES['artist_logo']['name'] && $_FILES['artist_logo']['tmp_name'] != '') {
                            $model->setArtistLogo($uploader->getUploadedFileName());
                        } else {
                            $model->setArtistBanner($uploader->getUploadedFileName());
                        }
                    } else {
                        if (isset($data[$fieldName[$i]]['delete']) && $data[$fieldName[$i]]['delete'] == 1) {
                            $data[$fieldName[$i]] = '';
                        } else {
                            unset($data[$fieldName[$i]]);
                        }
                    }
                }
            }
            // save model
            try {
                if (!empty($data)) {
                    $model->addData($data);
                    Mage::getSingleton('adminhtml/session')->setFormData($data);
                }
				
                $model->save();
				
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('zeon_artist')->__('The artist has been saved.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $redirectBack = true;
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('zeon_artist')->__('Unable to save the artist.')
                );
                $redirectBack = true;
                Mage::logException($e);
            }
            if ($redirectBack) {
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('id')) {
            try {
            // init model and delete
            $model = Mage::getModel('zeon_artist/artist');
            $model->load($id);
            $model->delete();
            // display success message
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('zeon_artist')->__('The artist has been deleted.')
            );
            // go to grid
            $this->_redirect('*/*/');
            return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('zeon_artist')->__(
                        'An error occurred while deleting artist data. Please review log and try again.'
                    )
                );
                Mage::logException($e);
                // save data in session
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('zeon_artist')->__('Unable to find a artist to delete.')
        );
        // go to grid
        $this->_redirect('*/*/');
    }

    /**
     * Delete specified artist using grid massaction
     *
     */
    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('artist');
        if (!is_array($ids)) {
            $this->_getSession()->addError($this->__('Please select artist(s).'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getSingleton('zeon_artist/artist')->load($id);
                    $model->delete();
                }

                $this->_getSession()->addSuccess($this->__('Total of %d record(s) have been deleted.', count($ids)));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('zeon_artist')->__(
                        'An error occurred while mass deleting artist. Please review log and try again.'
                    )
                );
                Mage::logException($e);
                return;
            }
        }
        $this->_redirect('*/*/index');
    }

     /**
     * Update specified artist status using grid massaction
     *
     */
    public function massStatusAction()
    {
        $ids = $this->getRequest()->getParam('artist');
        if (!is_array($ids)) {
            $this->_getSession()->addError($this->__('Please select artist(s).'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getSingleton('zeon_artist/artist')->load($id);
                    $model->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) have been updated', count($ids)));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('zeon_artist')->__(
                        'An error occurred while mass updating artist. Please review log and try again.'
                    )
                );
                Mage::logException($e);
                return;
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Load Artist from request
     *
     * @param string $idFieldName
     * @return Zeon_Artist_Model_Artist $model
     */
    protected function _initArtist($idFieldName = 'artist_id')
    {
        $id = (int)$this->getRequest()->getParam($idFieldName);
        $model = Mage::getModel('zeon_artist/artist');
        if ($id) {
            $model->load($id);
        }
        if (!Mage::registry('current_artist')) {
            Mage::register('current_artist', $model);
        }
        return $model;
    }

    /**
     * Render Artist grid
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}