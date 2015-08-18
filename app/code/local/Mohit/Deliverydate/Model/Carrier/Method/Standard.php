<?php
 
class Adepta_DeliveryTime_Model_Carrier_Method_Standard extends Adepta_DeliveryTime_Model_Carrier_Method_Abstract
{
    public function getCost()
    {
        return 3.00;
    }
 
    public function getPrice()
    {
        return 7.00;
    }
}