<?php
class Zeon_Artist_Model_Config extends Mage_Eav_Model_Config
{
    const XML_PATH_LIST_DEFAULT_SORT_BY     = 'zeon_artist/frontend/default_sort_by';

    protected $_storeId = null;

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('zeon_artist/config');
    }

    /**
     * Set store id
     *
     * @param integer $storeId
     * @return Zeon_Artist_Model_Config
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }

    /**
     * Return store id, if is not set return current app store
     *
     * @return integer
     */
    public function getStoreId()
    {
        if ($this->_storeId === null) {
            return Mage::app()->getStore()->getId();
        }
        return $this->_storeId;
    }

    /**
     * Retrieve Attributes Used for Sort by as array
     * key = code, value = name
     *
     * @return array
     */
    public function getAttributeUsedForSortByArray()
    {
        $options = array(
            'title'  => Mage::helper('zeon_artist')->__('Artist Title'),
            'update_time'  => Mage::helper('zeon_artist')->__('Recent Artist')
        );

        return $options;
    }

    /**
     * Retrieve resource model
     *
     * @return Zeon_Artist_Model_Resource_Eav_Mysql4_Config
     */
    protected function _getResource()
    {
        return Mage::getResourceModel('zeon_artist/config');
    }

    /**
     * Retrieve Artist List Default Sort By
     *
     * @param mixed $store
     * @return string
     */
    public function getArtistListDefaultSortBy($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_LIST_DEFAULT_SORT_BY, $store);
    }
}
