<?php
class Zeon_Artist_Block_Left extends Mage_Core_Block_Template
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
                                    ->addOrder('sort_order', 'asc');
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
}