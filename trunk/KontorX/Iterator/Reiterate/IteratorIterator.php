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
	protected $_current;
	
	/**
	 * @var KontorX_Iterator_Reiterate_Container
	 */
	protected $_parent;
	
	/**
	 * @var KontorX_Iterator_Reiterate_Container
	 */
	protected $_new;

	/**
	 * Enter description here...
	 * @return KontorX_Iterator_Reiterate_Container
	 */
	final public function iterate(KontorX_Iterator_Reiterate_Container $container) {
		$this->_new = $this->_parent = $container;

		$this->rewind();
		while ($this->valid()) {
			$this->_current = $container->getInstance($this->current());
			$this->_parent->addChildren($this->_current, $this->getDepth());
			$this->next();
		}

		return $this->_parent;
	}

	public function beginChildren() {
		$this->_parent = $this->_current;
	}

	public function endChildren() {
		$this->_parent = $this->_new;
	}
}