<?php
abstract class Promotor_View_Helper_Site_Abstract {
	/**
	 * @var Promotor_View_Helper_Site
	 */
	protected $_site;

	/**
	 * @param Promotor_View_Helper_Site $site
	 * @param array $options 
	 */
	public function __construct(Promotor_View_Helper_Site $site, $options = null) {
		$this->_site = $site;

		if (is_array($options)) {
			$this->setOptions($options);
		}
		
		// initialize helper
		$this->init();
	}

	/**
	 * @return Promotor_View_Helper_Site_Abstract
	 */
	public function init() {
		return $this;
	}
	
	/**
	 * @param array $options 
	 * @return Promotor_View_Helper_Site_Abstract
	 */
	public function setOptions(array $options) {
		foreach ($options as $key => $value) {
			$method = 'set' . ucfirst($key);
			if (method_exists($this, $method)) {
				$this->$method($value);
			}
		}
		return $this;
	}

	/**
	 * @return string  
	 */
	abstract public function render();

	/**
	 * @return string
	 */
	public function __toString() {
		try {
			return (string) $this->render();
		} catch (Exception $e) {
			trigger_error($e->getMessage(), E_USER_WARNING);
			return '';
		}
	}
	
	/**
	 * @var integer
	 */
	protected $_id;
	
	/**
	 * @param integer $id
	 * @return Promotor_View_Helper_Site_Abstract
	 */
	public function setId($id) {
		$this->_id = (int) $id;
		return $this;
	}

	/**
	 * @var string
	 */
	protected $_alias;
	
	/**
	 * @param string $alias
	 * @return Promotor_View_Helper_Site_Abstract
	 */
	public function setAlias($alias) {
		$this->_alias = $alias;
		return $this;
	}
	
	/**
	 * @var KontorX_Db_Table_Tree_Row_Abstract
	 */
	protected $_row;
	
	/**
	 * @param KontorX_Db_Table_Tree_Row_Abstract $row
	 * @return Promotor_View_Helper_Site_Abstract
	 */
	public function setRow($row) {
		$this->_row = $row;
		return $this;
	}

	/**
	 * @return string|integer|KontorX_Db_Table_Tree_Row_Abstract
	 * @throws Exception if id,alias or row is not set
	 */
	public function getIdentification() {
		if (null !== $this->_id) {
			return $this->_id;
		} elseif (null !== $this->_alias) {
			return $this->_alias;
		} elseif (null !== $this->_row) {
			return $this->_row;
		}

		throw new Exception('identification is not set');
	}
}