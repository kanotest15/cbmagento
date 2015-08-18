<?php
class Zeon_Artist_Block_Adminhtml_Artist_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Load Wysiwyg on demand and Prepare layout
     */
    protected function _prepareLayout()
    { 
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    /**
     * Set form id prefix, set values if artist is editing
     *
     * @return Zeon_Artist_Block_Adminhtml_Artist_Edit_Tab_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $htmlIdPrefix = 'artist_information_';
        $form->setHtmlIdPrefix($htmlIdPrefix);
        $fieldsetHtmlClass = 'fieldset-wide';
        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(array('tab_id' => $this->getTabId()));

        /* @var $model Zeon_Artist_Model_Artist */
        $model = Mage::registry('current_artist');
        $contents = $model->getDescription();

        $fieldset = $form->addFieldset(
            'base_fieldset', 
            array(
                'legend'=>Mage::helper('zeon_artist')->__('Artist information'),
                'class'    => $fieldsetHtmlClass,
            )
        );

        if ($model->getArtistId()) {
            $fieldset->addField(
                'artist_id', 
                'hidden', 
                array(
                    'name'    => 'artist_id',
                )
            );
        }

        $fieldset->addField(
            'artist', 
            'select', 
            array(
                'label'    => Mage::helper('zeon_artist')->__('Artist'),
                'name'     => 'artist',
                'required' => true,
                'options'  => Mage::getModel('zeon_artist/options')->getArtist(),
            )
        );

        $fieldset->addField(
            'identifier', 
            'text', 
            array(
                'label'    => Mage::helper('zeon_artist')->__('Identifier'),
                'name'     => 'identifier',
                'required' => false,
            )
        );

        $fieldset->addField(
            'status', 
            'select', 
            array(
                'label'    => Mage::helper('zeon_artist')->__('Status'),
                'name'     => 'status',
                'required' => 'true',
                'disabled' => (bool)$model->getIsReadonly(),
                'options'  => Mage::getModel('zeon_artist/status')->getAllOptions(),
            )
        );

        if (!$model->getId()) {
            $model->setData('status', Zeon_Artist_Model_Status::STATUS_ENABLED);
			$model->setData('display_authorized_dealer', Zeon_Artist_Model_Status::STATUS_ENABLED);
        }
        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField(
                'store_ids', 
                'multiselect', 
                array(
                    'name'      => 'store_ids[]',
                    'label'     => Mage::helper('zeon_artist')->__('Visible In'),
                    'required'  => true,
                    'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                    'value'        => $model->getStoreIds(),
                )
            );
        } else {
            $fieldset->addField(
                'store_id', 
                'hidden', 
                array(
                    'name'    => 'store_ids[]',
                    'value'    => Mage::app()->getStore(true)->getId()
                )
            );
            $model->setStoreIds(Mage::app()->getStore(true)->getId());
        }

        $fieldset->addField(
            'is_display_home', 
            'select', 
            array(
                'label'    => Mage::helper('zeon_artist')->__('Display on Frontend'),
                'name'     => 'is_display_home',
                'required' => 'true',
                'disabled' => (bool)$model->getIsReadonly(),
                'options'  => Mage::getModel('zeon_artist/status')->getAllOptions(),
           )
        ); 

        $options[] = array(
            'value'     => '',
            'label'     => '',
        );
		
		/* Akash Kumar Updated 05 May 2015 */
		$fieldset->addField(
            'display_authorized_dealer', 
            'select', 
            array(
                'label'    => Mage::helper('zeon_artist')->__('Display Authorized Dealer'),
                'name'     => 'display_authorized_dealer',
                'required' => false,
                'disabled' => (bool)$model->getIsReadonly(),
                'options'  => Mage::getModel('zeon_artist/status')->getAllOptions(),
            )
        );
		
        $fieldset->addField(
            'artist_logo', 
            'Thumbnail', 
            array(
                'label'     => Mage::helper('zeon_artist')->__('Artist Logo'),
                'required' => 'true',
                'name'      => 'artist_logo',
            )
        );
            $fieldset->addField(
            'artist_banner', 
            'Thumbnail', 
            array(
                'label'     => Mage::helper('zeon_artist')->__('Artist Banner'),
                'required'  => false,
                'name'      => 'artist_banner',
            )
        );
         $fieldset->addField(
            'artist_image',
           'image',
            array(
                'label'     => Mage::helper('zeon_artist')->__('Artist Image'),
                'required' => false,
                'name'      => 'artist_image',
            )
        );
           $fieldset->addField(
            'artist_image1', 
       'image',
            array(
                'label'     => Mage::helper('zeon_artist')->__('Artist Image'),
                'required' => false,
                'name'      => 'artist_image1',
            )
        );
           $fieldset->addField(
            'artist_image2', 
       'image',
            array(
                'label'     => Mage::helper('zeon_artist')->__('Artist Image'),
                'required' => false,
                'name'      => 'artist_image2',
            )
        );
            $fieldset->addField(
            'artist_image3', 
       'image',
            array(
                'label'     => Mage::helper('zeon_artist')->__('Artist Image'),
                'required' => false,
                'name'      => 'artist_image3',
            )
        );
 $fieldset->addField(
            'artist_image4', 
       'image',
            array(
                'label'     => Mage::helper('zeon_artist')->__('Artist Image'),
                'required' => false,
                'name'      => 'artist_image4',
            )
        );
 $fieldset->addField(
            'artist_image5', 
       'image',
            array(
                'label'     => Mage::helper('zeon_artist')->__('Artist Image'),
                'required' => false,
                'name'      => 'artist_image5',
            )
        );

		$fieldset->addField(
            'artist_event', 
            'editor', 
            array(
                'name'      => 'artist_event',
                'label'     => Mage::helper('zeon_artist')->__('Artist Event'),
                'title'     => Mage::helper('zeon_artist')->__('Artist Event'),
               
                'required'  => false,
                'config'    => $wysiwygConfig,
            )
        );


        $fieldset->addField(
            'description', 
            'editor', 
            array(
                'name'      => 'description',
                'label'     => Mage::helper('zeon_artist')->__('Description'),
                'title'     => Mage::helper('zeon_artist')->__('Description'),
                'style'     => 'height:36em',
                'required'  => false,
                'config'    => $wysiwygConfig,
            )
        );	
          $fieldset->addField(
            'description1', 
            'editor', 
            array(
                'name'      => 'description1',
                'label'     => Mage::helper('zeon_artist')->__('Title'),
                'title'     => Mage::helper('zeon_artist')->__('Title'),
                'style'     => 'height:36em',
                'required'  => false,
                'config'    => $wysiwygConfig,
            )
        );	

            $fieldset->addField(
            'description2', 
            'editor', 
            array(
                'name'      => 'description2',
                'label'     => Mage::helper('zeon_artist')->__('Artist Buzz'),
                'title'     => Mage::helper('zeon_artist')->__('Artist Buzz'),
                'style'     => 'height:36em',
                'required'  => false,
                'config'    => $wysiwygConfig,
            )
        );	


        $fieldset->addField(
            'sort_order', 
            'text', 
            array(
                'label'        => Mage::helper('zeon_artist')->__('Sort Order'),
                'name'         => 'sort_order',
            )
        );
		
        $form->setValues($model->getData());
        $this->setForm($form);
        return $this;
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('zeon_artist')->__('Artist Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }
}