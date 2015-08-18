<?php
class Zeon_Artist_Block_List extends Mage_Core_Block_Template
{
    protected $_artistCollection;

    /**
     * Retrieve Artists collection
     *
     * @return Zeon_Artist_Model_Resource_Artist_Collection
     */
    protected function _getArtistsCollection()
    {
        if (is_null($this->_artistCollection)) {
            $this->_artistCollection = Mage::getResourceModel('zeon_artist/artist_collection')
                                    ->distinct(true)
                                    ->addStoreFilter(Mage::app()->getStore()->getId())
                                    ->addFieldToFilter('status', Zeon_Artist_Model_Status::STATUS_ENABLED)
                                    ->addOrder('identifier', 'asc');
        }
        return $this->_artistCollection;
    }

    /**
     * Retrieve loaded Artists collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getArtistsCollection()
    {
        return $this->_getArtistsCollection();
    }

    /**
     * Prepare global layout
     *
     * @return Zeon_Artist_Block_List
     */
    protected function _prepareLayout()
    {
        $helper = Mage::helper('zeon_artist');
        // show breadcrumbs
        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb(
                'home', 
                array(
                    'label'=>$helper->__('Home'), 
                    'title'=>$helper->__('Go to Home Page'), 
                    'link'=>Mage::getBaseUrl()
                )
            );
            $breadcrumbs->addCrumb(
                'artist_list', 
                array(
                    'label'=>$helper->__('Artists'), 
                    'title'=>$helper->__('Artists')
                )
            );
        }
        $head = $this->getLayout()->getBlock('head');
        if ($head) {
            $head->setTitle($helper->getDefaultTitle());
            $head->setKeywords($helper->getDefaultMetaKeywords());
            $head->setDescription($helper->getDefaultMetaDescription());
        }

        return parent::_prepareLayout();
    }
}