<?php
class Zeon_Artist_Block_Adminhtml_Artist_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form before rendering HTML
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(
            array(
                'id' => 'edit_form', 
                'action' => $this->getData('action'), 
                'method' => 'post', 
                'enctype' => 'multipart/form-data'
            )
        );

        $artist = Mage::registry('current_artist');

        if ($artist->getId()) {
            $form->addField('artist_id', 'hidden', array('name' => 'artist_id'));
            $form->setValues($artist->getData());
        }

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}