<?php
require_once 'KontorX/DataGrid/Row/Editable/Db/Adapter.php';
class KontorX_DataGrid_Row_Editable_Db_FormSelect extends KontorX_DataGrid_Row_Editable_Db_Adapter {

	/**
	 * @var string
	 */
	public $helper = 'formSelect';
	
	/**
	 * @var string
	 */
	protected $_optionKey = 'key';

	/**
	 * @param string $optionKey
	 * @return KontorX_Form_Element_Db_Abstract
	 */
	public function setOptionKey($optionKey) {
		$this->_optionKey = (string) $optionKey;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getOptionKey() {
		return $this->_optionKey;
	}
	
	/**
	 * @var string
	 */
	protected $_optionValue = 'value';

	/**
	 * @param string $optionValue
	 * @return KontorX_Form_Element_Db_Abstract
	 */
	public function setOptionValue($optionValue) {
		$this->_optionValue = (string) $optionValue;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getOptionValue() {
		return $this->_optionValue;
	}

	protected function _onFetch(array $row, array $rowset) {
		// pierwszy rekord pusty..
		if (!isset($rowset[null])) {
			$rowset[null] = null;
		}
		$rowset[$row[$this->_optionKey]] = $row[$this->_optionValue];
		return $rowset;
	}
	
	/**
	 * @return string
	 */
	public function getValue() {
		return $this->getData(
			$this->getAttrib('value', $this->getColumnName()));
	}

	public function render() {
		// Tworzenie prefisku, klucza głównego
		$primaryVal = $this->getPrimaryKey();
		$primaryKey = implode(self::SEPARATOR, array_intersect_key($this->getData(), array_flip($primaryVal)));

		$columnName = $this->getColumnName();
		$prefix = $this->getPrefix();

		// ustawienie attrybutu 'class' dla elementu widoku.
		if (null !== ($class = $this->getAttrib('class'))) {
			$this->setAttrib('class', self::HTML_CLASS . ' ' . $class);
		} else {
			$this->setAttrib('class', self::HTML_CLASS);
		}

		$name = sprintf('%s[%s][%s]', $prefix, $primaryKey, $columnName);
		$value = $this->getValue();
		$attribs = $this->getAttribs();

		$options = $this->_fetchAll();

		$view = $this->getView();
		return $view->{$this->helper}($name, $value, $attribs, $options);		
	}
}