<?php
class Zeon_Artist_Model_Mysql4_Artist extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        // Note that the artist_id refers to the key field in your database table.
        $this->_init('zeon_artist/artist', 'artist_id');
    }

    /**
     * Process Artist data before saving
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Zeon_Artist_Model_Resource_Artist
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$this->getIsUniqueArtistToStores($object)) {
            Mage::throwException(
                Mage::helper('zeon_artist')->__('A artist URL key for specified store already exists.')
            );
        }

        if ($this->isNumericArtistIdentifier($object)) {
            Mage::throwException(
                Mage::helper('zeon_artist')->__('The Artist URL key cannot consist only of numbers.')
            );
        }
        // modify create / update dates
        if ($object->isObjectNew() && !$object->hasCreationTime()) {
            $object->setCreationTime(Mage::getSingleton('core/date')->gmtDate());
        }

        $object->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());

        return parent::_beforeSave($object);
    }

     /**
     *  Check whether artist identifier is valid
     *
     *  @param    Mage_Core_Model_Abstract $object
     *  @return   bool
     */

    protected function isNumericArtistIdentifier(Mage_Core_Model_Abstract $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('identifier'));
    }

    /**
     * Check for unique of identifier of artist(s) to selected store(s).
     *
     * @param Mage_Core_Model_Abstract $object
     * @return bool
     */
    public function getIsUniqueArtistToStores(Mage_Core_Model_Abstract $object)
    {
        if (Mage::app()->isSingleStoreMode() || !$object->hasData('store_ids')) {
            $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID);
        } else {
            $stores = (array)$object->getData('store_ids');
        }

        $selectId = $this->_getCommanArtist($object->getData('artist'), $stores);
        $fetchedSelectId = $this->_getWriteAdapter()->fetchRow($selectId);

        if (!$object['artist_id']) {
            if ($fetchedSelectId['artist_id']) {
                return false;
            }
        } elseif ($object['artist'] == $fetchedSelectId['artist'] && $object['artist_id'] != $fetchedSelectId['artist_id']) {
                return false;
        }

        $select = $this->_getLoadByIdentifierSelect($object->getData('identifier'), $stores);

        if ($object->getId()) {
            $select->where('mps.artist_id <> ?', $object->getId());
        }

        if ($this->_getWriteAdapter()->fetchRow($select)) {
            return false;
        }
        return true;
    }

    /**
     * Load store Ids array
     *
     * @param Zeon_Artist_Model_Artist $object
     */
    public function loadStoreIds(Zeon_Artist_Model_Artist $object)
    {
        $pollId   = $object->getId();
        $storeIds = array();
        if ($pollId) {
            $storeIds = $this->lookupStoreIds($pollId);
        }
        $object->setStoreIds($storeIds);
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($id)
    {
        return $this->_getReadAdapter()->fetchCol(
            $this->_getReadAdapter()->select()
            ->from(
                $this->getTable(
                    'zeon_artist/store'
                ),
                'store_id'
            )
            ->where("{$this->getIdFieldName()} = :id_field"),
            array(':id_field' => $id)
        );
    }

    /**
     * Get artist name
     *
     * @param int $id
     * @return array
     */
    public function getArtistName($id, $storeId)
    {
        $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID, $storeId);
        $select = $this->_getReadAdapter()->select()
            ->from(array('eaov' => $this->getTable('eav/attribute_option_value')))
            ->join(
                array('eao' => $this->getTable('eav/attribute_option')),
                'eaov.option_id = eao.option_id',
                array()
            )
           ->where('eao.option_id = ?', $id)
           ->where('eaov.store_id IN (?)', $stores);

        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('eaov.value');
            return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Retrieve load select with filter by artist, store and activity
     *
     * @param string $identifier
     * @param int|array $store
     * @param int $isActive
     * @return Varien_Db_Select
     */
    protected function _getCommanArtist($id, $store, $isActive = null)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('mp' => $this->getMainTable()))
            ->join(
                array('mps' => $this->getTable('zeon_artist/store')),
                'mp.artist_id = mps.artist_id',
                array()
            )
            ->where('mp.artist = ?', $id)
            ->where('mps.store_id IN (?)', $store);

        if (!is_null($isActive)) {
            $select->where('mp.status = ?', $isActive);
        }
        return $select;
    }

    /**
     * Retrieve load select with filter by identifier, store and activity
     *
     * @param string $identifier
     * @param int|array $store
     * @param int $isActive
     * @return Varien_Db_Select
     */
    protected function _getLoadByIdentifierSelect($identifier, $store, $isActive = null)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('mp' => $this->getMainTable()))
            ->join(
                array('mps' => $this->getTable('zeon_artist/store')),
                'mp.artist_id = mps.artist_id',
                array()
            )
            ->where('mp.identifier = ?', $identifier)
            ->where('mps.store_id IN (?)', $store);

        if (!is_null($isActive)) {
            $select->where('mp.status = ?', $isActive);
        }
        return $select;
    }

    /**
     * Check if artist identifier exist for specific store
     * return page id if page exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return int
     */

    public function checkIdentifier($identifier, $storeId)
    {
        $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID, $storeId);
        $select = $this->_getLoadByIdentifierSelect($identifier, $stores, 1);
        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('mp.artist_id')
            ->order('mps.store_id DESC')
            ->limit(1);

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Delete current artist from the table zeon_artist_store and then
     * insert to update "artist to store" relations
     *
     * @param Mage_Core_Model_Abstract $object
     */
    public function saveArtistStore(Mage_Core_Model_Abstract $object)
    {
        /** stores */
        $deleteWhere = $this->_getReadAdapter()->quoteInto('artist_id = ?', $object->getId());
        $this->_getReadAdapter()->delete($this->getTable('zeon_artist/store'), $deleteWhere);

        foreach ($object->getStoreIds() as $storeId) {
            $artistStoreData = array(
            'artist_id'   => $object->getId(),
            'store_id'  => $storeId
            );
            $this->_getWriteAdapter()->insert($this->getTable('zeon_artist/store'), $artistStoreData);
        }
    }
}