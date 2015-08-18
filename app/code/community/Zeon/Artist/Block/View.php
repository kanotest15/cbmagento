<?php
class Zeon_Artist_Block_View extends Mage_Catalog_Block_Product_Abstract
{
    protected $_artist;

    protected $_defaultToolbarBlock = 'catalog/product_list_toolbar';
    
    protected $_artistCollection;

    /**
     * Retrieve Artist instance
     *
     * @return Zeon_Artist_Model_Artist
     */
    public function getArtist()
    {
        $artistId = $this->getRequest()->getParam('artist_id', false);
        if (is_null($this->_artist)) {
            if ($artistId) {
                $this->_artist = Mage::getModel('zeon_artist/artist')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($artistId);
            } else {
                $this->_artist = Mage::getSingleton('zeon_artist/artist');
            }
        }
        return $this->_artist; 
    }
    
     /**
     * Retrieve Artist collection
     *
     * @return Zeon_Artist_Model_Resource_Artist_Collection
     */
    protected function _getArtistCollection() 
    {
        $artistCode = Mage::helper('zeon_artist')->getArtistAttributeCode();
        if (is_null($this->_artistCollection)) {
            $artistId = $this->getArtist()->getArtist();
        
            $this->_artistCollection = Mage::getResourceModel('catalog/product_collection');
            $this->_artistCollection->setVisibility(
                Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds()
            );
            
            $this->_artistCollection = $this->_addProductAttributesAndPrices($this->_artistCollection)
            ->addStoreFilter()
            ->addAttributeToFilter($artistCode, $artistId);
        
        }
        
        return $this->_artistCollection;
    }
    
    /**
     * Retrieve loaded Artist collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getArtistCollection()
    {
        return $this->_getArtistCollection();
    }

    /**
     * Prepare global layout
     *
     * @return Zeon_Artist_Block_View
     */
    protected function _prepareLayout()
    {
        $artist = $this->getArtist();
        $helper = Mage::helper('zeon_artist');
        // show breadcrumbs
        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbs->addCrumb(
                    'home', 
                    array(
                        'label'=>$helper->__('Home'), 
                        'title'=>$helper->__('Go to Home Page'), 
                        'link'=>Mage::getBaseUrl()
                    )
                );
                $breadcrumbs->addCrumb(
                    'artist_list', 
                    array(
                        'label'=>$helper->__('Artist'), 
                        'title'=>$helper->__('Artist'), 
                        'link'=>Mage::getUrl('artist')
                    )
                );
                $breadcrumbs->addCrumb(
                    'artist_view', 
                    array(
                        'label'=>Mage::getModel('zeon_artist/artist')
                            ->getArtistName($artist->getArtist(), Mage::app()->getStore()->getId()), 
                        'title'=>$artist->getIdentifier()
                    )
                );
        }

        $head = $this->getLayout()->getBlock('head');
        if ($head) {
            $head->setTitle($helper->getDefaultTitle());
            $head->setKeywords($helper->getDefaultMetaKeywords());
            $head->setDescription($helper->getDefaultMetaDescription());
        }

        return parent::_prepareLayout();
    }
    

    public function getMode()
    {
        return $this->getChild('toolbar')->getCurrentMode();
    }

    /**
     * Retrieve Products collection
     *
     *
     */
    protected function _beforeToHtml()
    {  
        $toolbar = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->_getArtistCollection();
    
        // use sortable parameters
        if ($orders = $this->getAvailableOrders()) {
            $toolbar->setAvailableOrders($orders);
        }
        if ($sort = $this->getSortBy()) {
            $toolbar->setDefaultOrder($sort);
        }
        if ($dir = $this->getDefaultDirection()) {
            $toolbar->setDefaultDirection($dir);
        }
        if ($modes = $this->getModes()) {
            $toolbar->setModes($modes);
        } 

        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);

        $this->setChild('toolbar', $toolbar);
        Mage::dispatchEvent(
            'catalog_block_product_list_collection', 
            array(
                'collection' => $this->_getArtistCollection()
            )
        );

        $this->setProductCollection($collection);

        return parent::_beforeToHtml();
    }

    public function getToolbarBlock()
    {   
        if ($blockName = $this->getToolbarBlockName()) {
            if ($block = $this->getLayout()->getBlock($blockName)) {
                return $block;
            }
        }
        $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, microtime());
        return $block;
    }

    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    public function setCollection($collection)
    {
        $this->_artistCollection = $collection;
        return $this;
    }

	public function getArtistReview($artistId){ 
	
		$products= Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('manufacturer',$artistId);
		foreach($products as $product){
			$productids[]= $product->getId();
		}
		$product_ids= array_unique($productids);
		
		foreach($product_ids as $productId){	
			$reviews = Mage::getModel('review/review')
						->getResourceCollection()
						->addStoreFilter(Mage::app()->getStore()->getId()) 
						->addEntityFilter('product', $productId)
						->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
						->setDateOrder()
						->addRateVotes(); 
			$collection= $reviews->getData(); 
			if(!empty($collection)){
				foreach($reviews as $review){
					//$rating= Mage::getModel('rating/rating')->getCollection()->addFieldToFilter('rating_code','Quality');	
					$rating =  Mage::getModel('rating/rating_option_vote')
								->getResourceCollection()
								->setReviewFilter($review->getReviewId())
								->addRatingInfo(Mage::app()->getStore()->getId())
								->setStoreFilter(Mage::app()->getStore()->getId())
								->getFirstItem();
				
					$review_collection[]= array(
												'product_id' => $productId,
												'detail'=> $review->getDetail(),
												'percent'=> $rating->getPercent(),
												'date'=> $review->getCreatedAt()
										);
				}
			}else{
				continue;
			}
		}
		
		return $review_collection;
	}
}