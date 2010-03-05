<?php
/**
 * @version $Id$
 * @author $Author$
 */
class KontorX_Form_Element_Date extends Zend_Form_Element_Xhtml {

	public function init() {
		$this->addPrefixPath(
			'KontorX_Form_Decorator',
			'KontorX/Form/Decorator',
			self::DECORATOR
		);

		$this->setIsArray(true);
//		$this->addValidator('Date', array('format' => $this->_format));
	}
	
	/**
	 * @var string
	 */
	protected $_format = 'YYYY-MM-DD';
	
	/**
	 * @param string $format
	 * @return KontorX_Form_Element_Date
	 */
	public function setFormat($format) {
		$this->_format = (string) $format;
		return $this;
	}

	public function loadDefaultDecorators() {
		if ($this->loadDefaultDecoratorsIsDisabled()) {
            return;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('Date')
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
		$value = array_filter($value);

		if (empty($value))
		{
			return null;
		}
		
		$value = trim(implode('-', $value));

		return $value;
	}

	/**
	 * @return array
	 */
	public function getDateParts() {
		$value = parent::getValue();
		
		// YYYY-MM-DD
		if (is_string($value))
		{
			// YYYY-MM-DD HH:MM:SS
			$value = explode(' ', $value);
			// YYYY-MM-DD
			$value = explode('-', $value[0]);
		}

		$value = (array) $value;
		$value += array_fill(0,3,null);
		return $value;
	}
}