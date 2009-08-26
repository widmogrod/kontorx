<?php
require_once 'KontorX/Update/Abstract.php';
abstract class KontorX_Update_Db_Abstract extends KontorX_Update_Abstract {

	/**
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected static $_adapter;

	/**
	 * @return Zend_Db_Adapter_Abstract
	 */
	public function getAdapter() {
		if (null === self::$_adapter) {
			self::$_adapter = Zend_Db_Table_Abstract::getDefaultAdapter();
		}
		return self::$_adapter;
	}

	/**
	 * Wykonuje zapytanie do bazy danych bindując parametry
	 * 
	 * Przykład:
	 * <code>
	 * $options = array('name' => $name);
	 * $options = $this->_getOptions(self::REMOVE_COLUMN, $options);
	 * return $this->_execute($this->_sql[self::REMOVE_COLUMN], $options);
	 * </code>
	 * 
	 * @param string $sql
	 * @param array $params
	 * @return bool
	 */
	protected function _execute($sql, $params = array()) {
		$adapter = $this->getAdapter();
		// zamień wartości w zapytaniu, bez cytowania!
		$sql = $this->_bindNoQuoted($sql, $params);

		try {
			$stmt = $adapter->prepare($sql);
			// zamień wartości w zapytaniu z cytowaniem
			$result = $stmt->execute($params);
			$this->_setStatus($result ? self::SUCCESS : self::FAILURE);
			return $result;
		} catch (Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
		}
	}

	/**
	 * Zmienna mapuje parametry zapytania (@see _sqlOptions)
	 * na wartości, które będą zamieniane bez quotowania! 
	 * 
	 * Wartości bez quotowania zostały występują w zapytaniu z prefiksem ":@"
	 * Watości z quotowaniem są z prefiksem ":"
	 * 
	 * Przykład:
	 *
	 * <code>
	 * protected $_bindNoQuoted = array(
	 * 	'table' => ':@table',
	 * 	'type' => ':@type',
	 * 	'null' => ':@null',
	 * 	'after' => ':@after'
	 * );
	 * </code>
	 * 
	 * @var array
	 */
	protected $_bindNoQuoted = array();

	/**
	 * Zmienna mapuje parametry zapytania (@see _sqlOptions)
	 * na wartości, które będą zamieniane bez quotowania! 
	 * 
	 * @param string $sql
	 * @param array $params 
	 * @return string as SQL
	 */
	protected function _bindNoQuoted($sql, array &$params) {
		$bind = array();
		foreach ($this->_bindNoQuoted as $paramMaped => $paramMapedTo) {
			if (array_key_exists($paramMaped, $params)) {
				$bind[$paramMapedTo] = $params[$paramMaped];
				unset($params[$paramMaped]);
			}
		}

		return str_replace(
			array_keys($bind), $bind, $sql);
	}
	
	/**
	 * Przechowuje tablicę parametrów jakie mogą występować w SQL
	 * tzn. tylko te parametry będą przyjmowane pozostałe będą filtrowane
	 * przy użyciu metody {@see $this->_getOptions()}
	 * 
	 * Przykład:
	 * <code>
	 * protected $_sqlOptions = array(
	 * 	self::SQL_SELECT => array('table')
	 * );
	 * </code>
	 * 
	 * @var array
	 */
	protected $_sqlOptions = array();

	/**
	 * Przechowywuje zapytania SQL
	 * 
	 * Przykład:
	 * <code>
	 * protected $_sql = array(
	 * 	self::SQL_SELECT => 'SELECT FROM `:@table`
	 * );
	 * </code>
	 * 
	 * @var array
	 */
	protected $_sql = array();
	
	/**
	 * Przygotowywuje opcje do bindowania w zapytaniu
	 * 
	 * Przykład:
	 * <code>
	 * protected $_sqlOptions = array(
	 * 	self::SQL_SELECT => array('table')
	 * );
	 * 
	 * $this->_getOptions(self::SQL_SELECT, array(
	 * 	'table' => 'my_db_table_name',
	 * 	'key_not_exsists' => 'value_for_666'
	 * )); // return array('table' => 'my_db_table_name')
	 * 
	 * </code>
	 * 
	 * @param string $type
	 * @param array $options
	 * @return array 
	 */
	protected function _getOptions($type, array $options) {
		if (array_key_exists($type, $this->_sqlOptions)) {
			$optionsKey = array_combine(
				$this->_sqlOptions[$type],
				array_fill(0, count($this->_sqlOptions[$type]), null)
			);
			$options = array_intersect_key(
				array_merge(
					$optionsKey,
					get_object_vars($this),
					$options
				),
				$optionsKey
			);
		}

		foreach ($options as $key => $val) {
			if (null !== $val) {
				unset($options[$key]);
				$key = ltrim($key,':');
				$options[$key] = $val;
			}
		}

		return $options;
	}
}