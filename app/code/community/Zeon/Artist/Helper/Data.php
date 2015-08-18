<?php
class Zeon_Artist_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLED = 'zeon_artist/general/is_enabled';
    const XML_PATH_DEFAULT_ATTRIBUTE_CODE = 'zeon_artist/frontend/artist_attribute_code';
    const XML_PATH_DEFAULT_META_TITLE = 'zeon_artist/frontend/meta_title';
    const XML_PATH_DEFAULT_META_KEYWORDS = 'zeon_artist/frontend/meta_keywords';
    const XML_PATH_DEFAULT_META_DESCRIPTION = 'zeon_artist/frontend/meta_description';
    
    public function setIsModuleEnabled($value)
    {
        Mage::getModel('core/config')->saveConfig(self::XML_PATH_ENABLED, $value);
    }

    /**
     * Retrieve default title for artist
     *
     * @return string
     */
    public function getArtistAttributeCode()
    {
        return Mage::getStoreConfig(self::XML_PATH_DEFAULT_ATTRIBUTE_CODE);
    }

    /**
     * Retrieve default title for artist
     *
     * @return string
     */
    public function getDefaultTitle()
    {
        return Mage::getStoreConfig(self::XML_PATH_DEFAULT_META_TITLE);
    }

    /**
     * Retrieve default meta keywords for artist
     *
     * @return string
     */
    public function getDefaultMetaKeywords()
    {
        return Mage::getStoreConfig(self::XML_PATH_DEFAULT_META_KEYWORDS);
    }

    /**
     * Retrieve default meta description for artist
     *
     * @return string
     */
    public function getDefaultMetaDescription()
    {
        return Mage::getStoreConfig(self::XML_PATH_DEFAULT_META_DESCRIPTION);
    }
    
    /**
     * Retrieve Template processor for Block Content
     *
     * @return Varien_Filter_Template
     */
    public function getBlockTemplateProcessor()
    {
        return Mage::getModel('zeon_artist/template_filter');
    }
}