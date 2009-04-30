<?php
require_once 'Zend/Db/Table/Rowset/Abstract.php';

/**
 * KontorX_Db_Table_Rowset_Tree_Abstract
 *
 * @category	KontorX
 * @package		KontorX_Db
 * @subpackage	Table
 * @version		0.1.4
 */
class KontorX_Db_Table_Tree_Rowset_Abstract extends Zend_Db_Table_Rowset_Abstract implements RecursiveIterator {
	/**
     * Nazwa kolumny poziomu zagnieżdżenia
     *
     * @var string
     */
	protected $_level = null;

	/**
	 * Seprarator poziomu zagnieżdżenia
	 *
	 * @var string
	 */
	protected $_separator = '/';

	/**
	 * @Overwrite
	 */
	public function init() {
		if (null === $this->_level) {
			require_once 'KontorX/Db/Table/Tree/Rowset/Exception.php';
			throw new KontorX_Db_Table_Tree_Rowset_Exception('Field `$_level` name for nested records is not definded');
		}
        // sortowanie rekordow by przyjely forme stroktory drzewiastej       
		usort($this->_data, array($this, '_nestedSort'));
	}

	/**
     * @Overwrite
     * 
     * @return KontorX_Db_Table_Tree_Row_Abstract current element from the collection
     */
    public function current() {
    	return parent::current();
    }

	/**
	 * Funkcja sortujaca pola zagnieżdżenia
	 * 
	 * Kolejność rekordów przyjmuje stroktórę drzewiastą
	 * <code>
	 * 1
	 * 1.1
	 * 1.2
	 * 2
	 * </code>
	 *
	 * @param array $row_1
	 * @param array $row_2
	 * @return int
	 */
	protected final function _nestedSort(array $row_1, array $row_2){
		$primaryKey = current($this->_table->info(Zend_Db_Table::PRIMARY));

		// wartosc klucza glownego porownywanych kolumn
		// TODO Dodać możliwośc określenia innego pola
		// da nam to mozliwośc określenia sortowania
		$primary_1 = $row_1[$primaryKey];
		$primary_2 = $row_2[$primaryKey];

		$level_1 = $row_1[$this->_level];
		$level_2 = $row_2[$this->_level];

		$levels_1   = explode($this->_separator, $level_1);
		$levels_1   = array_filter($levels_1);
		$levels_1[] = $primary_1;

		$levels_2   = explode($this->_separator, $level_2);
		$levels_2   = array_filter($levels_2);
		$levels_2[] = $primary_2;
		
		foreach ($levels_1 as $key_1 => $val_1 ) {
  			$val_2 = @$levels_2[$key_1] == ''
  				? '' :  $levels_2[$key_1] ;

	    	if($val_1 != $val_2){
		  		return $val_1 < $val_2
		  			? -1 : 1 ;
	    	}
  		}
	}

	private $_childrens = array();
	
	public function hasChildren () {
		$result = false;

		$current = $this->current();
		$key = $current->{$this->_level} . $this->_separator . $current->id;
		
		if (isset($this->_childrens[$key])) {
			return (count($this->_childrens[$key]) > 0);
		} else {
			$this->_childrens[$key] = array();
		}
		
		foreach ($this->_data as $data) {
			if ($data[$this->_level] == $key) {
				$this->_childrens[$key][] = $data;
				$result = true;
			}
		}

		return $result;
	}

	public function getChildren () {
		$current = $this->current();
		$key = $current->{$this->_level} . $this->_separator . $current->id;

		if (isset($this->_childrens[$key])) {
			$data  = array(
	            'table'    => $this->_table,
	            'data'     => $this->_childrens[$key],
	            'readOnly' => $this->_readOnly,
	            'rowClass' => $this->_rowClass,
	            'stored'   => true
	        );
	        return new self($data);
		}
		
		return null;
	}
}