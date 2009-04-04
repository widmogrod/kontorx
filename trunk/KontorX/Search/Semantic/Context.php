<?php
require_once 'KontorX/Search/Semantic/Context/Interface.php';
/**
 * @author gabriel
 *
 */
class KontorX_Search_Semantic_Context implements KontorX_Search_Semantic_Context_Interface {
	const WORD_SEPARATOR = ' ';
	
	const CONTEXT_SEPARATOR = '"';

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
	private $_count = null;
	
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
			: null; 
	}
	
	public function setInput($input) {
		$this->_input = (string) $input;
		// karzdy przecinek jako osoby znak
		$this->_input = str_replace(',', ' , ', $this->_input);

		$words = array();
		// Podziel słowa jako tekst w cudzysłowach
		if (substr_count($this->_input, self::CONTEXT_SEPARATOR) > 1) {
			$explod = explode(self::CONTEXT_SEPARATOR, $this->_input);
			foreach ($explod as $key => $value) {
				$value = trim($value);
				// Tylko parzyste części nie są w cudzysłowie "" 
				if ($key % 2 == 0) {
					// czy klucz zawiera inne podzielne znaki?
					if(substr_count($value, self::WORD_SEPARATOR) > 0) {
						foreach (explode(self::WORD_SEPARATOR, $value) as $value) {
							$words[] = $value;
						}
					} else {
						$words[] = $value;
					}
				} else {
					$words[] = $value;				
				}
			}
		} else {
			$words = explode(self::WORD_SEPARATOR, $this->_input);
		}
		
		// tablica pozbawiona pustych elementow
		$words = array_diff($words, array(''));
		// Resetowanie kluczy
		$this->_words = array_values($words);
	}
	
	/**
	 * @var array
	 */
	private $_output = array();

	public function setOutput($data) {
		$this->_output = $data;
	}

	public function addOutput($name, $data = null) {
		if (!is_array($this->_output)){
			$this->_output = array($this->_output);
		}

		if (null === $data) {
			array_push($this->_output, $name);
		} else {
			if (is_numeric($name)) {
				if (is_array($data)) {
					/**
					 * Niesprecyzowanie nazwy, łączenie danych.
					 * Pomocne podczas wielozagnieżdzonych interpretatorów.
					 * Umożliwie "leprze" tworzenie tablic wynikowych, bez
					 * dodatkowych zagnieżdzeń.. no chyba że takie sobie zażyczymy
					 * podając nazwę intepretatora.
					 */
					$this->_output = array_merge($this->_output, $data);
				} else {
					$this->_output[(string)$name] = $data;
				}
			} else {
				$this->_output[(string)$name] = $data;
			}
		}
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
 		$this->_count = null;
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
		}
	}
}