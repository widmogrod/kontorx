<?php
/**
 * Dekorator dekoruje tylko elementy {@see KontorX_Form_Element_Payments}
 * 
 * @version $Id$
 * @author $Author$
 */
class KontorX_Form_Element_Payments extends Zend_Form_Element_Xhtml
{
	public function init() {
		$this->addPrefixPath(
			'KontorX_Form_Decorator',
			'KontorX/Form/Decorator',
			self::DECORATOR
		);

		$this->setIsArray(true);
	}
	
	public function loadDefaultDecorators() {
		if ($this->loadDefaultDecoratorsIsDisabled()) {
            return;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('Payments')
                ->addDecorator('Errors')
                ->addDecorator('Description', array('tag' => 'p', 'class' => 'description'));
        }
	}

	/**
	 * @var SimpleXMLElement
	 */
	protected $_paymentsTypes;
	
	/**
	 * @param SimpleXMLElement $types
	 * @return void
	 */
	public function setPaymentsTypes(SimpleXMLElement $types) {
		$this->_paymentsTypes = $types;
	}	
	
	/**
	 * @return SimpleXMLElement 
	 */
	public function getPaymentsTypes() {
		return $this->_paymentsTypes;
	}
}