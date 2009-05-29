<?php
class KontorX_View_Helper_PageBlocks {

	/**
	 * @var Zend_View_Abstract
	 */
	protected $_view = null;
	protected $_blocks = array();

	public function __construct(Zend_View_Abstract $view){
//		if (null === $view) {
//			Zend_Debug::dump(Zend_Registry::getArrayCopy());
//			$view = Zend_Registry::get('Zend_View');
//		}

		$this->_view   = $view;
		$this->_blocks = $this->_prepareBlocks();
	}

	protected function _prepareBlocks() {
		if (!is_array($this->_view->pageBlocks) && !$this->_view->pageBlocks instanceof Traversable) {
			return array();
		}

		$blocks = array();
		foreach ($this->_view->pageBlocks as $block) {
			$blocks[$block->url] = $block->content;
		}
		return $blocks;
	}

	public function __get($name) {
		return array_key_exists($name, $this->_blocks)
			? $this->_blocks[$name] : null;
	}
}
?>