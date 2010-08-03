<?php
require_once 'KontorX/Db/Table/Row.php';
require_once 'KontorX/Db/Table/Tree/Row/Interface.php';

/**
 * KontorX_Db_Table_Tree_Row_Abstract
 *
 * @category    KontorX
 */
abstract class KontorX_Db_Table_Tree_Row_Abstract extends KontorX_Db_Table_Row implements KontorX_Db_Table_Tree_Row_Interface {
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
	 * Przechowuje obiekt @see Zend_Db_Table_Row_Abstract rodzica
	 *
	 * @var Zend_Db_Table_Row_Abstract
	 */
	protected $_parentRow = null;

	/**
     * Store table, primary key and data in serialized object
     *
     * @return array
     */
    public function __sleep() {
		return array_merge(
			parent::__sleep(),
			array(
				'_table', // FIX For serialization..
				'_level',
				'_separator',
			)
		);
	}

    /**
     * Setup to do on wakeup.
     * A de-serialized Row should not be assumed to have access to a live
     * database connection, so set _connected = false.
     *
     * @return void
     */
    public function __wakeup()
    {
        parent::__wakeup();
    }
       
        /**
         * @Overwrite
         */
        public function init() {
			if (null === $this->_level) {
			        require_once 'KontorX/Db/Table/Tree/Row/Exception.php';
			        throw new KontorX_Db_Table_Tree_Row_Exception('Field `$_level` name for nested records is not definded');
			}
			if (!array_key_exists($this->_level, $this->_data)) {
            require_once 'KontorX/Db/Table/Tree/Row/Exception.php';
            throw new KontorX_Db_Table_Tree_Row_Exception("Specified column \"$this->_level\" is not in the row");
        }
        }

        /**
         * @Overwrite - jedyna modyfikacja to kolumna wirtualna kolumna depth
         */
        public function __get($columnName) {
			// specjalny atrybut zwracajacy glebokosc drzewa
			if ($columnName == 'depth') {
			        return $this->getDepth();
			}
			return parent::__get($columnName);
        }

        /**
         * Return depth of row nest..
         *
         * @return integer
         */
        public function getDepth() {
			$level = @$this->_data[$this->_level];
			return $level == '' ? 0 : substr_count($level, $this->_separator) + 1;
        }
       
        /**
         * Ustawia obiekt @see Zend_Db_Table_Row_Abstract rodzica
         *
         * @param Zend_Db_Table_Row_Abstract $row
         */
        public function setParentRow(Zend_Db_Table_Row_Abstract $row) {
			$this->_parentRow = $row;
        }

        /**
         * @var bool
         */
        protected $_isRoot = false;
       
        /**
         * Ustawiamy flage, żeby _update zrobił root ..
         * ale nie może być ustawiony parentRow()
         * @param bool $flag
         */
        public function setRoot($flag = true) {
			$this->_isRoot = (bool) $flag;
        }

        /**
         * Czy rekord updatowany będzie root?
         *
         * Czy będzie root? zalerzy jeszcze czy jest parentRow!
         *
         * @return bool
         */
        public function isRoot () {
			return $this->_isRoot;
        }
       
        /**
         * Zwraca obiekt @see Zend_Db_Table_Row_Abstract rodzica
         *
         * @return Zend_Db_Table_Row_Abstract
         */
        public function getParentRow() {
			return $this->_parentRow;
        }

        /**
         * Znajdz potomków
         *
         * @param Zend_Db_Table_Select  $select
         * @param bool						  $thisRowIncluded
         * @return KontorX_Db_Table_Tree_Rowset
         */
        public function findDescendant(Zend_Db_Table_Select $select = null, $thisRowIncluded = false) 
        {
			// rekord musi byc zapisany w bazie danych
			if (empty($this->_cleanData)) {
			        $message = 'Current row is not stored in database';
			        require_once 'KontorX/Db/Table/Tree/Row/Exception.php';
			        throw new KontorX_Db_Table_Tree_Row_Exception($message);
			}

			$primary = current($this->_primary);

			// aktualny poziom zagnieżdżenia
			$level = $this->__get($this->_level);
			$descendans = explode($this->_separator, $level);

			// TODO Czy zostawić coś takiego?
			// czy nie bedzie kolizi z *_Rowset?
			if (empty($descendans)) {
			        return array();
			}

			$select = (null === $select)
			        ? $this->select()
			        : $select;

			// czy wyłowić z aktualnym rekordem ?
			if (true === $thisRowIncluded) {
			        array_push($descendans,$this->$primary);
			}

			$rowset = $this->_table->find($descendans);
			return $rowset;
        }
       
