<?php
abstract class KontorX_Pdf_Adapter_Abstract
{
	abstract public function output();
	
	public function setOptions(array $options)
	{
		foreach ($options as $key => $value) 
		{
			$methodName = 'set' . ucfirst($key);
			if (method_exists($this, $methodName))
				$this->$methodName($value);
		}
	}
	
	protected $_fileame;
	
	public function setFilename($fileame)
	{
		$this->_fileame = $fileame;
	}
	
	public function getFilename()
	{
		if (null === $this->_fileame)
		{
			return 'document.pdf';
		}

		return $this->_fileame;
	}

	protected $_html;
	
	public function setHtml($html)
	{
		$this->_html = $html;
	}
	
	public function getHtml()
	{
		return $this->_html;
	}
}