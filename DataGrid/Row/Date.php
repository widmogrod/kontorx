<?php
require_once 'KontorX/DataGrid/Row/Abstract.php';
class KontorX_DataGrid_Row_Date extends KontorX_DataGrid_Row_Abstract {

	/**
	 * @var Zend_Date
	 */
	private $_date = null;
	
	/**
	 * @return Zend_Date
	 */
	private function _getDate() {
		if (null === $this->_date) {
			require_once 'Zend/Date.php';
			$this->_date = new Zend_Date();
		}
		return $this->_date;
	}
	
	/**
	 * Return a context as a html/text string
	 *
	 * @return string
	 */
	public function render() {
		$value   = $this->getData($this->getColumnName());
		$result  = $value;

		$options = $this->getOptions();

		// czy sa warunki
		if (!isset($options['conditions']) && !isset($options['conditions']['condition'])) {
			require_once 'KontorX/DataGrid/Exception.php';
			throw new KontorX_DataGrid_Exception("Conditions are not definded");
		}

		$date = $this->_getDate();
		
		if (!$date->isDate($value)) {
			require_once 'KontorX/DataGrid/Exception.php';
			throw new KontorX_DataGrid_Exception(sprintf("Value '%s' is not date format", $value));
		}

		if (null === @$options['conditions']['condition']['0']) {
			$options['conditions']['condition'] = array($options['conditions']['condition']);
		}
		
		foreach ((array) $options['conditions']['condition'] as $condition) {
			// czy mamy podstawowe opcje
			if (
//				!isset($condition['type'])
//				|| !isset($condition['compare'])
				!isset($condition['content']))
			{
				trigger_error("condition has not set 'type' or 'compare' or 'content' value", E_USER_WARNING);
				continue;
			}

			$type    = strtoupper(@$condition['type']);
			$compare = strtoupper(@$condition['compare']);
			$content = $condition['content'];

			switch ($type) {
				// time now
				default:
				case 'NOW': break;
			}

			$valid = null;
			switch ($compare) {
				default:
				case 'EARLIER': $valid = $date->isEarlier(new Zend_Date($value)); break;
				case 'LATER':   $valid = $date->isLater(new Zend_Date($value)); break;
			}

			// gdy któryś z warunków jest poprawny, kończymy na nim
			if (true === $valid) {
				$result = str_replace('{value}',$value, $content);
				$result = preg_replace("/{([\wd_\-^}}]+)}/ie", "\$this->getData('\\1')", $result);
				break;
			}
		}

		return $result;
	}
}