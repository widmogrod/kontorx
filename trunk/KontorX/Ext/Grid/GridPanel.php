<?php
require_once 'KontorX/Ext/Abstract.php';

/**
 * @author gabriel
 *
 */
class KontorX_Ext_Grid_GridPanel extends KontorX_Ext_Abstract {

	/**
	 * @param Zend_Config|array $options
	 * @return void
	 */
	public function __construct($options = array()) {
		if ($options instanceof Zend_Config) {
			$this->setOptions($options->toArray());
		} elseif (is_array($options)) {
			$this->setOptions($options);
		}
	}
	
	public function toJavaScript($renderToId = null) {
		$options = array(
			'columns' => $this->_columns,
			'loadMask' => $this->_loadMask,
		
			// wysokość teoretycznie nie musi zostać podana.. ale wtedy wyświetla się jeden wiersz
			'height' => $this->_height,
			'renderTo' => (null === $renderToId) 
							? $this->_renderToId : $renderToId,
			'store' => $this->_store,
 			'collapsible' => true,
			'frame' => true,
							
//			'view' => new KontorX_JavaScript_Expresion('new Ext.grid.GroupingView({forceFit:true})')							
		);

		if (null !== $this->_width)
			$options['width'] = $this->_width;
			
		if (null !== $this->_title)
			$options['title'] = $this->_title;
			
		if (null !== $this->_bbar)
			$options['bbar'] = $this->_bbar;
		
		$options = $this->_toJavaScript($options);

		return sprintf('new Ext.grid.GridPanel(%s);', $options);
	}
	
	/**
	 * @var KontorX_Ext_Data_Store_Interface|string
	 */
	protected $_store;

	/**
	 * @param KontorX_Ext_Data_Store_Interface|KontorX_JavaScript_Interface|string $store
	 * @return KontorX_Ext_Grid_GridPanel
	 */
	public function setStore($store) {
		if ($store instanceof KontorX_Ext_Data_Store_Interface
			|| $store instanceof KontorX_JavaScript_Interface 
			|| is_string($store)) {
			$this->_store = $store;
		} else {
			require_once 'KontorX/Exception.php';
			throw new KontorX_Exception('store is not string or instanceof "KontorX_Ext_Data_Store_Interface"');
		}
		return $this;
	}
	
	/**
	 * @var integer
	 */
	protected $_width;
	
	/**
	 * @param integer $width
	 * @return KontorX_Ext_Grid_GridPanel
	 */
	public function setWidth($width) {
		$this->_width = (int) $width;
		return $this;
	}

	/**
	 * @var integer
	 */
	protected $_height = 400;
	
	/**
	 * @param integer $width
	 * @return KontorX_Ext_Grid_GridPanel
	 */
	public function setHeight($height) {
		$this->_height = (int) $height;
		return $this;
	}
	
	/**
	 * @var bool
	 */
	protected $_loadMask = true;
	
	/**
	 * @param integer $loadMask
	 * @return KontorX_Ext_Grid_GridPanel
	 */
	public function setLoadMask($loadMask) {
		$this->_loadMask = (bool) $loadMask;
		return $this;
	}
	
	/**
	 * @var string
	 */
	protected $_renderToId = 'kx_ext_grid';
	
	/**
	 * @param string $renderToId
	 * @return KontorX_Ext_Grid_GridPanel
	 */
	public function setRenderId($renderToId) {
		$this->_renderToId = (string) $renderToId;
		return $this;
	}
	
	/**
	 * @var KontorX_JavaScript_Interface
	 */
	protected $_bbar;

	/**
	 * @param KontorX_JavaScript_Interface $bbar
	 * @return KontorX_Ext_Grid_GridPanel
	 */
	public function setBbar(KontorX_JavaScript_Interface $bbar) {
		$this->_bbar = $bbar;
		return $this;
	}
	
	/**
	 * @var string
	 */
	protected $_title;
	
	/**
	 * @param string $title
	 * @return KontorX_Ext_Grid_GridPanel
	 */
	public function setTitle($title) {
		$this->_title = (string) $title;
		return $this;
	}

	/**
	 * @var unknown_type
	 */
	protected $_columns = array();
	
	/**
	 * @param array $columns
	 * @return KontorX_Ext_Grid_GridPanel
	 */
	public function setColumns(array $columns) {
		$this->_columns = $columns;
		return $this;
	}
}