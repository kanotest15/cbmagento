<?php
class Zeon_Artist_Block_Adminhtml_Artist_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set defaults
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('artistGrid');
        $this->setDefaultSort('artist_id'); 
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(false);
         $this->setUseAjax(true); 
    }

    /**
     * Instantiate and prepare collection
     *
     * @return Zeon_Artist_Block_Adminhtml_Artist_Grid
     */
    protected function _prepareCollection()
    {
			$collection = Mage::getModel('zeon_artist/artist')->getCollection()->addFieldToSelect('*');

			/* $collection = Mage::getResourceModel('zeon_artist/artist_collection');
            $tableNameEAOV = Mage::getModel('core/resource')->getTableName('eav_attribute_option_value');
            $tableNameEAO = Mage::getModel('core/resource')->getTableName('eav_attribute_option');

            $collection->getSelect()->distinct()->join(
               array('eaov' => $tableNameEAOV), 
               'main_table.artist = eaov.option_id', 
               array('artist_name'=>'value')
           )
           ->join(array('eao' => $tableNameEAO), 'eao.option_id = eaov.option_id', array()); */

           if (!Mage::app()->isSingleStoreMode()) {
				$collection->addStoresVisibility();
           }

           $this->setCollection($collection);
           return parent::_prepareCollection();
    }


    /**
     * Define grid columns
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'artist_id', 
            array(
                'header'=> Mage::helper('zeon_artist')->__('ID'),
                'type'  => 'number',
                'width' => '1',
                'index' => 'artist_id',				
            )
        );

        /* $this->addColumn(
            'artist', 
            array(
                'header' => Mage::helper('zeon_artist')->__('Artist Title'),
                'index'  => 'artist',
				'width' => '200px',
				'type' => 'options',
				'options'  => Mage::getModel('zeon_artist/options')->getArtist(),
				'filter_condition_callback' => array($this, '_filterArtistCondition'),
            )
        ); */
		
         $this->addColumn(
            'artist_title', 
            array(
                'header' => Mage::helper('zeon_artist')->__('Artist Title'),
                'type'   => 'text',
                'index'  => 'artist_title',
				/*'filter_condition_callback' => array($this, '_addArtistToCollection'),*/
				'sortable'  => true,
				/* 'renderer'  => 'Zeon_Artist_Block_Adminhtml_Artist_Edit_Renderer_Red', */
            )
        );
        $this->addColumn(
            'update_time', 
            array(
                'header'=> Mage::helper('zeon_artist')->__('Update Time'),
                'type' => 'datetime',
                'index'=> 'update_time',
				'width' => '200px',
            )
        );

        $this->addColumn(
            'status', 
            array(
                'header'  => Mage::helper('zeon_artist')->__('Status'),
                'align'   => 'center',
                'width' => '100px',
                'index'   => 'status',
                'type'    => 'options',
                'options' => Mage::getModel('zeon_artist/status')->getAllOptions(),
            )
        );
        
        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn(
                'visible_in', 
                array(
                    'header'     => Mage::helper('zeon_artist')->__('Visible In'),
                    'type'       => 'store',
                    'index'      => 'stores',
                    'sortable'   => false,
                    'store_view' => true,
                    'width'      => 200
                )
            );
        } 

        $this->addColumn(
            'action', 
            array(
                'header'  => Mage::helper('zeon_artist')->__('Action'),
                'width' => '100px',
                'type'    => 'action',
                'align'   => 'center',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('zeon_artist')->__('Edit'),
                        'url'     => array('base'=> '*/*/edit'),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
            )
        );
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('artist_id');
        $this->getMassactionBlock()->setFormFieldName('artist');
        $this->getMassactionBlock()->addItem(
            'delete', 
            array(
                'label'    => Mage::helper('zeon_artist')->__('Delete'),
                'url'      => $this->getUrl('*/*/massDelete'),
                'confirm'  => Mage::helper('zeon_artist')->__(
                    'Are you sure you want to delete these artist?'
                ),
            )
        );
        return $this;
    }
	
	/* protected function _filterArtistCondition($collection, $column)
	{
		if (!$value = $column->getFilter()->getValue()) {
			return;
		}
		
		$this->getCollection()->addFieldToFilter('artist', array('finset' => $value));
	} */
	
	
	
    /**
     * Grid row URL getter
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * Define row click callback
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    /**
     * Add store filter
     *
     * @param Mage_Adminhtml_Block_Widget_Grid_Column  $column
     * @return Zeon_Artist_Block_Adminhtml_Artist_Grid
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getIndex() == 'stores') {
            $this->getCollection()->addStoreFilter($column->getFilter()->getCondition(), false);
        } elseif ($column->getIndex() == 'artist_name') {
            $this->getCollection()->addArtistNameFilter($column->getFilter()->getCondition());
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
	
	public function _addArtistToCollection($collection, $column)
    {	
		if (!$value = $column->getFilter()->getValue()) {
			return;
		}
        $result = $this->getCollection()->addArtistNameFilter($column->getFilter()->getValue());
		$this->getCollection()->addFieldToFilter('artist_title', $result)->setOrder('artist','ASC');
    }
}