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
class Magestore_RewardPoints_Model_Total_Quote_Point extends Mage_Sales_Model_Quote_Address_Total_Abstract {

    protected $_hiddentBaseDiscount = 0;
    protected $_hiddentDiscount = 0;
    
    public function __construct() {
        $this->setCode('rewardpoints');
    }

    /**
     * collect reward points total
     * 
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Magestore_RewardPoints_Model_Total_Quote_Point
     */
    public function collect(Mage_Sales_Model_Quote_Address $address) {
        $quote = $address->getQuote();
        $applyTaxAfterDiscount = (bool)Mage::getStoreConfig(
            'rewardpoints/spending/discount_before_tax',
            $quote->getStoreId()
        );
        if (!$applyTaxAfterDiscount){
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

            $address->setBaseGrandTotal($address->getBaseGrandTotal() - $baseDiscount + $this->_hiddentBaseDiscount);
            $address->setGrandTotal($address->getGrandTotal() - $discount + $this->_hiddentDiscount);

            $address->setRewardpointsSpent($address->getRewardpointsSpent() + $pointUsed);
            $address->setRewardpointsBaseDiscount($address->getRewardpointsBaseDiscount() + $baseDiscount);
            $address->setRewardpointsDiscount($address->getRewardpointsDiscount() + $discount);
            
            $quote->setRewardpointsBaseDiscount($address->getRewardpointsBaseDiscount() - $this->_hiddentBaseDiscount);
            $quote->setRewardpointsDiscount($address->getRewardpointsDiscount() - $this->_hiddentBaseDiscount);
            
            $address->setRewardpointsBaseHiddenTaxAmount($this->_hiddentBaseDiscount);
            $address->setRewardpointsHiddenTaxAmount($this->_hiddentDiscount);
            $quote->setRewardpointsBaseHiddenTaxAmount($this->_hiddentBaseDiscount);
            $quote->setRewardpointsHiddenTaxAmount($this->_hiddentDiscount);
            
            $address->setMagestoreBaseDiscount($address->getMagestoreBaseDiscount() + $baseDiscount);
        }

        return $this;
    }
    
