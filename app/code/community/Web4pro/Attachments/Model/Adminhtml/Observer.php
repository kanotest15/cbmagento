<?php
/**
 * WEB4PRO - Creating profitable online stores
 * 
 * @author WEB4PRO <srepin@corp.web4pro.com.ua>
 * @category  WEB4PRO
 * @package   Web4pro_Attachments
 * @copyright Copyright (c) 2015 WEB4PRO (http://www.web4pro.net)
 * @license   http://www.web4pro.net/license.txt
 */
/**
 * Adminhtml observer
 *
 * @category    Web4pro
 * @package     Web4pro_Attachments
 * @author      WEB4PRO <srepin@corp.web4pro.com.ua>
 */
class Web4pro_Attachments_Model_Adminhtml_Observer
{
    /**
     * check if tab can be added
     *
     * @access protected
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     * @author WEB4PRO <srepin@corp.web4pro.com.ua>
     */
    protected function _canAddTab($product)
    {
        if ($product->getId()) {
            return true;
        }
        if (!$product->getAttributeSetId()) {
            return false;
        }
        $request = Mage::app()->getRequest();
        if ($request->getParam('type') == 'configurable') {
            if ($request->getParam('attributes')) {
                return true;
            }
        }
        return false;
    }

    /**
     * add the attachment tab to products
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Web4pro_Attachments_Model_Adminhtml_Observer
     * @author WEB4PRO <srepin@corp.web4pro.com.ua>
     */
    public function addProductAttachmentBlock($observer)
    {
        $block = $observer->getEvent()->getBlock();
        $product = Mage::registry('product');
        if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs && $this->_canAddTab($product)) {
            $block->addTab(
                'attachments',
                array(
                    'label' => Mage::helper('web4pro_attachments')->__('Attachments'),
                    'url'   => Mage::helper('adminhtml')->getUrl(
                        'adminhtml/attachments_attachment_catalog_product/attachments',
                        array('_current' => true)
                    ),
                    'class' => 'ajax',
                )
            );
        }
        return $this;
    }

    /**
     * save attachment - product relation
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Web4pro_Attachments_Model_Adminhtml_Observer
     * @author WEB4PRO <srepin@corp.web4pro.com.ua>
     */
    public function saveProductAttachmentData($observer)
    {
        $post = Mage::app()->getRequest()->getPost('attachments', -1);
        if ($post != '-1') {
            $post = Mage::helper('adminhtml/js')->decodeGridSerializedInput($post);
            $product = Mage::registry('product');
            $attachmentProduct = Mage::getResourceSingleton('web4pro_attachments/attachment_product')
                ->saveProductRelation($product, $post);
        }
        return $this;
    }}
