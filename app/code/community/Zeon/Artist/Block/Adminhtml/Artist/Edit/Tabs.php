<?php
class Zeon_Artist_Block_Adminhtml_Artist_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize artist edit page tabs
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('artist_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('zeon_artist')->__('Artist Information'));
    }
}