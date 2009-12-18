<?php
require_once 'KontorX/DataGrid/Adapter/Interface.php';
/**
 * @author gabriel
 */
abstract class KontorX_DataGrid_Adapter_Abstract implements KontorX_DataGrid_Adapter_Interface {

	public function fetchData() {
        if ($this->_cacheEnabled()) {
            if (false === ($this->_data = self::$_cache->load($this->_getCacheId()))) {
            	self::$_cache->save($this->_fetchData(), $this->_getCacheId());
            }
        } else {
        	$this->_data = $this->_fetchData();
        }
        $this->_count = count($this->_data);
		return $this->_data;
	}

	/**
	 * Enter description here...
	 * @return array
	 */
	abstract protected function _fetchData();

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
	protected $_rows = array();

	/**
	 * @var array
	 */
	protected $_data = null;
	
	/**
	 * @var integer
	 */
	protected $_pointer = 0;
	
	/**
	 * @var integer
	 */
	protected $_count = 0;
	
	/**
	 * @var KontorX_DataGrid_Cell_Interface
	 */
	protected $_groupCell;
	
	/**
	 * @return KontorX_DataGrid_Adapter_Cellset_Interface
	 */
	public function current() {
		if (!isset($this->_rows[$this->_pointer])) {
			$columns = $this->_dataGrid->getColumns();
			/* @var $cellset KontorX_DataGrid_Adapter_Cellset_Instance */
			$cellset = new $this->_cellsetClass;

			/* @var $column KontorX_DataGrid_Column_Interface */
            foreach ($columns as $column) {
                /* @var $cell KontorX_DataGrid_Cell_Interface */ 
                $cell = clone $column->getCell();
				$cell->setData($this->_data[$this->_pointer]);

				if ($column->isGroup()) {
					/**
					 * Grupowanie odbywa się za pomoca porównywania nazwiy komurki
					 * Różne nazwy - przekazuje nową nazwę do porównywania
		             * 				 i dodaje do _Cellset kolumne. po której
		             * 				 odbywa się grupowanie.
					 */
	            	if ((string) $this->_groupCell != (string) $cell) {
	            		$this->_groupCell = $cell;
	            		$cellset->setGroupCell($cell);
	            	}
		        } else {
		        	$cellset->addCell($cell);
		        }
            }

            $this->_rows[$this->_pointer] = $cellset;
		}
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
		if (null === $this->_data) {
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
    
    /**
     * ADDITIONAL HELPER METHODS 
     */

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
     * Zwraca cache id.
     * @return string
     */
    private function _getCacheId() {
        $result = array(get_class($this), serialize($this));
        return sha1(implode($result));
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

	public function toArray() {
		$result = array();
		foreach ($this as $i => $cellset) {
			/* @var $cellset KontorX_DataGrid_Adapter_Cellset_Interface */
			$result[$i] = $cellset->toArray();
		}
		return $result;
	}
}