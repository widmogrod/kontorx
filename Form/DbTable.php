<?php
/**
 * KontorX_Form_DbTable
 *
 * @package 	KontorX_Form
 * @version 	0.2.1
 * @license		GNU GPL
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
 */
class KontorX_Form_DbTable extends Zend_Form {

	/**
	 * Tablica kolumn, które ignorujemy podczas tworzenia elementów @see Zend_Form_Element
	 *
	 * @var array
	 */
	protected $_ignoreColumns = array();

	/**
	 * Konstruktor
	 *
	 * @param Zend_Db_Table_Abstract $table
	 * @param array|Zend_Config $options
	 * @param array $ignoreColumns
	 */
	public function __construct(Zend_Db_Table_Abstract $table, $options = null, array $ignoreColumns = array()) {
		parent::__construct();

		// ustawiamy kolumny ignorowane
		$this->_setIgnoreColumns($ignoreColumns);

		if ($options instanceof Zend_Config) {
			$options = $options->toArray();
		} else
		if (!is_array($options)){
			$options = array();
		}

		$this->_setupFormFromTable($table, $options);
	}

	/**
	 * Ustaw klumny, które ignorujemy podczas tworzenia elementów @see Zend_Form_Element
	 *
	 * @param array $columns
	 */
	protected function _setIgnoreColumns(array $columns) {
		$this->_ignoreColumns = $columns;
	}

	/**
	 * Dodaj klumny, które ignorujemy podczas tworzenia elementów @see Zend_Form_Element
	 *
	 * @param array $columns
	 */
	protected function _addIgnoreColumns(array $columns) {
		$this->_ignoreColumns = array_merge($this->_ignoreColumns, $columns);
	}
	
	/**
	 * Dodaj kolumne, którą ignorujemy podczas tworzenia elementu @see Zend_Form_Element
	 *
	 * @param string $column
	 */
	protected function _addIgnoreColumn($column) {
		if (!in_array($column, $this->_ignoreColumns)) {
			$this->_ignoreColumns[] = $column;
		}
	}

	/**
	 * Sprawdza czy kolumna jest ignorowana podczas tworzenia elementu @see Zend_Form_Element
	 *
	 * @param string $column
	 */
	protected function _hasIgnoredColumn($column) {
		return in_array($column, $this->_ignoreColumns);
	}
	
	/**
	 * Tworzy na podstawie @see Zend_Db_Table_Abstract formularz
	 *
	 * @param Zend_Db_Table_Abstract $table
	 * @param array $formOptions
	 */
	protected function _setupFormFromTable(Zend_Db_Table_Abstract $table, array $formOptions = array()) {
		$referenceMap = $table->info(Zend_Db_Table_Abstract::REFERENCE_MAP);
		$metadata = $table->info(Zend_Db_Table_Abstract::METADATA);

		// dodajemy do ignorowania klucz glowny
		$prmaryKay = $table->info(Zend_Db_Table_Abstract::PRIMARY);
		$this->_addIgnoreColumns($table->info(Zend_Db_Table_Abstract::PRIMARY));

		foreach ($metadata as $column => $options) {
			if ($this->_hasIgnoredColumn($column)) {
				// pomijany kolumny
				continue;
			}

			list($element, $elementName, $elementOptions) = $this->_createElement($options);

			// czy mamy do czynienia z kluczem opcym
			$referenceOptions = $this->_isForeignColumn($column, $referenceMap);
			if ($referenceOptions !== false) {
				// TODO Czy $referenceOptions['refColumns'] może być array?
				$refColumns 		= (string) $referenceOptions['refColumns'];
				$refColumnsAsName   = (string) @$referenceOptions['refColumnsAsName'];
				$refClassName		= (string) $referenceOptions['refTableClass'];
				require_once 'Zend/Loader.php';
				Zend_Loader::loadClass($refClassName);
				$refClass 			= new $refClassName();

				$foreign = array(null => null); // pierwsze pole powinno byc zawsze puste!
				foreach ($refClass->fetchAll() as $row) {
					$key = $row->{$refColumns};
					$value = null === $refColumnsAsName
						? $key
						: (isset($row->{$refColumnsAsName}) ? $row->{$refColumnsAsName} : $key);
					$foreign[$key] = $value;
				}
				$elementOptions['multiOptions'] = $foreign;
				$element = 'select';
			}

			// aktualizowanie danych elementu
			if (array_key_exists($column, (array) @$formOptions['elements'])) {
				$formElementOptions = $formOptions['elements'][$column];
				// zmiana typu
				if (isset($formElementOptions['type'])) {
					$element = $formElementOptions['type'];
				}
				// aktualizacja opcji
				if (isset($formElementOptions['options'])) {
					$elementOptions = array_merge (
						$elementOptions,
						(array) $formElementOptions['options']
					);
				}
			}
			
			$this->addElement($element, $elementName, $elementOptions);
		}
	}

	protected $_foreignColumn = array();
	
	/**
	 * Sprawdza czy kolumna w tabeli jest kluczem obcym
	 *
	 * @param string $column
	 * @param array $referenceMap
	 * @return bool
	 */
	protected function _isForeignColumn($column, array $referenceMap) {
		// dzięki temu loop jest tylko 1-raz!
		if (empty($this->_foreignColumn)) {
			foreach ($referenceMap as $option) {
				// TODO Czy $options['columns'] może być array?
				$this->_foreignColumn[(string) $option['columns']] = $option;
			}
		}
		return array_key_exists($column, $this->_foreignColumn)
			? $this->_foreignColumn[$column]
			: false;
	}

	/**
	 * Tworzy Form_Element
	 *
	 * @param array $options
	 * @param bool $loop
	 * @return array
	 */
	protected function _createElement(array $options, $loop = false) {
		// TODO Dodać możliwośc pobierania nazwy z opisu pola w DB (?)
		$elementName = $options['COLUMN_NAME'];
		$elementOptions = array(
			'label' => $elementName
		);

		$dataType = strtoupper($options['DATA_TYPE']);
		switch ($dataType) {
			case 'TIMESTAMP':
			case 'DATETIME':
				$element = 'text';
				// dodanie atrybutu class
				if (isset($elementOptions['class'])) {
					$elementOptions['class'] += ' datetime';
				} else {
					$elementOptions['class'] = 'datetime';
				}
				
				break;
			case 'VARCHAR':
				if ($options['LENGTH'] < 100) {
					$element = 'text';
				} else {
					$element = 'textarea';
					$elementOptions += array('class' => 'medium');
				}
				break;
			case 'TINYINT':
//				$element = 'checkbox';
//				break;
			case 'INTEGER':
			case 'FLOAT':
			case 'BOOL':
			case 'BOOLEAN':
				switch($options['LENGTH']) {
					case 1:
						$element = 'checkbox';
						break;
					default:
						$element = 'text';
				}
				break;
			case 'TEXT':
				$element = 'textarea';
				break;
			default:
				if ($loop) {
					$element = 'text';
					break;
				}

				if (!preg_match('#(?P<type>\w+)\((?P<length>[0-9]+)\)#i', $dataType, $matched)) {
					$element = 'text';
					break;
				}

				$options['DATA_TYPE'] = $matched['type'];
				$options['LENGTH'] 	  = $matched['length'];
				return $this->_createElement($options, true);
		}
		
		return array($element, $elementName, $elementOptions);
	}
}