<?php
require_once 'Zend/Db/Table/Rowset/Abstract.php';

/**
 * KontorX_Db_Table_Rowset_Tree_Abstract
 */
class KontorX_Db_Table_Tree_Rowset_Abstract extends Zend_Db_Table_Rowset_Abstract implements RecursiveIterator {
	/**
     * Nazwa kolumny poziomu zagnieżdżenia
     * @var string
     */
	protected $_level = null;

	/**
	 * Seprarator poziomu zagnieżdżenia
	 * @var string
	 */
	protected $_separator = '/';
	
	/**
	 * @param array $config
	 * @return void
	 */
	public function __construct(array $config) {
	 	if (isset($config['level'])) {
	 		$this->_level = (string) $config['level'];
	 	}
	 	if (isset($config['separator'])) {
	 		$this->_separator = (string) $config['separator'];
	 	}

	 	parent::__construct($config);
	 }
	
	public function init() {
		if (null === $this->_level) {
			require_once 'KontorX/Db/Table/Tree/Rowset/Exception.php';
			throw new KontorX_Db_Table_Tree_Rowset_Exception('Field `$_level` name for nested records is not definded');
		}
	}

	/**
	 * @var array
	 */
	private $_childrens = array();

	/**
	 * @return bool
	 */
	public function hasChildren () {
		$result = false;

		$current = $this->current();
		$key = $current->{$this->_level} == ''
			? $current->id
			: $current->{$this->_level} . $this->_separator . $current->id;

		if (isset($this->_childrens[$key])) {
			return (count($this->_childrens[$key]) > 0);
		} else {
			$this->_childrens[$key] = array();
		}

		foreach ($this->_data as $i => $data) {
			if (false !== strstr($data[$this->_level], $key)) {
				$this->_childrens[$key][] = $data;
				unset($this->_data[$i]);
				--$this->_count;
				$result = true;
			}
		}

		// reset keys
		$this->_data = array_values($this->_data);
		
		return $result;
	}

	/**
	 * @return KontorX_Db_Table_Tree_Rowset_Abstract
	 */
	public function getChildren () {
		$current = $this->current();
		$key = $current->{$this->_level} == ''
			? $current->id
			: $current->{$this->_level} . $this->_separator . $current->id;

		if (isset($this->_childrens[$key])) {
			$data  = array(
	            'table'    => $this->_table,
	            'data'     => $this->_childrens[$key],
	            'readOnly' => $this->_readOnly,
	            'rowClass' => $this->_rowClass,
	            'stored'   => true,
				// KontorX stuff
				'level'	   => $this->_level,
				'separator'=> $this->_separator,
	        );
	        return new self($data);
		}
		
		return null;
	}

	/**
	 * @return string
	 */
	protected function _getLevelPath() {
		$current = $this->current();
		return $current->{$this->_level} == ''
			? $current->id
			: $current->{$this->_level} . $this->_separator . $current->id;
	}
}