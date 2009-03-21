<?php
/**
 * KontorX_Search_Semantic
 *
 * @author gabriel
 */
class KontorX_Search_Semantic {
    
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
    	$result = array();
    	
    	foreach ($this->_query as $query) {
    		if (null !== $query->query($content)) {
    			$result[] = $query;
    		}
    	}

    	return $result;
    }
}