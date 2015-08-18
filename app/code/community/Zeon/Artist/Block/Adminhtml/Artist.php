<?php
class Zeon_Artist_Block_Adminhtml_Artist extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
    * Initialize artist manage page
    *
    * @return void
    */
    public function __construct()
    {
        $this->_controller = 'adminhtml_artist';
        $this->_blockGroup = 'zeon_artist';
        $this->_headerText = Mage::helper('zeon_artist')->__('Manage Artist');
        $this->_addButtonLabel = Mage::helper('zeon_artist')->__('Add Artist');
        parent::__construct();
    }
}