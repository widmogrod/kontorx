<?php
class KontorX_Form_Element_NIP extends Zend_Form_Element_Xhtml 
{
	public function init() 
	{
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
	
	public function loadDefaultDecorators() 
	{
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
	public function getValue() 
	{
		$value = (array) parent::getValue();
		return implode($value);
	}

	/**
	 * @return array:
	 */
	public function getNIPParts() 
	{
		$result = array(
			'   ',
			'   ',
			'  ',
			'  '
		);
		
		$value = parent::getValue();

		// usuń wszystkie znaki, które nie są cyfrą
		if (!is_numeric($value) && is_string($value))
			$value = preg_replace('/^[0-9]+/','', $value);
		
		// jeżeli jest to liczba potnij ją w odpowiednim formacie NIP
		if (is_numeric($value)) 
		{
			$position = 0;
			foreach ($result as $key => $part) {
				$length = strlen($part);

				$result[$key] = substr($value, $position, $length);
				$position += $length;
			}
		} else {
			$result = (array) $value;
		}

		$result += array_fill(0,4,null);
		return $result;
	}
}