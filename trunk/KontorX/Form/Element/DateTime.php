<?php
/**
 * @version $Id$
 * @author $Author$
 */
class KontorX_Form_Element_DateTime extends Zend_Form_Element_Xhtml {

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
	protected $_format = 'YYYY-MM-DD HH:MM:SS';
	
	/**
	 * @param string $format
	 * @return KontorX_Form_Element_Date
	 */
	public function setFormat($format) 
	{
		$this->_format = (string) $format;
		return $this;
	}

	public function loadDefaultDecorators() 
	{
		if ($this->loadDefaultDecoratorsIsDisabled()) 
		{
            return;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) 
        {
            $this->addDecorator('DateTime')
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
		$part1 = array_slice($value, 0, 3);
		$part1 = implode('-', $part1);
		$part2 = array_slice($value, 3, 6);
		$part2 = implode(':', $part2);

		$value = $part1 . ' ' . $part2;
		return $value;
	}

	/**
	 * @return array
	 */
	public function getParts() 
	{
		$value = parent::getValue();

		// YYYY-MM-DD
		if (is_string($value))
		{
			// YYYY-MM-DD HH:MM:SS
			$part = explode(' ', $value);
			$value = array_fill(0,6,null);

			list($value[0], $value[1], $value[2]) = explode('-', $part[0]);
			list($value[3], $value[4], $value[5]) = explode(':', $part[1]);
		}

		return $value;
	}
}