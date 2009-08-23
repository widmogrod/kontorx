<?php
class KontorX_Iterator_Reverse implements OuterIterator, Countable {
	/**
	 * @var Iterator
	 */
	private $_it;

	/**
	 * @var array
	 */
	private $_data = array();

	/**
	 * @var integer
	 */
	private $_pointer = 0;
	
	/**
	 * @var integer
	 */
	private $_count = 0;
	
	/**
	 * @param Iterator $it
	 * @return void
	 */
	public function __construct(Iterator $it) {
		$this->_it = $it;
		$this->_fetch();
	}
	
	/**
	 * Zbiera informacje z iteratora.. i ustawia je w kolejności odwrotnej
	 * @return void
	 */
	private function _fetch() {
		$this->_data = array();

		/* @var Iterator */
		$it = $this->getInnerIterator();
		while ($it->valid()) {
			$data = array(
				'key' => $it->key(),
				'current' => $it->current()
			);

			// dodaj do poczatku
			array_unshift($this->_data, $data);

			++$this->_count;
			
			$it->next();
		}
	}

	function getInnerIterator () {
		return $this->_it;
	}

	public function current () {
		return @$this->_data[$this->_pointer]['current'];
	}

	public function next () {
		++$this->_pointer;
	}

	public function key () {
		return @$this->_data[$this->_pointer]['key'];
	}

	public function valid () {
		return $this->_pointer < $this->_count;
	}

	public function rewind () {
		$this->_it->rewind();
		$this->_pointer = 0;
		$this->_count = 0;

		$this->_fetch();
	}
	
	public function count() {
		return $this->_count;
	}
}