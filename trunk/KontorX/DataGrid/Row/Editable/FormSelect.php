<?php
require_once 'KontorX/DataGrid/Row/Editable/Abstract.php';
class KontorX_DataGrid_Row_Editable_FormSelect extends KontorX_DataGrid_Row_Editable_Abstract {

	protected $_helper = 'formSelect';

	/**
	 * @var array
	 */
	protected $_multiOptions = null;
	
	/**
	 * @param array $multiOptions
	 * @return void
	 */
	public function setMultiOptions(array $multiOptions) {
		$this->_multiOptions = $multiOptions;
	}
	
	/**
	 * @return array
	 */
	public function getMultiOptions() {
		return $this->_multiOptions;
	}

	public function render() {
		// Tworzenie prefisku, klucza głównego
		$primaryVal = $this->getPrimaryKey();
		$primaryKey = implode(self::SEPARATOR, array_intersect_key($this->getData(), array_flip($primaryVal)));

		$columnName = $this->getColumnName();
		$prefix = $this->getPrefix();

		$name = sprintf('%s[%s][%s]', $prefix, $primaryKey, $columnName);
		$value = $this->getData($columnName);

		$helper = $this->getHelperName();
		return $this->getView()->$helper($name, $value, $this->getAttribs(), $this->getMultiOptions());
	}
}