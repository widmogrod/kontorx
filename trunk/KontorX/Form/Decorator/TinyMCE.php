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
			'height' => '400',
		
			'plugins' => 'filemanager,safari,style,layer,table,save,advhr,advimage,advlink,emotions,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras',
			'theme_advanced_buttons1' => 'save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect',
			'theme_advanced_buttons2' => 'cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor,filemanager',
			'theme_advanced_buttons3' => 'tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen',
			'theme_advanced_buttons4' => 'insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,blockquote,pagebreak,|,filemanager',
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