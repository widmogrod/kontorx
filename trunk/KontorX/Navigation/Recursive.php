<?php
class KontorX_Navigation_Recursive extends RecursiveIteratorIterator {
	/**
	 * @var Zend_Navigation_Container
	 */
	private $_navigation = null;

	/**
	 * @param Zend_Navigation_Container $navigation
	 * @param Traversable $iterator
	 * @param $mode
	 * @param $flags
	 * @return void
	 */
	public function __construct(Zend_Navigation_Container $navigation, Traversable $iterator, $mode = self::SELF_FIRST, $flags = 0) {
		$this->_navigation = $navigation;
		parent::__construct($iterator, $mode, $flags);
	}

	/**
	 * @return Zend_Navigation_Container
	 */
	public function create() {
		$this->_parentPage = $this->_navigation;

		$this->rewind();
		while ($this->valid()) {
			$current = $this->current()->toArray();
			if (null !== $this->_visitor) {
				$current = $this->_visitor->prepare($current);
			}

			$this->_currentPage = Zend_Navigation_Page::factory($current);
			
			$this->_parentPage->addPage($this->_currentPage);
			$this->next();
		}

		return $this->_navigation;
	}

	/**
	 * @var KontorX_Navigation_Recursive_Visitor
	 */
	private $_visitor = null;
	
	/**
	 * @param KontorX_Navigation_Recursive_Visitor_Interface $visitor
	 * @return KontorX_Navigation_Recursive
	 */
	public function accept(KontorX_Navigation_Recursive_Visitor_Interface $visitor) {
		$this->_visitor = $visitor;
		return $this;
	}

	private $_open = 0;
	
	public function beginChildren() {
		++$this->_open;
		$this->_parentPage = $this->_currentPage;
//		$nav = $this->_navigation;
//		$this->_navigation = new Zend_Navigation_Container();
//		$nav->addPage($this->_navigation);
//		Zend_Debug::dump($this->getInnerIterator()->toArray(),'inner');
//		Zend_Debug::dump($this->current()->name,'begin');
//		Zend_Debug::dump($this->_open,'begin');
	}
	
	public function endChildren() {
		--$this->_open;
		$this->_parentPage = $this->_navigation;
//		Zend_Debug::dump($this->key(),'end');
//		Zend_Debug::dump($this->_open,'end');
	}
}