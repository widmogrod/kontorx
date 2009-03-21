<?php
require_once 'KontorX/Search/Semantic/Query/Abstract.php';
class KontorX_Search_Semantic_Query_Chain extends KontorX_Search_Semantic_Query_Abstract {
	/**
     * @var array 
     */
    private $_chain = array();
   
    /**
     * @param KontorX_Search_Semantic_chain_Interface $query
     * @param string $name
     * @return KontorX_Search_Semantic
     */
    public function addQuery(KontorX_Search_Semantic_Query_Interface $query, $name) {
    	if (array_key_exists($name, $this->_chain)) {
    		require_once 'KontorX/Search/Semantic/Exception.php';
			throw new KontorX_Search_Semantic_Exception(sprintf("Query element by name '%s' exsists. Remove query element", $name));
    	}

    	$this->_chain[$name] = $query;
    	return $this;
    }
    
    /**
     * @param string $name
     * @return KontorX_Search_Semantic
     */
    public function removeQuery($name) {
    	if (array_key_exists($name, $this->_chain)) {
    		unset($this->_chain[$name]);
    	}
    	return $this;
    }

	public function query($content) {
		if (empty($this->_chain)) {
			require_once 'KontorX/Search/Semantic/Exception.php';
			throw new KontorX_Search_Semantic_Exception("No elements in chain");
		}

		$result = array();

		// typ 0 - ze względu na wystapienie
		foreach ($this->_chain as $queryName => $queryInstance) {
			$data = $queryInstance->query($content);
			if (null !== $data) {
				// zbieranie danych
				$result[$queryName] = $data;
				// łańcuch ma być zgodny z treścią w prawo
				$content = $queryInstance->getContentRight();
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