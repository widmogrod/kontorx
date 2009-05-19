<?php
class KontorX_Navigation_Recursive extends RecursiveIteratorIterator {
	/**
	 * @var Zend_Navigation_Container
	 */
	private $_navigation = null;

	/**
	 * @var Zend_Navigation_Page
	 */
	private $_currentPage = null;
	
	/**
	 * @var Zend_Navigation_Page
	 */
	private $_parentPage = null;
	
	/**
	 * @param Traversable $iterator
	 * @param Zend_Navigation_Container $navigation
	 * @param $mode
	 * @param $flags
	 * @return void
	 */
	public function __construct(Traversable $iterator, Zend_Navigation_Container $navigation = null, $mode = self::SELF_FIRST, $flags = 0) {
		parent::__construct($iterator, $mode, $flags);

		if (null !== $navigation) {
			$this->setNavigation($navigation);
		}
	}

	/**
	 * @param Zend_Navigation_Container $navigation
	 * @return KontorX_Navigation_Recursive
	 */
	public function setNavigation(Zend_Navigation_Container $navigation) {
		$this->_navigation = $navigation;
		return $this;
	}

	/**
	 * @return Zend_Navigation
	 */
	public function getNavigation() {
		if (null === $this->_navigation) {
			require_once 'Zend/Navigation.php';
			$this->_navigation = new Zend_Navigation();
		}
		return $this->_navigation;
	}
	
	/**
	 * @return Zend_Navigation_Container
	 */
	public function create() {
		$result = $this->_parentPage = $this->getNavigation();

		$this->rewind();
		while ($this->valid()) {
			$current = $this->current();
			$current = $this->prepare($current);

			$this->_currentPage = Zend_Navigation_Page::factory($current);
			
			$this->_parentPage->addPage($this->_currentPage);
			$this->next();
		}

		return $result;
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

	/**
	 * @param KontorX_Db_Table_Tree_Row_Abstract $current
	 * @return array
	 */
	public function prepare(KontorX_Db_Table_Tree_Row_Abstract $current) {
		$current = $current->toArray();
		if (null !== $this->_visitor) {
			$current = $this->_visitor->prepare($current);
		}
		return $current;
	}
	
	public function beginChildren() {
		$this->_parentPage = $this->_currentPage;
	}
	
	public function endChildren() {
		$this->_parentPage = $this->_navigation;
	}
}