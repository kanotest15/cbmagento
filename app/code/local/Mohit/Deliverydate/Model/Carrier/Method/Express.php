<?php
 
class Adepta_DeliveryTime_Model_Carrier_Method_Express extends 
Adepta_DeliveryTime_Model_Carrier_Method_Abstract 
{
    public function getCost()
    {
        return 5.00;
    }
 
    public function getPrice()
    {
        return 10.00;
    }
}