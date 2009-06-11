<?php
class KontorX_Form_Element_JsTree extends Zend_Form_Element {
	public $helper = 'formHidden';
	
	/**
	 * @var array
	 */
	protected $_jsOptions = array();
	
	public function setJsOptions(array $options) {
		$this->_jsOptions = $options;
	}
 
	public function init() {
		$this->addPrefixPath('KontorX_Form_Decorator_','KontorX/Form/Decorator/','decorator');
		$this->setDecorators(array(
			'Errors',
			array('Description',array('tag' => 'p', 'class' => 'description')),
			array('HtmlTag', array('tag' => 'dd', 'id'  => $this->getName() . '-element')),
			array('Label', array('tag' => 'dt')),
			array('JsTree', array('jsOptions' => $this->_jsOptions))
		));
	}
}