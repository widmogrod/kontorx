<?php
require_once 'Zend/Form/Decorator/Abstract.php';
class KontorX_Form_Decorator_FCKEditor extends Zend_Form_Decorator_Abstract {
	private $_basePath = '/js/fckeditor/';

	public function setBasePath($path) {
		$this->_basePath = $path;
	}

	public function render($content) {
		$view = $this->getElement()->getView();
		$view->inlineScript()->appendScript("
		var editor = new FCKeditor('" . $this->getElement()->getId() . "');
		editor.BasePath = '" . $this->_basePath . "';
		editor.ReplaceTextarea();");
		return $content;
	}	
}