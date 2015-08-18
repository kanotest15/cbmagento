<?php
class Zeon_Artist_Model_Artist extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('zeon_artist/artist');
    }

    /**
     * Check if artist identifier exist for specific store
     * return artist id if artist exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return int
     */
    public function checkIdentifier($identifier, $storeId)
    {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }

    /**
     * Save artist store, after artist save
     *
     * @return Zeon_Artist_Model_Artist
     */
    protected function _afterSave()
    {
        if ($this->hasStoreIds()) {
            $this->_getResource()->saveArtistStore($this);
        }
        return parent::_afterSave();
    }

    public function addStoreId($storeId)
    {
        $ids = $this->getStoreIds();
        if (!in_array($storeId, $ids)) {
            $ids[] = $storeId;
        }
        $this->setStoreIds($ids);
        return $this;
    }

    public function getStoreIds()
    {
        $ids = $this->_getData('store_ids');
        if (is_null($ids)) {
            $this->loadStoreIds();
            $ids = $this->getData('store_ids');
        }
        return $ids;
    }

    public function loadStoreIds()
    {
        $this->_getResource()->loadStoreIds($this);
    }

    function addError($error)
    {
        $this->_errors[] = $error;
    }

    function getErrors()
    {
        return $this->_errors;
    }

    function resetErrors()
    {
        $this->_errors = array();
    }

    function printError($error, $line = null)
    {
        if ($error == null) return false;
        $img = 'error_msg_icon.gif';
        $liStyle = 'background-color:#FDD; ';
        echo '<li style="'.$liStyle.'">';
        echo '<img src="'.Mage::getDesign()->getSkinUrl('images/'.$img).'" class="v-middle"/>';
        echo $error;
        if ($line) {
            echo '<small>, Line: <b>'.$line.'</b></small>';
        }
        echo "</li>";
    }

    public function getArtistName($id, $storeId)
    {
        return $this->_getResource()->getArtistName($id, $storeId);
    }
}