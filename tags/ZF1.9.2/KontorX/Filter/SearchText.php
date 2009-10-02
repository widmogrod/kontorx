<?php
require_once 'Zend/Filter/Interface.php';

/**
 * SearchText
 * 
 * @category 	KontorX
 * @package 	KontorX_Filter
 * @version 	0.1.0
 * @license		GNU GPL
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
 */
class KontorX_Filter_SearchText implements Zend_Filter_Interface {

	private $_replaceText = array();
	private $_minTextLenght = 3;
	private $_wordList = array();

	/**
	 * Enter description here...
	 *
	 * @param string $value
	 * @return boolean|string|null
	 */
	public function filter($value) {
		// Wyczysc tekst z znaków zabronionych
		$value = str_replace($this->_replaceText, null, $value);
		// Sprawdz dlugosc wyszukiwanej frazy
		if(strlen($value) < $this->_minTextLenght) {
			return null;
		}
		// Wydziel  słowa
		$value = str_replace('-', ' ', $value);
		$valueArray = explode(' ', $value);
		// Przefiltruj slowa
		$this->_wordList = array_filter($valueArray,array($this,'_filter'));
		
		// Musza byc jakies slowa, nie ma czyli nie przeszlo filtracji
		if (count($this->_wordList) == 0) {
			return null;
		}

		return implode(' ', $this->_wordList);
	}

	/**
	 * Enter description here...
	 *
	 * @param integer $lenght
	 */
	public function setMinTextLenght($lenght) {
		$this->_minTextLenght = (int) $lenght;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param array $array
	 */
	public function setReplaceText(array $array) {
		$this->_replaceText = $array;
	}

	/**
	 * Enter description here...
	 *
	 * @return array
	 */
	public function getWordList() {
		return $this->_wordList;
	}

	/**
	 * Enter description here...
	 *
	 * @param string $value
	 * @return boolean
	 */
	private function _filter($value) {
		// Puste stringi są nam niepotrzebne
		if ($value == '') {
			return false;
		}
		
		return true;
	}
}
?>