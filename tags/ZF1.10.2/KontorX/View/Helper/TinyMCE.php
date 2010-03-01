<?php
require_once 'KontorX/View/Helper/JsAbstract.php';

/**
 * TinyMCE implementation {@link http://tinymce.moxiecode.com}
 * @author gabriel
 */
class KontorX_View_Helper_TinyMCE extends KontorX_View_Helper_JsAbstract {
	
	const JS_KEY = 'tiny_mce';
	
	/**
	 * @var arary
	 */
	protected $_jsOptionsSchema = array(
		'mode' => null,
		'elements' => null,

		'theme' => null,
		'skin' => null,
		'skin_variant' => null,
	
		'language' => null,
	
		'width' => null,
		'height' => null,
	
		'document_base_url' => null,
		'content_css' => null,

		'plugins' => null,
		'editor_selector' => null, 
		'theme_advanced_buttons1' => null,
		'theme_advanced_buttons2' => null,
		'theme_advanced_buttons3' => null,
		'theme_advanced_buttons4' => null,
		'theme_advanced_toolbar_location' => null,
		'theme_advanced_toolbar_align' => null,
		'theme_advanced_statusbar_location' => null,
		'theme_advanced_resizing' => null,

		'template_external_list_url' => null,
		'external_link_list_url' => null,
		'external_image_list_url' => null,
		'media_external_list_url' => null,
		'template_replace_values' => null,
		'username' => null,
		'staffid' => null
	);

	/**
	 * @var arary
	 */
	protected $_jsDefaultOptions = array(
		'theme' => 'simple',
	); 

	/**
	 * @var string
	 */
	protected $_scriptName = 'js/tiny_mce/tiny_mce.js';
	
	/**
	 * @param string $script
	 * @return KontorX_View_Helper_TinyMCE
	 */
	public function setScriptName($script) {
		$this->_scriptName = (string) $script;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getScriptName() {
		return $this->_scriptName;
	}
	
	const MODE_TEXTAREAS = 'textareas';
	const MODE_EXACT = 'exact';

	/**
	 * @param string $mode
	 * @return KontorX_View_Helper_TinyMCE
	 */
	public function setMode($mode) {
		$this->setJsOption('mode', (string) $mode);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getMode() {
		if (!isset($this->_jsOptions['mode'])) {
			return $this->_jsOptions['mode'] = self::MODE_TEXTAREAS;
		}
		return $this->_jsOptions['mode'];
	}
	
	/**
	 * @return KontorX_View_Helper_TinyMCE
	 */
	public function tinyMCE(array $jsOptions = null) {
		if (is_array($jsOptions)) {
			$this->setJsOptions($jsOptions);
		}
		return $this;
	}

	/**
	 * @return string
	 */
	public function render() {
		/* @var $headScript Zend_View_Helper_HeadScript */
		$headScript = $this->view->getHelper('headScript');
		$headScript->offsetSetFile(self::JS_KEY, $this->getScriptName());

		$this->getMode();
		
		$options = $this->_getJsOptions();
		/*$replace = array(
			'{{id}}' => $this->_id,
			'{{fullyQualifiedName}}' => $this->_fullyQualifiedName
		);
		$options = str_replace(array_keys($replace), $replace, $options);*/

		$script = sprintf('tinyMCE.init(%s);',$options);
	    $this->view->getHelper('inlineScript')
			->appendScript($script);
		return '';
	}
}