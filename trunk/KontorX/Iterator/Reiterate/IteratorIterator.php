<?php
/**
 * Re-iterate Recursive iterator into specific container @see KontorX_Iterator_Reiterate_Container
 * 
 * Main task of this is creating difrent data structure context... @see Promotor_Iterator_Reiterate_Container_JsTreeArray
 * 
 * @author gabriel
 */
class KontorX_Iterator_Reiterate_IteratorIterator extends RecursiveIteratorIterator {
	/**
	 * @var KontorX_Iterator_Reiterate_Container
	 */
	protected $_children;
	
	/**
	 * @var KontorX_Iterator_Reiterate_Container
	 */
	protected $_current;
	
	/**
	 * @var array of @see KontorX_Iterator_Reiterate_Container
	 */
	protected $_parent = array();
	
	/**
	 * @var integer
	 */
	protected $_depth = 0;

	/**
	 * Enter description here...
	 * @return KontorX_Iterator_Reiterate_Container
	 */
	final public function iterate(KontorX_Iterator_Reiterate_Container $container) {
		$this->_parent[$this->_depth] = $this->_current = $container;

		$this->rewind();
		while ($this->valid()) {
			$this->_depth = $this->getDepth();
			$this->_children = $container->getInstance($this->current());
			$this->_current->addChildren($this->_children, $this->getDepth());
			$this->next();
		}

		return $this->_current;
	}

	public function beginChildren() {
		$this->_parent[$this->_depth] = $this->_current;
		$this->_current = $this->_children;
	}

	public function endChildren() {
		if (isset( $this->_parent[$this->_depth-1]))
			$this->_current = $this->_parent[$this->_depth-1];
	}
}