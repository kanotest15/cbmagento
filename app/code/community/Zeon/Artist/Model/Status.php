<?php
class Zeon_Artist_Model_Status extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 0;

    static public function getAllOptions()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('zeon_artist')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('zeon_artist')->__('Disabled')
        );
    }
}