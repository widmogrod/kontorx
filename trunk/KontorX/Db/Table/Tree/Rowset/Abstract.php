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
	
	public function __sleep() {
		return array_merge(
			parent::__sleep(),
			array(
				'_table', // FIX For serialization.. 
				'_level',
				'_separator')
		);
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
	public function hasChildren() {
		$result = false;

		$key = $this->_getLevelKey();

		if (isset($this->_childrens[$key]))
		{
			return (bool) count($this->_childrens[$key]);
		}

		$this->_childrens[$key] = array();

		foreach ($this->_data as $pointer => $data) 
		{
			if ($pointer === $this->_pointer) 
			{
				continue;
			}
			
//			$thisKey = $this->_getLevelKey($data);

			
			// Tutaj jest błąd co jeżeli rodzic jest 1 a dziecko 12
			// 12 posiada 1 na początku a nie jest jego rodzicem!
			// sprawdz czy poczatek $data[$this->_level] jest identyczny z kluczem
			// jeśli tak, to jest to dziecko
//			if ($key == substr($data[$this->_level], 0, strlen($key)))

			// sprawcz czy klucz rodzica jest identyczny jak dziecka
			if ($key == $data[$this->_level]) 
			{
				$this->_childrens[$key][] = $data;
				unset($this->_data[$pointer]);
				--$this->_count;
				$result = true;
			}
			
		}
		
		if ($result) 
		{
			// reset keys
			$this->_data = array_values($this->_data);
		}

		return $result;
	}

	/**
	 * @return KontorX_Db_Table_Tree_Rowset_Abstract
	 */
	public function getChildren() {
		$key = $this->_getLevelKey();
		if (!isset($this->_childrens[$key])) {
			if (!$this->hasChildren()) {
				return null;
			}
		}

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

	/**
	 * @return string
	 */
	protected function _getLevelKey(array $data = null) {
		if (null === $data)
		{
			$current = $this->current();
			return $current->__get($this->_level) == ''
				? $current->__get('id')
				: $current->__get($this->_level) . $this->_separator . $current->__get('id');
		} else {
			return $data[$this->_level] == ''
				? $data['id']
				: $data[$this->_level] . $this->_separator . $data['id'];
		}
	}
}