<?php
class Zeon_Artist_Model_Mysql4_Artist_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('zeon_artist/artist');
        $this->_map['fields']['artist_id'] = 'main_table.artist_id';
        $this->_map['fields']['update_time'] = 'main_table.update_time';
        $this->_map['fields']['status'] = 'main_table.status';
    }

    /**
     * Add stores column
     *
     * @return Zeon_Artist_Model_Mysql4_Artist_Collection
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        if ($this->getFlag('add_stores_column')) {
            $this->_addStoresVisibility();
        }
        $this->_addArtistName();
        return $this;
    }

    /**
     * Set add stores column flag
     *
     * @return Zeon_Artist_Model_Mysql4_Artist_Collection
     */
    public function addStoresVisibility()
    {
        $this->setFlag('add_stores_column', true);
        return $this;
    }

    /**
     * Collect and set stores ids to each collection item
     * Used in artist grid as Visible in column info
     *
     * @return Zeon_Artist_Model_Mysql4_Artist_Collection
     */
    protected function _addStoresVisibility()
    {
        $artistIds = $this->getColumnValues('artist_id');
        $artistStores = array();
        if (sizeof($artistIds)>0) {
            $select = $this->getConnection()->select()
                ->from($this->getTable('zeon_artist/store'), array('store_id', 'artist_id'))
                ->where('artist_id IN(?)', $artistIds);
            $artistRaw = $this->getConnection()->fetchAll($select);
            foreach ($artistRaw as $artist) {
                if (!isset($artistStores[$artist['artist_id']])) {
                    $artistStores[$artist['artist_id']] = array();
                }

                $artistStores[$artist['artist_id']][] = $artist['store_id'];
            }
        }

        foreach ($this as $item) {
            if (isset($artistStores[$item->getId()])) {
                $item->setStores($artistStores[$item->getId()]);
            } else {
                $item->setStores(array());
            }
        }

        return $this;
    }

    /**
     * Collect and set Artist title to each collection item
     * Used in artist grid as Category in column info
     *
     * @return Zeon_Artist_Model_Mysql4_Artist_Collection
     */
    protected function _addArtistName()
    {
        $artistIds = $this->getColumnValues('artist');
        $artists = array();
            if (sizeof($artistIds)>0) {
            $select = $this->getConnection()->select()
                ->from($this->getTable('eav/attribute_option_value'), array('option_id','value'))
                ->where('option_id IN(?)', $artistIds);
            $artistRaw = $this->getConnection()->fetchAll($select);

                foreach ($artistRaw as $artist) {
                    $artists[$artist['option_id']] = array();
                    $artists[$artist['option_id']] = $artist['value'];
                }
            }

        foreach ($this as $item) {
            if (isset($artists[$item->getArtist()])) {
                $item->setArtist($artists[$item->getArtist()]);
            } else {
                $item->setArtist('');
            }
        }
        return $this;
    }

    /**
     * Add Filter by store
     *
     * @param int|array $storeIds
     * @param bool $withAdmin
     * @return Zeon_Artist_Model_Mysql4_Artist_Collection
     */
    public function addStoreFilter($storeIds, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter')) {
            if ($withAdmin) {
                $storeIds = array(0, $storeIds);
            }

            $this->getSelect()->join(
                array('store_table' => $this->getTable('zeon_artist/store')),
                'main_table.artist_id = store_table.artist_id',
                array()
            )
            ->where('store_table.store_id in (?)', $storeIds);
            $this->setFlag('store_filter', true);
        }
        return $this;
    }

    /**
     * Add Filter by category
     *
     * @param string $artistName
     * @return Zeon_Artist_Model_Mysql4_Artist_Collection
     */
    /*public function addArtistNameFilter($artistName)
    {
        if (!$this->getFlag('artist_name_filter')) {
			$artist_attr= Mage::getModel('zeon_artist/options')->getArtist();
			foreach($artist_attr as $key=>$value){
				//if(strtolower($value)==strtolower($artistName)){
				$artist = strtolower($value);
				$search = strtolower($artistName);
				if(strpos($artist,$search) !== false){
					$data = $key;
					break;
				}
			}
        }
        return $data;
    }*/
}