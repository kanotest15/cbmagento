<?php
class Zeon_Artist_Model_Template_Filter extends Mage_Widget_Model_Template_Filter
{
    /**
     * Whether to allow SID in store directive: AUTO
     *
     * @var bool
     */
    protected $_useSessionInUrl = null;

    /**
     * Setter whether SID is allowed in store directive
     *
     * @param bool $flag
     * @return Zeon_News_Model_Template_Filter
     */
    public function setUseSessionInUrl($flag)
    {
        $this->_useSessionInUrl = (bool)$flag;
        return $this;
    }
}
