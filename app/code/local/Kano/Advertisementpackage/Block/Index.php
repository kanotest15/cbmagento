<?php   
class Kano_Tender_Block_Index extends Mage_Core_Block_Template{   
 
	public function getKanotender(){
		$model = Mage::getModel('tender/tender')->getCollection();
//                zend_debug::dump($model->getData());exit;
//$data = $model->getCollection();
//        foreach ($model as $value) {
//            
//          print_r($value['emd']);  
//          
//            
//        }

	 return $model;
 
        }
        
        
        public function getTenderDetails($param) {
            $model = Mage::getSingleton('tender/tender')->load($param);
            return $model;
            
        }
    
}