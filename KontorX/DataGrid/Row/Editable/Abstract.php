<?php
require_once 'KontorX/DataGrid/Row/ViewHelper.php';
abstract class KontorX_DataGrid_Row_Editable_Abstract extends KontorX_DataGrid_Row_ViewHelper {

	const SEPARATOR = ':|:';
	
	const HTML_CLASS = 'kx_editable';
	
	/**
	 * @var string
	 */
	protected $_helper = null;
	
	/**
	 * @return string
	 * @throws KontorX_DataGrid_Exception
	 */
	public function getHelperName() {
		if (null === $this->_helper) {
			require_once 'KontorX/DataGrid/Exception.php';
			throw new KontorX_DataGrid_Exception('Helper name is not set');
		}

		return (string) $this->_helper;
	}
	
	/**
	 * @var string
	 */
	protected $_prefix = 'editable';

	/**
	 * @param string $prefix
	 * @return void
	 */
	public function setPrefix($prefix) {
		$this->_prefix = (string) $prefix;
	}

	/**
	 * @return string
	 */
	public function getPrefix() {
		return $this->_prefix;
	}
	
	/**
	 * @var array
	 */
	protected $_primaryKey = null;
	
	/**
	 * @param array|string $primaryKey
	 * @return void
	 */
	public function setPrimaryKey($primaryKey) {
		$this->_primaryKey = (array) $primaryKey;
	}
	
	/**
	 * @return array
	 * @throws KontorX_DataGrid_Exception
	 */
	public function getPrimaryKey() {
		if (null === $this->_primaryKey) {
			require_once 'KontorX/DataGrid/Exception.php';
			throw new KontorX_DataGrid_Exception('Primary key is not set');
		}

		return $this->_primaryKey;
	}
	
	/**
	 * @return string
	 */
	protected function _getClassAttr() {
		return self::HTML_CLASS . '-' . $this->getClassName();
	}
	
	/**
	 * @return string
	 */
	protected function _prepareClassAttr($class = null) {
		return (null === $class)
			? $this->_getClassAttr()
			: $this->_getClassAttr() . ' ' . $class;
	}

	public function render() {
		// Tworzenie prefisku, klucza głównego
		$primaryVal = $this->getPrimaryKey();
		$primaryKey = implode(self::SEPARATOR, array_intersect_key($this->getData(), array_flip($primaryVal)));

		$columnName = $this->getColumnName();
		$prefix = $this->getPrefix();

		$name = sprintf('%s[%s][%s]', $prefix, $primaryKey, $columnName);
		$value = $this->getData($columnName);

		// ustawienie attrybutu 'class' dla elementu widoku.
		$class = $this->getAttrib('class');
		$class = $this->_prepareClassAttr($class);
		$this->setAttrib('class', $class);

		$helper = $this->getHelperName();
		return $this->getView()->$helper($name, $value, $this->getAttribs());
	}
}