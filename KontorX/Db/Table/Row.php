<?php
require_once 'Zend/Db/Table/Row/Abstract.php';
class KontorX_Db_Table_Row extends Zend_Db_Table_Row_Abstract
{
	public function __clone() 
	{
		if (!is_array($this->_primary)) {
			return;
		}

		if (empty($this->_cleanData)) {
			return;
		}

		/**
		 * Poniższe opcje maja zapewnic że po wykonaniu na
		 * sklonowanym obiekcie ->save() zostanie dodany nowy rekord!
		 */
		
		// zapewnia insert
		$this->_cleanData = null;
		// usunięcie informacji o primary key
		$this->_data = array_diff_key($this->_data, array_flip($this->_primary));
		// pola modyfikowalne ustaw na wszystkie!
		$this->_modifiedFields = $this->_data;
	}	
}