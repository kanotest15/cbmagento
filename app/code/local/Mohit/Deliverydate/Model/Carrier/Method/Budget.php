<?php
 
class Adepta_DeliveryTime_Model_Carrier_Method_Budget extends Adepta_DeliveryTime_Model_Carrier_Method_Abstract
{
    public function getCost()
    {
        return 1.00;
    }
 
    public function getPrice()
    {
        return 3.00;
    }
}