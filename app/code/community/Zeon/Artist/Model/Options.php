<?php
class Zeon_Artist_Model_Options extends Mage_Core_Model_Abstract
{
    public function getArtist()
    {
        $attributeCode = Mage::helper('zeon_artist')->getArtistAttributeCode();

        $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', "$attributeCode");
        
        foreach ($attribute->getSource()->getAllOptions(true, true) as $option) {
             $attributeArray[$option['value']] = $option['label'];
        }
        return($attributeArray);
    }
}