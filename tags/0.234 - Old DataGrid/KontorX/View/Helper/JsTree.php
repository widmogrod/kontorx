<?php
/**
 * Implementation jsTree @link http://www.jstree.com/ version 0.9.8
 * @author gabriel
 */
class KontorX_View_Helper_JsTree extends KontorX_View_Helper_JsAbstract {

	/**
	 * @var string
	 */
	const DATA_TYPE_XML = 'xml';
	const DATA_TYPE_JSON = 'json';

	/**
	 * @var string
	 */
	protected $_scriptPath = 'js/jsTree';

	/**
	 * @var string
	 */
	protected $_dataType;
	
	/**
	 * @var string
	 */
	protected $_id;
	
	/**
	 * @var array
	 */
	protected $_jsOptionsSchema = array(
		'data' => array(
				'type' => null,
		        'method' => null,
		        'async' => null,
		        'async_data' => null,
		        'url' => null,
		        'json' => null,
		        'xml' => null
		),
		'selected' => null,
		'opened' => null,
		'languages' => null,
		'path' => null,
		'cookies' => null,
		'ui' => array(
			'dots' => null,
			'rtl' => null,
			'animation' => null,
			'hover_mode' => null,
			'scroll_spd' => null,
			'theme_path' => null,
			'theme_name' => null,
			'context' => array(
				'id' => null,
				'label' => null,
				'icon' => null,
				'visible' => null, 
				'action' => null
			),
		),
		'rules' => array(
			'multiple' => null,
			'metadata' => null,
			'type_attr' => null,
			'multitree' => null,
			'createat' => null,
			'use_inline' => null,
			'clickable' => null,
			'renameable' => null,
			'deletable' => null,
			'creatable' => null,
			'draggable' => null,
			'dragrules' => null,
			'drag_copy' => null,
			'droppable' => null,
			'drag_button' => null
		),
		'lang' => array(
			'new_node' => null,
			'loading' => null
		),
		'callback' => array(
			'beforechange' => null,
			'beforeopen' => null,
			'beforeclose' => null,
			'beforemove' => null,
			'beforecreate' => null,
			'beforerename' => null,
			'beforedelete' => null,
			'onJSONdata' => null,
			'onselect' => null,
			'ondeselect' => null,
			'onchange' => null,
			'onrename' => null,
			'onmove' => null,
			'oncopy' => null,
			'oncreate' => null,
			'ondelete' => null,
			'onopen' => null,
			'onopen_all' => null,
			'onclose' => null,
			'error' => null,
			'ondblclk' => null,
			'onload' => null,
			'onfocus' => null,
			'ondrop' => null
		)
	);

	/**
	 * @var arary
	 */
	protected $_jsDefaultOptions = array(); 

	/**
	 * @param array $options
	 * @return KontorX_View_Helper_JsTree
	 */
	public function jsTree($id, array $options = null) {
		$this->_id = (string) $id;
		if (null !== $options) {
			$this->_initOptions($options);
		}
		return $this;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->render();
	}

	/**
	 * @return string
	 */
	public function render() {
		$this->_setup();
		
		$options = $this->_getJsOptions();
		$replace = array(
			'{{id}}' => $this->_id,
			'{{fullyQualifiedName}}' => $this->_fullyQualifiedName
		);
		$options = str_replace(array_keys($replace), $replace, $options);

		$script = sprintf('jQuery(function ($){$("#%s").tree(%s);});', $this->_id, $options);
		
	    $this->view->getHelper('headScript')
			->appendScript($script);
		return '';
	}

	/**
	 * @return KontorX_View_Helper_JsTree
	 */
	protected function _setup() {
		$headScript = $this->view->getHelper('headScript');
		$headScript
			->offsetSetFile(71, $this->_scriptPath . '/_lib/css.js')
			->offsetSetFile(72, $this->_scriptPath . '/source/tree_component.min.js');
		$headLink = $this->view->getHelper('headLink');
		$headLink
			->appendStylesheet($this->_scriptPath . '/source/tree_component.css');

		switch($this->_dataType) {
			case self::DATA_TYPE_XML:
				$headScript
					->offsetSetFile(73, $this->_scriptPath . '/_lib/sarissa.js')
					->offsetSetFile(74, $this->_scriptPath . '/_lib/sarissa_ieemu_xpath.js')
					->offsetSetFile(75, $this->_scriptPath . '/_lib/jquery.xslt.js');
				break;
		}
		return $this;
	}
	
	/**
	 * @param array $options
	 * @return KontorX_View_Helper_JsTree
	 */
	protected function _initOptions(array $options) {
		foreach ($options as $name => $value) {
			$method = 'set'.ucfirst($name);
			if (method_exists($this, $method)) {
				call_user_func_array(array($this, $method), (array) $value);
				unset($options[$name]);
			}
		}
		$this->setJsOptions((array) $options);
		return $this;
	}
	
	protected $_fullyQualifiedName;
	
	/**
	 * Enter description here...
	 * @return unknown_type
	 */
	public function setFullyQualifiedName($name) {
		$this->_fullyQualifiedName = $name;
	}

	/**
	 * @param string $dataType
	 * @return KontorX_View_Helper_JsTree
	 */
	public function setDataType($dataType) {
		$this->_dataType = strtolower((string) $dataType);
		return $this;
	}
	
	/**
	 * @param string $path
	 * @return KontorX_View_Helper_JsTree
	 */
	public function setScriptPath($path) {
		$this->_scriptPath = rtrim((string) $path,'/');
		return $this;
	}
}