        /**
         * Znajdz rodziców rodzica ;]
         *
         * @param integer			               $depthLevel
         * @param Zend_Db_Table_Select  $select
         * @return KontorX_Db_Table_Tree_Rowset
         */
        public function findParents($depthLevel = null, Zend_Db_Table_Select $select = null) {
			// rekord musi byc zapisany w bazie danych
			if (empty($this->_cleanData)) {
			        $message = 'Current row is not stored in database';
			        require_once 'KontorX/Db/Table/Tree/Row/Exception.php';
			        throw new KontorX_Db_Table_Tree_Row_Exception($message);
			}

			// aktualny poziom zagnieżdżenia
			$level = $this->__get($this->_level);

			// root nie ma rodziców
			if ($level == '') {
			        return array();
			}


			$select = (null === $select)
			        ? $this->select()
			        : $select;

			if (!is_integer($depthLevel)) {
			        $depthLevel = $this->getDepth();
			}

			$levelArray       = explode($this->_separator, $level);
			$levelParents = $this->_regexpDepthLevelParents($levelArray, $depthLevel);
			$select->where("$this->_level REGEXP '^$levelParents$'");
               
			return $this->_table->fetchAll($select);
        }

        /**
         * Znajdz dzieci dla rodzica
         *
         * @param integer			               $depthLevel głębokość do jakiej mają być wyszukiwane
         * @param Zend_Db_Table_Select  $select
         * @return KontorX_Db_Table_Tree_Rowset
         */
        public function findChildrens($depthLevel = null, Zend_Db_Table_Select $select = null) 
        {
			// rekord musi byc zapisany w bazie danych
			if (empty($this->_cleanData)) {
				$message = 'Current row is not stored in database';
				require_once 'KontorX/Db/Table/Tree/Row/Exception.php';
				throw new KontorX_Db_Table_Tree_Row_Exception($message);
			}

			// aktualny poziom zagnieżdżenia
			$level = $this->__get($this->_level);

			// zabespieczenie gdy dzialamy na root
			// TODO Jak appendujemy roota to w przyszlosci
			// dodac mozliwosc dodania kolumny sortujacej!
			$levelChildrens = ($level == '')
		        ? $this->__get(current($this->_primary))
		        : $level . $this->_separator . $this->__get(current($this->_primary));

			$select = (null === $select) ? $this->select() : $select;

			// zapytanie wyszukujące dzieci dla rodzica
			if (is_integer($depthLevel)) {
				$levelRegexp = $this->_regexpDepthLevelChildrens($depthLevel, $depthLevel);
				$select->where("$this->_level REGEXP '^$levelRegexp$'");
			} else {
				$select->where("$this->_level LIKE ?", "$levelChildrens%");
			}
               
			return $this->_table->fetchAll($select);
        }

		/**
		 * Znajdz rodzeństwo tego rekordu
		 * 
		 * @param Zend_Db_Table_Select $select
		 * @param bool $includedSelf
		 * @throws KontorX_Db_Table_Tree_Row_Exception
		 */
		public function findSiblings(Zend_Db_Table_Select $select = null, $includedSelf = false) 
		{
			// rekord musi byc zapisany w bazie danych
			if (empty($this->_cleanData)) {
				$message = 'Current row is not stored in database';
				require_once 'KontorX/Db/Table/Tree/Row/Exception.php';
				throw new KontorX_Db_Table_Tree_Row_Exception($message);
			}
	
			$db = $this->_getTable()->getAdapter();
			
			// aktualny poziom zagnieżdżenia
			$level = $this->__get($this->_level);
	
			$select = (null === $select) ? $this->select() : $select;
			$select->where($db->quoteInto("$this->_level = ?", $levelRegexp));
	
			if (!$includedSelf) 
			{
				$where = array();
	
				$info = $this->_getTable()->info();
	        	$metadata = $info[Zend_Db_Table_Abstract::METADATA];
				
				$primaryKey = $this->_getPrimaryKey();
				foreach ($primaryKey as $column => $value)
				{
					$tableName = $db->quoteIdentifier($info[Zend_Db_Table_Abstract::NAME], true);
		            $type = $metadata[$column]['DATA_TYPE'];
		            $columnName = $db->quoteIdentifier($column, true);
		            $where[] = $db->quoteInto("{$tableName}.{$columnName} <> ?", $value, $type);
				}
	
				$select->where(implode(' AND ', $where));
			}

			return $this->_table->fetchAll($select);
		}
        
        /**
         * Formatuje zapytanie regexp dla odnajdywania rodziców
         *
         * @param array $level
         * @param integer $depth
         * @return string
         */
        protected function _regexpDepthLevelParents(array $level, $depth) {
			// validator
			if ($depth < 1 || empty($level)) {
			        return null;
			}

			$store  = null;
			$result = array();
			// loop przygotowywuje level
			do {
			        $value = array_shift($level);
			        $count = count($level);

			        $store = (null === $store)
						? $value
						: $store . $this->_separator . $value;

			        $result[] = $store;
			} while ($count > 0);

			$return = array();
			// loop tylko okreslonej glebokosci
			do {
			        $value = array_pop($result);
			        $count = count($result);

			        $return[] = $value;
			} while (--$depth > 0 && $count > 0);

			return '(' . implode('|', $return) . ')';
        }
       
