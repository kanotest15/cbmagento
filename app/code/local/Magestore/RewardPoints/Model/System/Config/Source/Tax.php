<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * RewardPoints Config Source Rounding Model
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Magestore_RewardPoints_Model_System_Config_Source_Tax
{
    /**
     * Options getter
     * 
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => '1', 'label' => Mage::helper('rewardpoints')->__('Before Tax')),
            array('value' => '0', 'label' => Mage::helper('rewardpoints')->__('After Tax')),
        );
    }
}
