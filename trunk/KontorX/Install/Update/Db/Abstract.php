<?php
require_once 'KontorX/Install/Update/Interface.php';
abstract class KontorX_Install_Update_Db_Abstract
	implements KontorX_Install_Update_Interface {

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

		$stmt = $adapter->prepare($sql);
		return $stmt->execute($params);
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