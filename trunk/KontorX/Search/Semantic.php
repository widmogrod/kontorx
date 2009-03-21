<?php
/**
 * KontorX_Search_Semantic
 *
 * @author gabriel
 */
class KontorX_Search_Semantic {
	 
	/**
	 * Separator oddzielnych słów
	 */
	const WORDS_SEPARATOR = ' ';
	
	/**
	 * Separator oddzielnej logiki w zdaniu
	 */
	const LOGIC_SEPARATOR = ',';

    /**
     * @var array 
     */
    private $_query = array();
   
    /**
     * @param KontorX_Search_Semantic_Query_Interface $query
     * @param string $name
     * @return KontorX_Search_Semantic
     */
    public function addQuery(KontorX_Search_Semantic_Query_Interface $query, $name) {
    	if (array_key_exists($name, $this->_query)) {
    		require_once 'KontorX/Search/Semantic/Exception.php';
			throw new KontorX_Search_Semantic_Exception(sprintf("Query element by name '%s' exsists. Remove query element", $name));
    	}

    	$this->_query[$name] = $query;
    	return $this;
    }
    
    /**
     * @param string $name
     * @return KontorX_Search_Semantic
     */
    public function removeQuery($name) {
    	if (array_key_exists($name, $this->_query)) {
    		unset($this->_query[$name]);
    	}
    	return $this;
    }
    
    /**
     * @param string $content
     * @return array
     */
    public function query($content) {
    	if (empty($this->_query)) {
			require_once 'KontorX/Search/Semantic/Exception.php';
			throw new KontorX_Search_Semantic_Exception("No query elements");
		}
		
    	if (empty($content)) {
			require_once 'KontorX/Search/Semantic/Exception.php';
			throw new KontorX_Search_Semantic_Exception("attribute 'content' can not be empty");
		}

    	$content = (string) $content;
    	if (false !== strstr($content, self::LOGIC_SEPARATOR)) {
    		$logicContent = explode(self::LOGIC_SEPARATOR, $content);
    		$logicContent = array_map('trim', $logicContent);
    		$logicContent = array_filter($logicContent);
    	} else {
    		$logicContent = array($content);
    	}

    	$result = array();
    	// każda wydzielona zawartość logiczna jest oddzielnie analizowana
    	foreach ($logicContent as $i => $content) {
	    	foreach ($this->_query as $queryName => $queryInstance) {
	    		$data = $queryInstance->query($content);
	    		// czy zapytanie znalazło to czego szuka ;]?
	    		if (null !== $data) {
	    			if (!isset($result[$i])) {
	    				$result[$i] = array();
	    			}

	    			$result[$i][$queryName] = $data;
	    		}
	    	}
    	}

    	return $result;
    }
}