<?php

class Kano_Advertisementpackage_Block_Adminhtml_Advertisementpackage_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {

        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset("advertisementpackage_form", array("legend" => Mage::helper("advertisementpackage")->__("Package information")));
						
        $fieldset->addField("package_name", "text", array(
            "label" => Mage::helper("advertisementpackage")->__("Package Name"),
            "class" => "required-entry",
            "required" => true,
            "name" => "package_name",
        ));
        $fieldset->addField("package_information", "textarea", array(
            "label" => Mage::helper("advertisementpackage")->__("Package Information"),
            "class" => "required-entry",
            "required" => true,
            "name" => "package_information",
        ));
		$validity_comment= '<p class="nm"><small>(in days, Like:- 20,30 etc.)</small></p>';
        $fieldset->addField("package_validity", "text", array(
            "label" => Mage::helper("advertisementpackage")->__("Package Validity"),
            "class" => "required-entry",
            "required" => true,
            "name" => "package_validity",
			"after_element_html" => $validity_comment,
        ));
		$amount_comment= '<p class="nm"><small>(in common English Currency Format , Like:- 2000, 4150.50 etc.)</small></p>';
        $fieldset->addField("package_amount", "text", array(
            "label" => Mage::helper("advertisementpackage")->__("Package Amount"),
            "class" => "required-entry",
            "required" => true,
            "name" => "package_amount",
			"after_element_html" => $amount_comment,
        ));
		$email_comment= '<p class="nm"><small>(This field implies that after how many days will user get email. Please enter number of days.)</small></p>';
        $fieldset->addField("package_email_confirmation", "text", array(
            "label" => Mage::helper("advertisementpackage")->__("package email Confirmation"),
            "class" => "required-entry",
            "required" => false,
            "name" => "package_email_confirmation",
			"after_element_html" => $email_comment,
        ));
	
        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('advertisementpackage')->__('Status'),
            'values' => Kano_Advertisementpackage_Block_Adminhtml_Advertisementpackage_Grid::getValueArray12(),
            'name' => 'status',
            "class" => "required-entry",
            "required" => true,
        ));

        if (Mage::getSingleton("adminhtml/session")->getTenderData()) {
            $form->setValues(Mage::getSingleton("adminhtml/session")->getAdvertisementpackageData());
            Mage::getSingleton("adminhtml/session")->setAdvertisementpackageData(null);
        } elseif (Mage::registry("advertisementpackage_data")) {
            $form->setValues(Mage::registry("advertisementpackage_data")->getData());
        }
        return parent::_prepareForm();
    }

}
