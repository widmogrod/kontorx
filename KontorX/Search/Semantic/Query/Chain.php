<?php
require_once 'KontorX/Search/Semantic/Query/Interface.php';
class KontorX_Search_Semantic_Query_Chain implements KontorX_Search_Semantic_Query_Interface {
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
		$result = array();

		// typ 0 - ze względu na wystapienie
		foreach ($this->_chain as $query) {
			if (null !== ($data = $query->query($content))) {
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
		
		return $result;
	}
}