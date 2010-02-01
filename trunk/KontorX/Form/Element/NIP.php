<?php
class KontorX_Form_Element_NIP extends Zend_Form_Element_Xhtml {

	public function init() {
		$this->addPrefixPath(
			'KontorX_Form_Decorator',
			'KontorX/Form/Decorator',
			self::DECORATOR
		);
		$this->addPrefixPath(
			'KontorX_Validate',
			'KontorX/Validate',
			self::VALIDATE
		);

		$this->setIsArray(true);

		$this->addValidator('NIP');
	}
	
	public function loadDefaultDecorators() {
		if ($this->loadDefaultDecoratorsIsDisabled()) {
            return;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('NIP')
                ->addDecorator('Errors')
                ->addDecorator('Description', array('tag' => 'p', 'class' => 'description'))
                ->addDecorator('HtmlTag', array('tag' => 'dd',
                                                'id'  => $this->getName() . '-element'))
                ->addDecorator('Label', array('tag' => 'dt'));
        }
	}

	/**
	 * @return string
	 */
	public function getValue() {
		$value = parent::getValue();
		return implode($value);
	}

	/**
	 * @return array:
	 */
	public function getNIPParts() {
		$value = (array) array_values(parent::getValue());
		$value += array_fill(0,4,null);
		return $value;
	}
}