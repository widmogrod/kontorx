<?php
require_once 'Zend/Form/Decorator/Abstract.php';
/**
 * TinyMCE implementation {@link http://tinymce.moxiecode.com}
 * @author gabriel
 */
class KontorX_Form_Decorator_TinyMCE extends Zend_Form_Decorator_Abstract {

	/**
	 * @var string, script path of 
	 */
	private $_scriptName;

	public function setScriptName($path) {
		$this->_scriptName = (string) $path;
	}

	public function render($content) {
		/* @var $el Zend_Form_Element */
		$el = $this->getElement();
		/* @var $view Zend_View_Interface */
		$view = $el->getView();
		/* @var $tinyMCE KontorX_View_Helper_TinyMCE */
		$tinyMCE = $view->tinyMCE();

		$tinyMCE->setJsOptions(array(
			'theme' => 'advanced',
			'skin' => 'o2k7',
			'skin_variant' => 'black',
			'width' => '100%',

			'language' => 'pl',
//			'document_base_url' => $view->baseUrl(),
			'content_css' => $view->baseUrl() . '/css/kontorx/messages-light.css',

			'plugins' => 'safari,style,layer,table,save,advimage,advlink,inlinepopups,preview,media,searchreplace,contextmenu,paste,fullscreen,visualchars,nonbreaking,xhtmlxtras,filemanager',
		
			'theme_advanced_buttons1' => 'save,newdocument,|,cut,copy,paste,pastetext,pasteword,|,search,replace,|,undo,redo,|,bold,italic,underline,strikethrough,|,sub,sup,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,outdent,indent,blockquote',
			'theme_advanced_buttons2' => 'cleanup,removeformat,|,styleselect,formatselect,fontselect,fontsizeselect,|,link,unlink,anchor,filemanager,image,media',
			'theme_advanced_buttons3' => 'tablecontrols,|,insertlayer,moveforward,movebackward,absolute,|,styleprops,|,visualchars,nonbreaking,visualaid,charmap,|,code,preview,|,fullscreen',

			'theme_advanced_toolbar_location' => 'top',
			'theme_advanced_toolbar_align' => 'left',
			'theme_advanced_statusbar_location' => 'bottom',
			'theme_advanced_resizing' => true
		));

		if (null !== $class = $el->getAttrib('class')) {
			$tinyMCE->setJsOption('editor_selector', $class);
		} else {
			$tinyMCE->setMode(KontorX_View_Helper_TinyMCE::MODE_EXACT);
			$tinyMCE->setJsOption('elements', $el->getId());
		}

		if (null !== $this->_scriptName) {
			$tinyMCE->setScriptName($this->_scriptName);
		}
		
		// render TinyMce via helper
		$tinyMCE->render();

		return $content;
	}	
}