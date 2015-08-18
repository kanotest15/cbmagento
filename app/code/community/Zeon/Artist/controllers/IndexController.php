<?php
class Zeon_Artist_IndexController extends Mage_Core_Controller_Front_Action
{
    const XML_PATH_ENABLED = 'zeon_artist/general/is_enabled';

    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getStoreConfigFlag(self::XML_PATH_ENABLED)) {
            $this->norouteAction();
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return;
        }
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle('Artists');
        $this->renderLayout();
    }

    public function viewAction()
    {
		/* Akash Kumar Updated 24 April 2015 */
		$currentUrl = Mage::helper('core/url')->getCurrentUrl(); 
		$url_arr= explode('/',$currentUrl); 
		$x= end($url_arr);
		if($x == ''){
			end($url_arr); 
			$artist= prev($url_arr); 
		}else{
			$artist= end($url_arr); 
		}
		/* $attribute_details = Mage::getSingleton("eav/config")->getAttribute("catalog_product", 'manufacturer'); 
		$options = $attribute_details->getSource()->getAllOptions(false); 
		foreach($options as $option){ 
			if(strtolower($option["label"])==trim(strtolower($artist))){
				$artistTitle= $option["label"];
				break;
			}
		} */
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($artist);
        $this->renderLayout();
    }
	
	public function inAction()
	{	
		$base= Mage::getBaseUrl( Mage_Core_Model_Store::URL_TYPE_WEB, true );
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
		$collection= Mage::getModel('catalog/product')->getCollection();
		$from= $_GET['from'];
		$to = $_GET['to'];
		
		try{
			for($i=$from ;$i<=$to; $i++){ 
				$product = Mage::getModel('catalog/product')->load($i);				
				/*$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);*/
				if(!empty($product) && $product->getName()!=''){
					$mediaGalleryAttribute = Mage::getModel('catalog/resource_eav_attribute')->loadByCode($product->getEntityTypeId(), 'media_gallery');
					$gallery = $product->getMediaGalleryImages()->getItems(); 
					if(empty($gallery)){ 
						$product->setData('status',2); 
					}else{ 
						foreach ($product->getMediaGalleryImages() as $image){
							$path= $image->getUrl(); 
							$data= getimagesize($path); 
							//$data= base64_encode(file_get_contents($path));
							if(!$data){ 
								$mediaGalleryAttribute->getBackend()->removeImage($product, $image->getFile());
								$product->save();
							}
						} 
					}
					if(empty($gallery)){
						$product->setData('status',2);
						echo 'Due to unavailable product Image, we have disabled Product <b>"'.$product->getName().'"</b> and id = <b>'.$product->getId().'</b><br>';
					}else{
						$size= count($gallery); 
						if($size==1){ 
							foreach ($product->getMediaGalleryImages() as $image){
								Mage::getSingleton('catalog/product_action')
								->updateAttributes(array($i), array('image'=>$image->getFile(),'small_image'=>$image->getFile(),'thumbnail'=>$image->getFile()), 0);
							}
						}
						if($size==2){ 
							$p= 0;
							foreach ($product->getMediaGalleryImages() as $image){
								$p++;
								if($p==1){
									Mage::getSingleton('catalog/product_action')
									->updateAttributes(array($i), array('image'=>$image->getFile(),'small_image'=>$image->getFile()), 0);
								}
								if($p==2){
									Mage::getSingleton('catalog/product_action')
									->updateAttributes(array($i), array('thumbnail'=>$image->getFile()), 0);
								}
							}
						}
						if($size==3 || $size>3){ 
							$q= 0;
							foreach ($product->getMediaGalleryImages() as $image){
								$q++;
								if($q==1){
									Mage::getSingleton('catalog/product_action')
									->updateAttributes(array($i), array('image'=>$image->getFile()), 0);
								}
								if($q==2){
									Mage::getSingleton('catalog/product_action')
									->updateAttributes(array($i), array('small_image'=>$image->getFile()), 0);
								}
								if($q==3){
									Mage::getSingleton('catalog/product_action')
									->updateAttributes(array($i), array('thumbnail'=>$image->getFile()), 0);
								}
							}
						}
					} 
					$product->save();
				}else{
					continue;
				}
			}
			Mage::getModel('catalog/product_image')->clearCache();
			/* $indexingObject = Mage::getSingleton('index/indexer')->getProcessesCollection(); 
			foreach ($indexingObject as $process){
				$process->reindexEverything();
			} */
			//print_r($product->getData()); 
			$min= $to+1;
			$max= $to+20;
			echo '<br><br>succesfully removed broken images from Product Id <b>'.$from.'</b> to <b>'.$to.'</b> !! <br><br>';
			echo 'for next --> <a id="click" href="javascript:void(0);">Click here!</a>';
			echo '<script type="text/javascript">
				var myEl = document.getElementById("click");

				myEl.addEventListener("click", function() {
					window.location="'.$base.'artist/index/in?from='.$min.'&to='.$max.'";
				}, false);
				</script>';		
		}catch(Exception $e){
			echo $e->getMessage();
		}
		die;
	}
	public function updateAction(){
		$coll = Mage::getModel('zeon_artist/artist')->getCollection();
		$attributeCode = Mage::helper('zeon_artist')->getArtistAttributeCode(); 
        $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $attributeCode);

        foreach ($attribute->getSource()->getAllOptions(true, true) as $option) {
             $attributeArray[$option['value']] = $option['label'];
        }
		foreach($coll as $key=>$val){
			foreach($attributeArray as $key2=>$val2){
				if(strtolower($val->getArtist())==strtolower($val2)){
					$br= Mage::getModel('zeon_artist/artist')->load($val->getArtistId());
					$br->setData('artist_title',$val2);
					$br->save();
					break;
				}
			}
		}
		die('her');
	}
}