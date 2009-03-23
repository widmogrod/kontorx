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
//		if ($this->valid()) {
//			$return = array_slice($this->_words, $this->_position);
//		} else {
			$return = $this->_words;
//		}
		return implode(self::WORD_SEPARATOR, $return);
	}
	
	public function setInput($input) {
		$this->_input = (string) $input;
		$this->_input = str_replace(
			// karzdy przecinek jako osoby znak
			array(','  , '  '),
			array(' , ', ' '),
			$this->_input
		);
		$this->_words = explode(self::WORD_SEPARATOR, $this->_input);
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
		return $this->_words[$this->_position];
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
//			$this->_position = 0;
			$this->_words = array_values($this->_words);
			$this->_count = null;
		}
	}
}