<?php
require_once 'Zend/Form/Decorator/Abstract.php';
require_once 'Zend/Form/Decorator/Marker/File/Interface.php';
class KontorX_Form_Decorator_File_ImagePreview extends Zend_Form_Decorator_Abstract implements Zend_Form_Decorator_Marker_File_Interface {

	/**
	 * @var string
	 */
	protected $_imagePath;
	
	/**
	 * @param string $path
	 * @return KontorX_Form_Decorator_File_Preview
	 */
	public function setImagePath($path) {
		$this->_imagePath = (string) $path;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getImagePath() {
		if (null === $this->_imagePath) {
			if (null === ($this->_imagePath =$this->getOption('imagePath'))) {
				$this->_imagePath = $this->getElement()->getTransferAdapter()->getDestination();
			}
		}
		return $this->_imagePath;
	}

	/**
	 * @return string
	 */
	protected function _imgAttr() {
		$imgAttr = array('width','height','hspace','vspace','align','style');
		$options = $this->getOptions();
		
		$attr = array_intersect_key($options, array_flip($imgAttr));
		if (count($attr) < 1) {
			return '';
		}
		return vsprintf(implode('="%s"', array_keys($attr)) . '="%s"', $attr);
	}
	
	public function render($content) {
		$element = $this->getElement();
		if (!$element instanceof KontorX_Form_Element_File) {
			return $content;
		}
		
		$name = $element->getFileValue();
		$path = rtrim($this->getImagePath(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $name;
		$attr = $this->_imgAttr(); 
		$image = sprintf('<img src="%s" alt="%s" %s/>', $path, $name, $attr);

		$placement = $this->getPlacement();
		$separator = $this->getSeparator();
		
		switch ($placement) {
            case self::APPEND:
                return $content . $separator . $image;
            case self::PREPEND:
                return $image . $separator . $content;
        }
	}
}