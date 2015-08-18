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
 * Rewardpoints Spend for Order by Point Model
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Magestore_RewardPoints_Model_Total_Quote_Pointaftertax extends Mage_Sales_Model_Quote_Address_Total_Abstract {

    public function __construct() {
        $this->setCode('rewardpoints_after_tax');
    }

    /**
     * collect reward points total
     * 
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Magestore_RewardPoints_Model_Total_Quote_Point
     */
    public function collect(Mage_Sales_Model_Quote_Address $address) {
        $quote = $address->getQuote();
        $applyTaxAfterDiscount = (bool) Mage::getStoreConfig(
                        'rewardpoints/spending/discount_before_tax', $quote->getStoreId()
        );
        if ($applyTaxAfterDiscount) {
            return $this;
        }
        if (!Mage::helper('rewardpoints')->isEnable($quote->getStoreId())) {
            return $this;
        }
        if ($quote->isVirtual() && $address->getAddressType() == 'shipping') {
            return $this;
        }
        if (!$quote->isVirtual() && $address->getAddressType() == 'billing') {
            return $this;
        }
        $session = Mage::getSingleton('checkout/session');
        if (!$session->getData('use_point')) {
            return $this;
        }
        $rewardSalesRules = $session->getRewardSalesRules();
        $rewardCheckedRules = $session->getRewardCheckedRules();
        if (!$rewardSalesRules && !$rewardCheckedRules) {
            return $this;
        }

        /** @var $helper Magestore_RewardPoints_Helper_Calculation_Spending */
        $helper = Mage::helper('rewardpoints/calculation_spending');

        $baseTotal = $helper->getQuoteBaseTotal($quote, $address);
        $maxPoints = Mage::helper('rewardpoints/customer')->getBalance();
        if ($maxPointsPerOrder = $helper->getMaxPointsPerOrder($quote->getStoreId())) {
            $maxPoints = min($maxPointsPerOrder, $maxPoints);
        }
        $maxPoints -= $helper->getPointItemSpent();
        if ($maxPoints <= 0) {
            return $this;
        }

        $baseDiscount = 0;
        $pointUsed = 0;

        // Checked Rules Discount First
        if (is_array($rewardCheckedRules)) {
            $newRewardCheckedRules = array();
            foreach ($rewardCheckedRules as $ruleData) {
                if ($baseTotal < 0.0001)
                    break;
                $rule = $helper->getQuoteRule($ruleData['rule_id']);
                if (!$rule || !$rule->getId() || $rule->getSimpleAction() != 'fixed') {
                    continue;
                }
                if ($maxPoints < $rule->getPointsSpended()) {
                    $session->addNotice($helper->__('Not enough points to spend'));
                    continue;
                }
                $points = $rule->getPointsSpended();
                $ruleDiscount = $helper->getQuoteRuleDiscount($quote, $rule, $points);
                if ($ruleDiscount < 0.0001) {
                    continue;
                }

                $baseTotal -= $ruleDiscount;
                $maxPoints -= $points;

                $baseDiscount += $ruleDiscount;
                $pointUsed += $points;

                $newRewardCheckedRules[$rule->getId()] = array(
                    'rule_id' => $rule->getId(),
                    'use_point' => $points,
                    'base_discount' => $ruleDiscount,
                );
                if ($rule->getStopRulesProcessing()) {
                    break;
                }
            }
            $session->setRewardCheckedRules($newRewardCheckedRules);
        }

        // Sales Rule (slider) discount Last
        if (is_array($rewardSalesRules)) {
            $newRewardSalesRules = array();
            if ($baseTotal > 0.0 && isset($rewardSalesRules['rule_id'])) {
                $rule = $helper->getQuoteRule($rewardSalesRules['rule_id']);
                if ($rule && $rule->getId() && $rule->getSimpleAction() == 'by_price') {
                    $points = min($rewardSalesRules['use_point'], $maxPoints);
                    $ruleDiscount = $helper->getQuoteRuleDiscount($quote, $rule, $points);
                    if ($ruleDiscount > 0.0) {
                        $baseTotal -= $ruleDiscount;
                        $maxPoints -= $points;

                        $baseDiscount += $ruleDiscount;
                        $pointUsed += $points;

                        $newRewardSalesRules = array(
                            'rule_id' => $rule->getId(),
                            'use_point' => $points,
                            'base_discount' => $ruleDiscount,
                        );
                    }
                }
            }
            $session->setRewardSalesRules($newRewardSalesRules);
        }

        // verify quote total data
        if ($baseTotal < 0.0001) {
            $baseTotal = 0.0;
            $baseDiscount = $helper->getQuoteBaseTotal($quote, $address);
        }

        if ($baseDiscount) {
            // Prepare reward points discount and point spent for each item
            $this->_prepareDiscountForTaxAmount($address, $baseDiscount, $pointUsed);

            $discount = Mage::app()->getStore()->convertPrice($baseDiscount);

            $address->setBaseGrandTotal($address->getBaseGrandTotal() - $baseDiscount);
            $address->setGrandTotal($address->getGrandTotal() - $discount);

            $address->setRewardpointsSpent($address->getRewardpointsSpent() + $pointUsed);
            $address->setRewardpointsBaseDiscount($address->getRewardpointsBaseDiscount() + $baseDiscount);
            $address->setRewardpointsDiscount($address->getRewardpointsDiscount() + $discount);
            
            $quote->setRewardpointsBaseDiscount($address->getRewardpointsBaseDiscount());
            $quote->setRewardpointsDiscount($address->getRewardpointsDiscount());

            $address->setMagestoreBaseDiscount($address->getMagestoreBaseDiscount() + $baseDiscount);
        }
        return $this;
    }

     /**
     * add spending points row after tax into quote total
     * 
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Magestore_RewardPoints_Model_Total_Quote_Point
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address) {
        $quote = $address->getQuote();
        $applyTaxAfterDiscount = (bool) Mage::getStoreConfig(
                        'rewardpoints/spending/discount_before_tax', $quote->getStoreId()
        );
        if ($applyTaxAfterDiscount) {
            return $this;
        }
        if ($amount = $address->getRewardpointsDiscount()) {
            if ($points = $address->getRewardpointsSpent()) {
                $title = Mage::helper('rewardpoints')->__('Use point (%s)', Mage::helper('rewardpoints/point')->format($points, $address->getQuote()->getStoreId())
                );
            } else {
                $title = Mage::helper('rewardpoints')->__('Use point on spend');
            }
            $address->addTotal(array(
                'code' => $this->getCode(),
                'title' => $title,
                'value' => -$amount,
            ));
        }
        return $this;
    }

    /**
     * Prepare Discount Amount used for Tax
     * 
     * @param Mage_Sales_Model_Quote_Address $address
     * @param type $baseDiscount
     * @return Magestore_RewardPoints_Model_Total_Quote_Point
     */
    public function _prepareDiscountForTaxAmount(Mage_Sales_Model_Quote_Address $address, $baseDiscount, $points) {
        $items = $address->getAllItems();
        if (!count($items))
            return $this;

        // Calculate total item prices
        $baseItemsPrice = 0;
        foreach ($items as $item) {
            if ($item->getParentItemId())
                continue;
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $baseItemsPrice += $item->getQty() * ($child->getQty() * $child->getBasePrice() - $child->getBaseDiscountAmount() + $child->getBaseTaxAmount());
                }
            } elseif ($item->getProduct()) {
                $baseItemsPrice += $item->getQty() * $item->getBasePrice() - $item->getBaseDiscountAmount() - $item->getRewardpointsBaseDiscount() + $item->getBaseTaxAmount();
            }
        }

        $discountForShipping = Mage::getStoreConfig(
                        Magestore_RewardPoints_Helper_Calculation_Spending::XML_PATH_SPEND_FOR_SHIPPING, $address->getQuote()->getStoreId()
        );
        $discountForShippingTax = Mage::getStoreConfig(
                        Magestore_RewardPoints_Helper_Calculation_Spending::XML_PATH_SPEND_FOR_SHIPPING_TAX, $address->getQuote()->getStoreId()
        );
        if ($discountForShipping) {
            $baseItemsPrice += $address->getBaseShippingAmount();
            if ($discountForShippingTax) {
                $baseItemsPrice += $address->getBaseShippingTaxAmount();
            }
        }
        
        if ($baseItemsPrice < 0.0001)
            return $this;

        // Update discount for each item
        foreach ($items as $item) {
            if ($item->getParentItemId())
                continue;
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $baseItemPrice = $item->getQty() * ($child->getQty() * $child->getBasePrice() - $child->getBaseDiscountAmount() + $child->getBaseTaxAmount());
                    $itemBaseDiscount = $baseDiscount * $baseItemPrice / $baseItemsPrice;
                    $itemDiscount = Mage::app()->getStore()->convertPrice($itemBaseDiscount);
                    $pointSpent = round($points * $baseItemPrice / $baseItemsPrice, 0, PHP_ROUND_HALF_DOWN);
                    $child->setRewardpointsBaseDiscount($child->getRewardpointsBaseDiscount() + $itemBaseDiscount)
                            ->setRewardpointsDiscount($child->getRewardpointsDiscount() + $itemDiscount)
                            ->setMagestoreBaseDiscount($child->getMagestoreBaseDiscount() + $itemBaseDiscount)
                            ->setRewardpointsSpent($child->getRewardpointsSpent() + $pointSpent);
                }
            } elseif ($item->getProduct()) {
                $baseItemPrice = $item->getQty() * $item->getBasePrice() - $item->getBaseDiscountAmount() - $item->getRewardpointsBaseDiscount() + $item->getBaseTaxAmount();
                $itemBaseDiscount = $baseDiscount * $baseItemPrice / $baseItemsPrice;
                $itemDiscount = Mage::app()->getStore()->convertPrice($itemBaseDiscount);
                $pointSpent = round($points * $baseItemPrice / $baseItemsPrice, 0, PHP_ROUND_HALF_DOWN);
                $item->setRewardpointsBaseDiscount($item->getRewardpointsBaseDiscount() + $itemBaseDiscount)
                        ->setRewardpointsDiscount($item->getRewardpointsDiscount() + $itemDiscount)
                        ->setMagestoreBaseDiscount($item->getMagestoreBaseDiscount() + $itemBaseDiscount)
                        ->setRewardpointsSpent($item->getRewardpointsSpent() + $pointSpent);
            }
        }
        if ($discountForShipping) {
            $shipping = $address->getBaseShippingAmount();
            if ($discountForShippingTax) {
                $shipping += $address->getBaseShippingTaxAmount();
            }
            $itemBaseDiscount = $baseDiscount * $shipping / $baseItemsPrice;
            $itemDiscount = Mage::app()->getStore()->convertPrice($itemBaseDiscount);
            $address->setRewardpointsBaseAmount($itemBaseDiscount)
                    ->setMagestoreBaseDiscountForShipping($itemBaseDiscount)
                    ->setRewardpointsAmount($itemDiscount);
        }

        return $this;
    }

}
