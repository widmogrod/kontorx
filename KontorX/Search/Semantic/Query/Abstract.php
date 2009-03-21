<?php
require_once 'KontorX/Search/Semantic/Query/Interface.php';
abstract class KontorX_Search_Semantic_Query_Abstract implements KontorX_Search_Semantic_Query_Interface {
	const CONTENT = 'CONTENT';
	const CONTENT_LEFT = 'CONTENT_LEFT';
	const CONTENT_RIGHT = 'CONTENT_RIGHT';
	
	public $_content = array(
		self::CONTENT => null,
		self::CONTENT_LEFT => null,
		self::CONTENT_RIGHT => null
	);

	public function getContent() {
		return $this->_content[self::CONTENT];
	}
	
	public function getContentLeft() {
		return $this->_content[self::CONTENT_LEFT];
	}
	
	public function getContentRight() {
		return $this->_content[self::CONTENT_RIGHT];
	}
	
	protected function _setContent($type, $content) {
		switch ($type) {
			case self::CONTENT:
			case self::CONTENT_LEFT:
			case self::CONTENT_RIGHT:
				$this->_content[$type] = $content;
				break;
			default:
				require_once 'KontorX/Search/Semantic/Exception.php';
				throw new KontorX_Search_Semantic_Exception(sprintf("Undefinded content type '%s'", $type));
		}
		
		return $this;
	}
}
