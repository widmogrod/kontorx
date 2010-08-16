<?php
class KontorX_Navigation_Recursive extends RecursiveIteratorIterator {
	/**
	 * @var Zend_Navigation_Container
	 */
	private $_navigation = null;
	
	/**
	 * @var Zend_Navigation_Container
	 */
	protected $_children;
	
	/**
	 * @var Zend_Navigation_Container
	 */
	protected $_current;
	
	/**
	 * @var array of @see Zend_Navigation_Container
	 */
	protected $_parent = array();
	
	/**
	 * @var integer
	 */
	protected $_depth = 0;
	
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
		$this->_parent[$this->_depth] = $this->_current = $this->getNavigation();

		$this->rewind();
		while ($this->valid()) {
			$this->_depth = $this->getDepth();

			$current = $this->current();
			$current = $this->prepare($current);
			
			$this->_children = Zend_Navigation_Page::factory($current);
			
			$this->_current->addPage($this->_children);
			$this->next();
		}

		return $this->getNavigation();
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
	 * @param mixed $current
	 * @return array
	 */
	public function prepare($current) {
		if (is_object($current)) {
			if (method_exists($current, 'toArray'))
				$current = $current->toArray();
		}

		if (null !== $this->_visitor) {
			$current = $this->_visitor->prepare($current);
		}

		return $current;
	}

	public function beginChildren() {
		$this->_parent[$this->_depth] = $this->_current;
		$this->_current = $this->_children;
	}

	public function endChildren() {
		// sprawdzam czy mogę zamknąć tą głębokość drzewa
		if (isset($this->_parent[$this->_depth-1])) {
			// domykam tą głęgokość
			$this->_current = $this->_parent[--$this->_depth];
		}
	}
}