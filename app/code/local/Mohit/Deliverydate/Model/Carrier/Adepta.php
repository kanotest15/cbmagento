<?php
class Adepta_DeliveryTime_Model_Carrier_Adepta extends Mage_Shipping_Model_Carrier_Abstract
implements Mage_Shipping_Model_Carrier_Interface {
 
    protected $_code = 'adeptacarrier';
 
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigFlag('active')) 
        {
            return false;
        }
 
        $result = Mage::getModel('shipping/rate_result');
 
        foreach($this->getAllowedMethods() as $methodName => $methodTitle)
        {
            $method = Mage::getModel('shipping/rate_result_method');
            $methodModel = Mage::getModel("adepta_deliverytime/carrier_method_{$methodName}");
            $method->setCarrier($this->_code);
            $method->setMethod($methodName);
            $method->setCarrierTitle($this->getConfigData('title'));
            $method->setMethodTitle($methodTitle);
            $method->setPrice($methodModel->getPrice());
            $method->setCost($methodModel->getCost());
            $result->append($method);
        }
 
        return $result;
    }
 
    public function getAllowedMethods()
    {
        $methods = array('express' => 'Express', 'standard' => 'Standard', 'budget' =>'Budget');  
        return $methods;
    }
}