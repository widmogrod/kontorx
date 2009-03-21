<?php
require_once 'KontorX/Search/Semantic/Query/Abstract.php';
class KontorX_Search_Semantic_Query_InArray extends KontorX_Search_Semantic_Query_Abstract {
	
	/**
	 * @var array
	 */
	private $_array = array();

	/**
	 * @param array $array
	 * @return void
	 */
	public function __construct(array $array) {
		$this->_array = $array;
	}

	public function query($content) {
		$wordsLeft = array();

		$words = explode(self::SEPARATOR, $content);
		while ($word = array_shift($words)) {
			if (in_array($word, $this->_array)) {
				// ustawieni tresci
				$this->_setContent(self::CONTENT, $word);
				$this->_setContent(self::CONTENT_LEFT, implode(self::SEPARATOR, $wordsLeft));
				$this->_setContent(self::CONTENT_RIGHT, implode(self::SEPARATOR, $words));

				return $word;
			} else {
				$wordsLeft[] = $word;
			}
		}

		return null;
	}
}