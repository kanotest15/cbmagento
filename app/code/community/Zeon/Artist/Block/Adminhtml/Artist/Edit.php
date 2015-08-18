<?php
class Zeon_Artist_Block_Adminhtml_Artist_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize artist edit page. Set management buttons
     *
     */
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_artist';
        $this->_blockGroup = 'zeon_artist';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('zeon_artist')->__('Save Artist'));
        $this->_updateButton('delete', 'label', Mage::helper('zeon_artist')->__('Delete Artist'));

        $this->_addButton(
            'save_and_edit_button', array(
            'label'   => Mage::helper('zeon_artist')->__('Save and Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class'   => 'save'
            ), 100
        );
        $this->_formScripts[] = "
            function saveAndContinueEdit() {
            editForm.submit($('edit_form').action + 'back/edit/');}";
    }

    /**
     * Get current loaded artist ID
     *
     */
    public function getArtistId()
    {
        return Mage::registry('current_artist')->getId();
    }

    /**
     * Get header text for artist edit page
     *
     */
    public function getHeaderText()
    {
        if (Mage::registry('current_artist')->getId()) {
            return $this->htmlEscape(Mage::registry('current_artist')->getTitle());
        } else {
            return Mage::helper('zeon_artist')->__('New Artist');
        }
    }

    /**
     * Get form action URL
     *
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('*/*/save');
    }
}