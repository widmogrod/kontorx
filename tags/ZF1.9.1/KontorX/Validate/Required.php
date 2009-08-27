<?php

require_once 'Zend/Validate/Abstract.php';

/**
 * KontorX_Validate_Required
 * 
 * @category 	KontorX
 * @package 	KontorX_Validate
 * @version 	0.1.5
 * @license		GNU GPL
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
 */
class KontorX_Validate_Required extends Zend_Validate_Abstract {
	const IS_REQUIRED      = 'isRequired';

    // TODO: TO  english
    protected $_messageTemplates = array(
		self::IS_REQUIRED		=> "Pole wymaga również wypełnienia pola %elementLabel%",
    );

    protected $_elementLabel = null;
    
    protected $_messageVariables = array(
        'elementLabel' => '_elementLabel',
    );

    /**
     * Enter description here...
     *
     * @var Zend_Form
     */
    private $_form = null;
    
    /**
     * Enter description here...
     *
     * @var string
     */
    private $_element = null;

    public function __construct(Zend_Form $form, $element) {
    	$this->_form = $form;
    	$this->_element = $element;
    }
    
	public function isValid($value) {
		$this->_setValue($value);

		$subform = null;
		if (is_array($this->_element)) {
			list($subform, $elementName) = $this->_element;
		} else {
			$elementName = $this->_element;
		}
		
		$element = $this->_form->getElement($elementName);
		$this->_elementLabel = $element->getLabel();

		if (null === $subform) {
			$postValue = @$_POST[$elementName];
		} else {
			$postValue = @$_POST[$subform][$elementName];
		}

		if ($element->getValue() == '' AND $postValue == '') {
			$this->_error(self::IS_REQUIRED);
			return false;
		}
	}
}
?>