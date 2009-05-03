<?php
/** Zend_Form_Element_Xhtml */
require_once 'Zend/Form/Element/File.php';

/**
 * @author gabriel
 *
 */
class KontorX_Form_Element_File extends Zend_Form_Element_File {
    
	protected $_fileValue;
	
	public function setValue($value) {
		$this->_fileValue = $value;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getFileValue() {
		return $this->_fileValue;
	}
	
}
