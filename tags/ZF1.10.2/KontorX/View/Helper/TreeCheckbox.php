<?php
class KontorX_View_Helper_TreeCheckbox extends KontorX_View_Helper_Tree_Abstract {
	
	/**
	 * @var Zend_View_Helper_FormCheckbox
	 */
	protected $_checkBox;
	
	/**
	 * Opcje wykorzystywane w checkBox
	 * @var array
	 */
	protected $_options = array(
		'name' => '__tree_checkBox[]', 
		'value' => 'id',
		'label' => 'name',
		'attribs' => null, 
		'checkedOptions' => array(),
		'checkedValues' => array(),
	);

	/**
	 * Renderowanie stroktory drzewiastej
	 *
	 * @param KontorX_Db_Table_Tree_Rowset_Abstract $rowset
	 * @return string
	 */
	public function treeCheckbox(KontorX_Db_Table_Tree_Rowset_Abstract $rowset, array $options = array()) {
		if (null === $this->_checkBox) {
			$this->_checkBox = $this->view->getHelper('FormCheckbox');
		}
		
		$this->setOptions($options);
		return $this->tree($rowset, @$options['UlClass']);
	}

	/**
	 * @param array $options
	 * @return void
	 */
	public function setOptions(array $options) {
		// filtrowanie kluczy opcji
		$this->_options = array_intersect_key($options, $this->_options) + $this->_options;
	}
	
	/**
	 * Przygotowanie formatu zagniezdzenia
	 *
	 * @param KontorX_Db_Table_Tree_Row_Abstract $row
	 * @return string
	 */
	protected function _data(KontorX_Db_Table_Tree_Row_Abstract $row)
	{
		// nazwa elementu checkbox
		$name   = $this->_options['name'];
		// sprawdzenie czy nazwa checkbox jest z []
		if (substr($name, -2, 2) != '[]') {
			// dodanie [] by był multi checkBox!
			$name .'[]';
		}

		// vartosc elementu checkbox
		$value  = (string) $row->__get($this->_options['value']);

		// atrybuty alememtu checkbox
		$attribs = $this->_options['attribs'];
		// ustawianie ID dla label
		$attribs['id'] = preg_replace('#[^\wd_\-]#i','',$name.'_'.$value);

		// sprawdz czy checbox jest 'checked'
		$checkedValues = (array) $this->_options['checkedValues'];
		if (in_array($value, $checkedValues)) {
			$attribs['checked'] = true;
		}

		// opche checkboxa
		$checkedOptions = (array) $this->_options['checkedOptions'];

		$checkBox = (string) $this->_checkBox->formCheckbox($name, $value, $attribs, $checkedOptions);
		
		// Etykieta elementu checkBox
		$label    = (string) $row->__get($this->_options['label']);
		
		return sprintf('<label for="%s">%s %s</label>',
							$attribs['id'],
							$checkBox,
							$label);
	}
}