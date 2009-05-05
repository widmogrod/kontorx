<?php
require_once 'Zend/Form/Decorator/Abstract.php';
class KontorX_Form_Decorator_FCKEditor extends Zend_Form_Decorator_Abstract {
	private $_basePath = '/js/fckeditor/';

	public function setBasePath($path) {
		$this->_basePath = $path;
	}

	public function render($content) {
		$inline = $this->getElement()->getView()->inlineScript();
		
		$script = 'var editor = new FCKeditor("' . $this->getElement()->getId() . '");'. PHP_EOL .
				  'editor.BasePath = "' . $this->_basePath . '";'. PHP_EOL;

		if (null !== ($lang = $this->getOption('Language'))) {
			$script .= 'editor.Config["DefaultLanguage"] = "'. $lang .'";'. PHP_EOL;
		}
		if (null !== ($skin = $this->getOption('skin'))) {
			$script .= 'editor.Config["SkinPath"] = "/js/fckeditor/editor/skins/'. $skin .'/";'. PHP_EOL;
		}

		$script .= 'editor.ReplaceTextarea();';
		$inline->appendScript($script);

		return $content;
	}	
}