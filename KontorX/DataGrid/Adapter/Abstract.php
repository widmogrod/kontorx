<?php
require_once 'KontorX/DataGrid/Adapter/Interface.php';
abstract class KontorX_DataGrid_Adapter_Abstract implements KontorX_DataGrid_Adapter_Interface {
	/**
	 * @var array
	 */
	protected $_fetched;

	/**
     * @var mixed
     */
	protected $_adaptable;

    /**
     * Set adaptable object
     * @param mixed $adaptable
     */
    public function setAdaptable($adaptable) {
        $this->_adaptable = $adaptable;
    }

    /**
     * Return a raw data
     * @return mixed
     */
    public function getAdaptable() {
        return $this->_adaptable;
    }
    
    /**
     * @var KontorX_DataGrid
     */
    protected $_dataGrid;
    
    /**
     * @param KontorX_DataGrid $dataGrid
     */
    public function setDataGrid(KontorX_DataGrid $dataGrid) {
    	$this->_dataGrid = $dataGrid;
    }

    protected $_cellsetClass = 'KontorX_DataGrid_Adapter_Cellset_Standard';
    
    /**
     * @param string $cellsetClass
     * @return void
     */
    public function setCellsetClass($cellsetClass) {
    	$this->_cellsetClass = (string) $cellsetClass;
    }

    /**
     * @return string
     */
    public function getCellsetClass() {
    	return $this->_cellsetClass;
    }

    /**
     * @var array of @see KontorX_DataGrid_Cell_Interface
     */
    private $_cells = null;

    /**
     * Get array set of @see KontorX_DataGrid_Cell_Interface
     * @return array
     */
    public function getCells() {
        if (null === $this->_cells) {
            $result = array();
            foreach ($this->_dataGrid->getColumns() as $columnName => $columnInstance) {
                $row = $columnInstance->getCell();
                if (null != $row) {
                    $result[$columnName] = $row;
                }
            }
            $this->_cells = $result;
        }

        return $this->_cells;
    }

    const CACHE_PREFIX = 'KontorX_DataGrid_Adapter_';

    /**
     * @var Zend_Cache_Core
     */
    protected static $_cache = null;

    /**
     * @param Zend_Cache_Core $cache
     */
    public static function setCache(Zend_Cache_Core $cache) {
        self::$_cache = $cache;
    }

    /**
     * @return string
     */
    protected function _getCacheId() {
        return self::CACHE_PREFIX . spl_object_hash($this);
    }

    /**
     * @var bool
     */
    private $_cacheEnabled = null;

    /**
     * @param bool $flag
     */
    public function setCacheEnable($flag = true) {
        $this->_cacheEnabled = $flag;
    }

    /**
     * @return bool
     */
    protected function _cacheEnabled() {
        return (self::$_cache === null || $this->_cacheEnabled === false) ? false : true;
    }
    
	/**
	 * @var array of @see KontorX_DataGrid_Cell_Interface
	 */
	protected $_rows = array();

	/**
	 * @var integer
	 */
	protected $_pointer = 0;
	
	/**
	 * @var integer
	 */
	protected $_count = 0;
    
    
	/**
	 * @return KontorX_DataGrid_Adapter_Cellset_Interface
	 */
	public function current() {
		return $this->_rows[$this->_pointer];
	}

	/**
	 * @return void
	 */
	public function next() {
		++$this->_pointer;
	}

	/**
	 * Layzy load, fetch data magick ;]
	 * @return void
	 */
	public function rewind() {
		if (true !== $this->_fetched) {
			$this->fetchData();
		}
		$this->_pointer = 0;
	}

	/**
	 * @return integer
	 */
	public function key() {
		return $this->_pointer;
	}
	
	/**
	 * @return bool
	 */
	public function valid() {
		return $this->_pointer < $this->_count;
	}

	/**
	 * @return integer
	 */
	public function count() {
		return $this->_count;
	}
}