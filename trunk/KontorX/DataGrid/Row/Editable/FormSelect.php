<?php
require_once 'KontorX/DataGrid/Row/Editable/Abstract.php';
class KontorX_DataGrid_Row_Editable_FormSelect extends KontorX_DataGrid_Row_Editable_Abstract {

	protected $_helper = 'formSelect';

	/**
	 * @var array
	 */
	protected $_multiOptions = array();
	
	/**
	 * @param array $multiOptions
	 * @return void
	 */
	public function setMultiOptions(array $multiOptions) {
		$this->_multiOptions = $multiOptions;
	}
	
	public function addMultiOption($key, $value) {
		$this->_multiOptions[$key] = $value;
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
		
		// ustawienie attrybutu 'class' dla elementu widoku.
		if (null !== ($class = $this->getAttrib('class'))) {
			$this->setAttrib('class', self::HTML_CLASS . ' ' . $class);
		} else {
			$this->setAttrib('class', self::HTML_CLASS);
		}

		$helper = $this->getHelperName();
		return $this->getView()->$helper($name, $value, $this->getAttribs(), $this->getMultiOptions());
	}
}