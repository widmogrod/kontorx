<?php
require_once 'KontorX/Search/Semantic/Query/Abstract.php';
class KontorX_Search_Semantic_Query_Date extends KontorX_Search_Semantic_Query_Abstract {

	/**
	 * @var Zend_Date
	 */
//	private $_date = null;
	
	/**
	 * @param array $array
	 * @return void
	 */
	public function __construct(array $array = null) {
//		require_once 'Zend/Date.php';
//		$this->_date = new Zend_Date();
	}

	public function query($content) {
		$wordsLeft = array();

		$words = explode(self::SEPARATOR, $content);
		while ($word = array_shift($words)) {
			if (false !== strtotime($word)) {
//				Zend_Date::isDate();
				// ustawieni tresci
				$this->_setContent(self::CONTENT, $word);
				$this->_setContent(self::CONTENT_LEFT, implode(self::SEPARATOR, $wordsLeft));
				$this->_setContent(self::CONTENT_RIGHT, implode(self::SEPARATOR, $words));

				return $word;
			} else
			if (is_numeric($word)) {
				$hour = (int) $word;
				if ($hour >= 0 || $hour <= 24) {
					$this->_setContent(self::CONTENT, $word);
					$this->_setContent(self::CONTENT_LEFT, implode(self::SEPARATOR, $wordsLeft));
					$this->_setContent(self::CONTENT_RIGHT, implode(self::SEPARATOR, $words));
	
					return $hour;
				}
			} else {
				$wordsLeft[] = $word;
			}
		}
		return null;
	}
}