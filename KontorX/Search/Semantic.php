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
     * @return void
     */
    public function addQuery(KontorX_Search_Semantic_Query_Interface $query) {
    	$this->_query[] = $query;
    }
    
    /**
     * @param string $content
     * @return array
     */
    public function query($content) {
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