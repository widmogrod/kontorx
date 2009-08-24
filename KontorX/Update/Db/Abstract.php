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
	 * @param string $sql
	 * @param array $params
	 * @return bool
	 */
	protected function _execute($sql, $params = array()) {
		$adapter = $this->getAdapter();
		$sql = $this->_bindNoQuoted($sql, $params);

		try {
			$stmt = $adapter->prepare($sql);
			$result = $stmt->execute($params);
			$this->_setStatus($result ? self::SUCCESS : self::FAILURE);
			return $result;
		} catch (Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
		}
	}

	/**
	 * @var array
	 */
	protected $_bindNoQuoted = array();

	/**
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
	 * @var array
	 */
	protected $_sqlOptions = array();

	/**
	 * @var array
	 */
	protected $_sql = array();
	
	/**
	 * @param string $type
	 * @param array $options
	 * @return array 
	 */
	protected function _getOptions($type, array $options) {
		if (array_key_exists($type, $this->_sqlOptions)) {
			$optionsKey = array_flip($this->_sqlOptions[$type]);
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