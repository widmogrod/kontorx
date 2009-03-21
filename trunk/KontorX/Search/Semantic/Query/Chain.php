<?php
require_once 'KontorX/Search/Semantic/Query/Abstract.php';
class KontorX_Search_Semantic_Query_Chain extends KontorX_Search_Semantic_Query_Abstract {
	/**
	 * @var array 
	 */
	private $_chain = array();

	
	/**
	 * @param KontorX_Search_Semantic_Query_Interface $query
	 * @return void
	 */
	public function addQuery(KontorX_Search_Semantic_Query_Interface $query) {
		$this->_chain[] = $query;
	}

	public function query($content) {
		if (empty($this->_chain)) {
			require_once 'KontorX/Search/Semantic/Exception.php';
			throw new KontorX_Search_Semantic_Exception("No elements in chain");
		}

		$result = array();

		// typ 0 - ze względu na wystapienie
		foreach ($this->_chain as $query) {
			$data = $query->query($content);
			if (null !== $data) {
				// zbieranie danych
				$result[] = $data;
				// łańcuch ma być zgodny z treścią w prawo
				$content = $query->getContentRight();
			} else {
				// TODO Dodać brakeOnFailure - (w domyśle true)
				return null;
			}
		}
		
		// TODO typ 1 - ze wzgledu na położenie
		
		if (empty($result)) {
			return null;
		}
		
		return $result;
	}
}