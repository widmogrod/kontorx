<?php
require_once 'Zend/View/Helper/FormSelect.php';
class KontorX_View_Helper_FormSelectTree extends Zend_View_Helper_FormSelect {

	public function formSelectTree($name, $value = null, $attribs = null,
		array $options = null, $listsep = "<br />\n")
	{
		if (!array_key_exists('rowset',$options)) {
			$message = "Options 'rowset' is not set";
			require_once 'Zend/Validate/Exception.php';
			throw new Zend_Validate_Exception($message);
		}

		$rowset = is_object($options['rowset'])
			? $options['rowset'] : array();

		if (!$rowset instanceof KontorX_Db_Table_Tree_Rowset_Abstract) {
			$message = "param 'options' is not instanceof 'KontorX_Db_Table_Tree_Rowset_Abstract'";
			require_once 'Zend/Validate/Exception.php';
			throw new Zend_Validate_Exception($message);
		}
		if (!array_key_exists('labelCol',$options)) {
			$message = "Options attribute 'labelCol' is not set";
			require_once 'Zend/Validate/Exception.php';
			throw new Zend_Validate_Exception($message);
		}
		
		$labelCol = (string) $options['labelCol'];
		// wartosc etykiety .. jest nie wymagana
		$valueCol = isset($options['valueCol'])
			? (string) $options['valueCol'] : null;

		// tworzenie opcji
		$options = array();
		foreach ($rowset as $row) {
			$label = str_repeat('--', $row->depth) . " ";
			$label .= strip_tags($row->{$labelCol});

			// bez etykiety
			if (null === $valueCol) {
				$options[] = $label;
			} else {
				$options[$row->{$valueCol}] = $label;
			}
		}
		
		return $this->formSelect($name, $value, $attribs, $options, $listsep);
	}
}