<?php
require_once 'KontorX/Search/Semantic/Context/Interface.php';
/**
 * @author gabriel
 *
 */
class KontorX_Search_Semantic_Context implements KontorX_Search_Semantic_Context_Interface {
	const WORD_SEPARATOR = ' ';

	/**
	 * @var string
	 */
	private $_input = null;
	
	/**
	 * @var array
	 */
	private $_words = array();
	
	/**
	 * @var int
	 */
	private $_position = 0;

	/**
	 * @param string $input
	 * @return void
	 */
	public function __construct($input = null) {
		if (null !== $input) {
			$this->setInput($input);
		}
	}

	public function getInput() {
		return ($this->count() > 0)
			? implode(self::WORD_SEPARATOR, $this->_words)
			: array(); 
	}
	
	public function setInput($input) {
		$this->_input = (string) $input;
		// karzdy przecinek jako osoby znak
		$this->_input = str_replace(',', ' , ', $this->_input);
		// tablica pozbawiona postych elementow
		$this->_words = array_diff(explode(self::WORD_SEPARATOR, $this->_input), array());
	}
	
	/**
	 * @var array
	 */
	private $_output = array();

	public function setOutput($data) {
		$this->_output = $data;
	}
	
	public function addOutput($name, $data) {
		$this->_output[(string)$name] = $data;
	}
	
	public function getOutput() {
		return $this->_output;
	}
	
	public function clearOutput() {
		$this->_output = array();
	}
	
	public function __clone() {
		$this->clearOutput();
	}
	
	public function __toString() {
		return $this->getInput();
	}

	public function current() {
		return isset($this->_words[$this->_position])
			? $this->_words[$this->_position]
			: null;
	}

	public function key() {
		return $this->_position;
	}

	public function next() {
		if ($this->_position <= $this->count()) {
			++$this->_position;
		}
	}

 	public function rewind() {
 		$this->_position = 0;
 		// Resetuję klucze, bo np. po remove są puste luki!
 		$this->_words = (array) array_values($this->_words);
 	}

	public function valid () {
		return ($this->_position <= $this->count());
	}
	
	public function count() {
		if (null === $this->_count) {
			$this->_count = count($this->_words);
		}
		return $this->_count;
	}
	
	public function remove() {
		if ($this->valid()) {
			unset($this->_words[$this->_position]);
			// Resetuję klucze
//			$this->_words = (array) array_values($this->_words);
			// Resetuję count
			--$this->_count;
		}
	}
}