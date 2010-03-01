<?php
require_once 'Zend/Validate/Abstract.php';

/**
 * @author gabriel
 *
 * Example:
 * <code>
 * 
 * class My_Form extends Zend_Form {
 *     public function init() {
 *	       $this->_requiredOthers = new KontorX_Validate_Form_RequiredOther($this, array('company', 'nip'));
 *
 *         // init form fields(...)
 *     }
 *  
 *     @override
 *     public function isValid($data) {
 *	       // modyfikuje pola innych wartości
 *		   $this->_requiredOthers->isValid($data);
 *			
 *		   return parent::isValid($data);
 *	   }
 * </code>
 */
class KontorX_Validate_Form_RequiredOther extends Zend_Validate_Abstract {
	
	const REQUIRED_OTHERS = 'REQUIRED_OTHERS';
	
	/**
	 * @var Zend_Form
	 */
	protected $_form;
	
	/**
	 * @var array of Zend_Form_Element
	 */
	protected $_elements;
	
	protected $_messageTemplates = array(
        self::REQUIRED_OTHERS 	=> 'Wypełnij inne pola',
    );

	public function __construct(Zend_Form $form, $elements) {
		$this->setForm($form);
		$this->setElements($elements);
	}
	
	public function isValid($data) {
		$requiredOther = false;

		$elements = $this->getElements();

		/* @var $element Zend_Form_Element */
		foreach ($elements as $element) {
			/**
			 * Jeżeli, który kolwiek z wartości elementów jest różny
			 * od null to wymagaj podania innych wartości dla elementów
			 */
			$value = $element->getValue();
			if ((is_array($value) && count($value) > 0)
					|| strlen($value) > 0)
			{
				$requiredOther = true;
				break;
			}
		}
		
		if (!$requiredOther) {
			return true;
		}

		// TODO: optymalizacja?
		// TODO: zbieranie label i w error message użyć jako value
		foreach ($elements as $element) {
			$element->setRequired(true);
		}

		$this->_error(self::REQUIRED_OTHERS);
		return false;
	}
	
	/**
	 * @param Zend_Form $form
	 * @return void
	 */
	public function setForm(Zend_Form $form) {
		$this->_form = $form;
	}

	/**
	 * @return Zend_Form
	 */
	public function getForm() {
		return $this->_form;
	}
	
	/**
	 * @param array $elements
	 * @return void
	 */
	public function setElements(array $elements) {
		$this->_elements = $elements;
	}

	/**
	 * @return array
	 */
	public function getElements() {
		$elements = array();
		foreach ($this->_elements as $element) {
			$elements[] = $this->_getElement($element);
		}
		return $elements;
	}

	/**
	 * @param Zend_Form_Element|array|string $element
	 * @return unknown_type
	 */
	protected function _getElement($element) {
		if (is_array($element) && count($element) == 2) {
			list($subForm, $element) = $element;
			$element = $this->getForm()->getSubForm($subForm)->getElement($element);
			
		} elseif (is_string($element)) {
			$element = $this->getForm()->getElement($element);
		} else {
			require_once 'Zend/Validate/Exception.php';
			throw new Zend_Validate_Exception('element is not valid name of "Zend_Form_Element"');
		}

		if (!($element instanceof Zend_Form_Element)) {
			require_once 'Zend/Validate/Exception.php';
			throw new Zend_Validate_Exception('element is not instance of "Zend_Form_Element"');
		}

		return $element;
	}
}