        /**
         * Pomocnik formatujacy zapytanie o głębokość zagnieżdżenia
         *
         * @param integer       $level
         * @param string        $regexLevelPrepend
         * @param bool          $root
         * @return string
         */
        protected function _regexpDepthLevelChildrens($depthLevel, $regexLevelPrepend = null, $root = false) {
			// podstawowe formuły regexp

			// $regexpMain = "([0-9]+$this->_separator)";
			$regexpMain = "[0-9]*$this->_separator*";
			$regexpRoot = "[0-9]*";

			// odleglosc zawsze jest bezwzgledna
			// wzgledna jest deklarowana poprzez przekazanie $levelPrepend
			$depth = 0;

			$result = null;
			do {
			        $result .= $regexLevelPrepend;
			        // nie zaczynamy regexp od separatora!
			        if (true === $root) {
						$result .= $regexpRoot;
						// gdy rekord root pomniejszamy level
						// dlatego ze root jest w level null
						// i appendowany klucz glowny jest bez separatora
						$repeat = $depth-1;
			        } else {
						$repeat = $depth;
			        }

			        $result .= @str_repeat($regexpMain, $repeat);

			        // cache loop
			        if ($depth+1 < $depthLevel) {
						$result .= '|';
			        }
			} while (++$depth < $depthLevel);
			return $result;
        }
       
        /**
         * @Overwrite
         */
        protected function _insert() {
			// czy jest parent row
			$parentRow = $this->getParentRow();
			if (null === $parentRow) {
			        return;
			}

			// generowanie nowej wartości dla zagnieżdzenia
			$level = $parentRow->__get($this->_level);
			$level = ($level == '')
			        ? $parentRow->{current($this->_primary)}
			        : $level . $this->_separator . $parentRow->__get(current($this->_primary));

			// ustawianie zagniezdzenia
			$this->__set($this->_level, $level);
               
			// zerowanie parent row .. zeby nie bylo baboli np. podczas update
			$this->_parentRow = null;
        }

        /**
         * @Overwrite
         */
        protected function _update() {
			// czy jest parent row
			$parentRow = $this->getParentRow();
			if (null === $parentRow) {
			        // czy zapisujemy jako root?
			        if (!$this->isRoot()) {
						return;
			        }
			        $level = '';
			} else {
			        // generowanie nowej wartości dla zagnieżdzenia
			        $level = $parentRow->__get($this->_level);
			        $level = ($level == '')
						? $parentRow->__get(current($this->_primary))
						: $level . $this->_separator . $parentRow->__get(current($this->_primary));
			}

			// stara wartosc zagniezdzenia
			$levelOld = $this->__get($this->_level);
			// ustawianie zagniezdzenia
//              $this->__get($this->_level) = $level;
//
//              // zabespieczenie gdy dzialamy na root
//              $level = $levelOld == ''
//			      ? $this->__get(current($this->_primary))
//			      : $levelOld . $this->_separator . $this->__get(current($this->_primary));
               
			// update dzieci rodzica gdy zostaje zmienione jego polozenie
			$table = $this->getTable();
			$db = $table->getAdapter();

			$db->beginTransaction();
			try {
			        // TODO przyd dużej ilość rekordów może zostać przepełniona pamięć!
			        // Zastąpić to zapytaniem SQL!
			        foreach ($this->findChildrens() as $children) {
						$childrenLevel = ($levelOld == '')
						        ? $level . $this->_separator . $children->__get($this->_level)
						        : str_replace($levelOld, $level, $children->__get($this->_level));
						// level nie moze zaczynac się od separatora!
						$childrenLevel = ($childrenLevel{0} == $this->_separator)
						        ? substr($childrenLevel, 1)
						        : $childrenLevel;


						$children->__set($this->_level, $childrenLevel);

						$children->save();
			        }
			        $this->__set($this->_level, $level);

			        $db->commit();
			} catch (Zend_Db_Exception $e) {
			        $db->rollBack();
			        throw $e;
			}			      

			// zerowanie parent row ..
			$this->_parentRow = null;
        }

        /**
         * @Overwrite
         */
        protected function _delete() {
			// update dzieci rodzica gdy zostaje zmienione jego polozenie
			$table = $this->getTable();
			$db = $table->getAdapter();

			// zabespieczenie gdy dzialamy na root
			$level = $this->__get($this->_level) == ''
			        ? $this->__get(current($this->_primary))
			        : $this->__get($this->_level) . $this->_separator . $this->__get(current($this->_primary));

			$where = $db->quoteInto("$this->_level LIKE ?", $level . '%');
			$db = $this->getTable()->delete($where);
        }
       
        public function toArray() {
			$data = $this->_data;
			$data['depth'] = $this->getDepth();
        return (array) $data;
    }
}