    /**
     * add spending points row into quote total
     * 
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Magestore_RewardPoints_Model_Total_Quote_Point
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address) {
        $quote = $address->getQuote();
        $applyTaxAfterDiscount = (bool)Mage::getStoreConfig(
            'rewardpoints/spending/discount_before_tax',
            $quote->getStoreId()
        );
        if (!$applyTaxAfterDiscount){
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
        $store=Mage::app()->getStore();
        foreach ($items as $item) {
            if ($item->getParentItemId())
                continue;
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $baseItemsPrice += $item->getQty() * ($child->getQty() * $child->getBasePrice() - $child->getBaseDiscountAmount());
                }
            } elseif ($item->getProduct()) {
                $baseItemsPrice += $item->getQty() * $item->getBasePrice() - $item->getBaseDiscountAmount() - $item->getRewardpointsBaseDiscount();
            }
        }

        $discountForShipping = Mage::getStoreConfig(
                        Magestore_RewardPoints_Helper_Calculation_Spending::XML_PATH_SPEND_FOR_SHIPPING, $address->getQuote()->getStoreId()
        );
        if ($discountForShipping) {
            $baseItemsPrice += $address->getBaseShippingAmount();
        }
        if ($baseItemsPrice < 0.0001)
            return $this;

        // Update discount for each item
        foreach ($items as $item) {
            if ($item->getParentItemId())
                continue;
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $baseItemPrice = $item->getQty() * ($child->getQty() * $child->getBasePrice() - $child->getBaseDiscountAmount());
                    $itemBaseDiscount = $baseDiscount * $baseItemPrice / $baseItemsPrice;
                    $itemDiscount = Mage::app()->getStore()->convertPrice($itemBaseDiscount);
                    $pointSpent = round($points * $baseItemPrice / $baseItemsPrice, 0, PHP_ROUND_HALF_DOWN);
                    $child->setRewardpointsBaseDiscount($child->getRewardpointsBaseDiscount() + $itemBaseDiscount)
                            ->setRewardpointsDiscount($child->getRewardpointsDiscount() + $itemDiscount)
                            ->setMagestoreBaseDiscount($child->getMagestoreBaseDiscount() + $itemBaseDiscount)
                            ->setRewardpointsSpent($child->getRewardpointsSpent() + $pointSpent);
                    $baseTaxableAmount = $child->getBaseTaxableAmount();
                    $taxableAmount = $child->getTaxableAmount();
                    $child->setBaseTaxableAmount($child->getBaseTaxableAmount() - $child->getRewardpointsBaseDiscount());
                    $child->setTaxableAmount($child->getTaxableAmount() - $child->getRewardpointsDiscount());
                    if($child->getBaseTaxableAmount() < 0){
                        $child->setBaseTaxableAmount(0);
                        $child->setTaxableAmount(0);
                    }
                    
                    if(Mage::helper('tax')->priceIncludesTax()){
                        $rate = $this->getItemRateOnQuote($child->getProduct(), $store);
                        $hiddenBaseTaxBeforeDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($baseTaxableAmount, $rate, true, false);
                        $hiddenTaxBeforeDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($taxableAmount, $rate, true, false);

                        $hiddenBaseTaxAfterDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($child->getBaseTaxableAmount(), $rate, true, false);
                        $hiddenTaxAfterDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($child->getTaxableAmount(), $rate, true, false);

                        $hiddentBaseDiscount = Mage::getSingleton('tax/calculation')->round($hiddenBaseTaxBeforeDiscount) - Mage::getSingleton('tax/calculation')->round($hiddenBaseTaxAfterDiscount);
                        $hiddentDiscount = Mage::getSingleton('tax/calculation')->round($hiddenTaxBeforeDiscount) - Mage::getSingleton('tax/calculation')->round($hiddenTaxAfterDiscount);
                        
                        $child->setRewardpointsBaseHiddenTaxAmount($hiddentBaseDiscount);
                        $child->setRewardpointsHiddenTaxAmount($hiddentDiscount);

                        $this->_hiddentBaseDiscount += $hiddentBaseDiscount;
                        $this->_hiddentDiscount += $hiddentDiscount;
                    }
                }
            } elseif ($item->getProduct()) {
                $baseItemPrice = $item->getQty() * $item->getBasePrice() - $item->getBaseDiscountAmount() - $item->getRewardpointsBaseDiscount();
                $itemBaseDiscount = $baseDiscount * $baseItemPrice / $baseItemsPrice;
                $itemDiscount = Mage::app()->getStore()->convertPrice($itemBaseDiscount);
                $pointSpent = round($points * $baseItemPrice / $baseItemsPrice, 0, PHP_ROUND_HALF_DOWN);
                $item->setRewardpointsBaseDiscount($item->getRewardpointsBaseDiscount() + $itemBaseDiscount)
                        ->setRewardpointsDiscount($item->getRewardpointsDiscount() + $itemDiscount)
                        ->setMagestoreBaseDiscount($item->getMagestoreBaseDiscount() + $itemBaseDiscount)
                        ->setRewardpointsSpent($item->getRewardpointsSpent() + $pointSpent);
                
                $baseTaxableAmount = $item->getBaseTaxableAmount();
                $taxableAmount = $item->getTaxableAmount();
                $item->setBaseTaxableAmount($item->getBaseTaxableAmount() - $item->getRewardpointsBaseDiscount());
                $item->setTaxableAmount($item->getTaxableAmount() - $item->getRewardpointsDiscount());
                if($item->getBaseTaxableAmount() < 0){
                    $item->setBaseTaxableAmount(0);
                    $item->setTaxableAmount(0);
                }
                
                if(Mage::helper('tax')->priceIncludesTax()){
                    $rate = $this->getItemRateOnQuote($item->getProduct(), $store);
                    $hiddenBaseTaxBeforeDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($baseTaxableAmount, $rate, true, false);
                    $hiddenTaxBeforeDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($taxableAmount, $rate, true, false);

                    $hiddenBaseTaxAfterDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($item->getBaseTaxableAmount(), $rate, true, false);
                    $hiddenTaxAfterDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($item->getTaxableAmount(), $rate, true, false);

                    
                    $hiddentBaseDiscount = Mage::getSingleton('tax/calculation')->round($hiddenBaseTaxBeforeDiscount) - Mage::getSingleton('tax/calculation')->round($hiddenBaseTaxAfterDiscount);
                    $hiddentDiscount = Mage::getSingleton('tax/calculation')->round($hiddenTaxBeforeDiscount) - Mage::getSingleton('tax/calculation')->round($hiddenTaxAfterDiscount);
                    
                    $item->setRewardpointsBaseHiddenTaxAmount($hiddentBaseDiscount);
                    $item->setRewardpointsHiddenTaxAmount($hiddentDiscount);
                    
                    $this->_hiddentBaseDiscount += $hiddentBaseDiscount;
                    $this->_hiddentDiscount += $hiddentDiscount;
                }
            }
        }
        if ($discountForShipping) {
            $itemBaseDiscount = $baseDiscount * $address->getBaseShippingAmount() / $baseItemsPrice;
            $itemDiscount = Mage::app()->getStore()->convertPrice($itemBaseDiscount);
            $address->setRewardpointsBaseAmount($itemBaseDiscount)
                    ->setMagestoreBaseDiscountForShipping($address->getMagestoreBaseDiscountForShipping() + $itemBaseDiscount)
                    ->setRewardpointsAmount($itemDiscount);
            $baseTaxableAmount = $address->getBaseShippingTaxable();
            $taxableAmount = $address->getShippingTaxable();
            $address->setBaseShippingTaxable($address->getBaseShippingTaxable() - $address->getRewardpointsBaseAmount());
            $address->setShippingTaxable($address->getShippingTaxable() - $address->getRewardpointsAmount());
            if($address->getBaseShippingTaxable() < 0){
                $address->setBaseShippingTaxable(0);
                $address->setShippingTaxable(0);
            }
            if(Mage::helper('tax')->shippingPriceIncludesTax()){
                $rate = $this->getShipingTaxRate($address, $store);
                $hiddenBaseTaxBeforeDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($baseTaxableAmount, $rate, true, false);
                $hiddenTaxBeforeDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($taxableAmount, $rate, true, false);

                $hiddenBaseTaxAfterDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($address->getBaseShippingTaxable(), $rate, true, false);
                $hiddenTaxAfterDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($address->getShippingTaxable(), $rate, true, false);

                $this->_hiddentBaseDiscount += Mage::getSingleton('tax/calculation')->round($hiddenBaseTaxBeforeDiscount) - Mage::getSingleton('tax/calculation')->round($hiddenBaseTaxAfterDiscount);
                $this->_hiddentDiscount += Mage::getSingleton('tax/calculation')->round($hiddenTaxBeforeDiscount) - Mage::getSingleton('tax/calculation')->round($hiddenTaxAfterDiscount);
            }
        }

        return $this;
    }
    
    //get Rate
    public function getItemRateOnQuote($product, $store){
        //Calculate rate to subtract taxable amount
        $priceIncludesTax = (bool)Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_PRICE_INCLUDES_TAX, $store);
        $taxClassId = $product->getTaxClassId();
        if ($taxClassId && $priceIncludesTax) {
            $request = Mage::getSingleton('tax/calculation')->getRateRequest(false, false, false, $store);
            $rate = Mage::getSingleton('tax/calculation')
                ->getRate($request->setProductClassId($taxClassId));
            return $rate;
        }
        return 0;
    }
    public function getShipingTaxRate($address, $store){        
        $request = Mage::getSingleton('tax/calculation')->getRateRequest(
                        $address,
                        $address->getQuote()->getBillingAddress(),
                        $address->getQuote()->getCustomerTaxClassId(),
                        $store
                    );
        $request->setProductClassId(Mage::getSingleton('tax/config')->getShippingTaxClass($store));
        $rate = Mage::getSingleton('tax/calculation')->getRate($request);
        return $rate;
    }

}
