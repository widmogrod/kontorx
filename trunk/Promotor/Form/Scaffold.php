<?php
/**
 * @author gabriel
 *
 */
class Promotor_Form_Scaffold extends Zend_Form {
	
	public function getValues($suppressArrayNotation = false) {
        $values = array();
        foreach ($this->getElements() as $key => $element) {
            if (!$element->getIgnore()) {
                $values[$key] = $element->getValue();
            }
        }
        foreach ($this->getSubForms() as $key => $subForm) {
            /*$fValues = $this->_attachToArray($subForm->getValues(true), $subForm->getElementsBelongTo());*/
            $values = array_merge($values, $subForm->getValues(true));
        }

        if (!$suppressArrayNotation && $this->isArray()) {
            $values = $this->_attachToArray($values, $this->getElementsBelongTo());
        }

        return $values;
    }
    
	/**
	 * @var mixed
	 */
	private $_pk = null;
	
	/**
	 * @param mixed $pk
	 * @return void
	 */
	public function setPrimaryKey($pk) {
		$this->_pk = $pk;
	}

	/**
	 * @return mixed
	 */
	public function getPrimaryKey() {
		return $this->_pk;
	}
	
	/**
	 * @var Zend_Db_Table_Rowset_Abstract
	 */
	private $_rowset = null;
	
	/**
	 * @param Zend_Db_Table_Rowset_Abstract $rowset
	 * @return void
	 */
	public function setRowset(Zend_Db_Table_Rowset_Abstract $rowset) {
		$this->_rowset = $rowset;
	}

	/**
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function getRowset() {
		return $this->_rowset;
	}
}