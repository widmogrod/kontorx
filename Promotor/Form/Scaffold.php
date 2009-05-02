<?php
/**
 * @author gabriel
 *
 */
class Promotor_Form_Scaffold extends Zend_Dojo_Form {
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
    
	private $_pk = null;
	
	public function setPrimaryKey($pk) {
		$this->_pk = $pk;
	}

	public function getPrimaryKey() {
		return $this->_pk;
	}
}