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
    private $_interpreter = array();
   
    /**
     * @param KontorX_Search_Semantic_Query_Interface $interpreter
     * @param string $name
     * @return KontorX_Search_Semantic
     */
    public function addInterpreter(KontorX_Search_Semantic_Interpreter_Interface $interpreter, $name) {
    	$this->_interpreter[(string)$name] = $interpreter;
    	return $this;
    }
    
    /**
     * @param string $name
     * @return KontorX_Search_Semantic
     */
    public function removeInterpreter($name) {
    	if (array_key_exists($name, $this->_interpreter)) {
    		unset($this->_interpreter[$name]);
    	}
    	return $this;
    }
    
    /**
     * @param KontorX_Search_Semantic_Context_Interface|string $context
     * @return void
     */
    public function interpret($context) {
    	if (is_string($context)) {
    		$context = new KontorX_Search_Semantic_Context_Interface($context);
    	} elseif (!$context instanceof KontorX_Search_Semantic_Context_Interface) {
    		require_once 'KontorX/Search/Semantic/Exception.php';
			throw new KontorX_Search_Semantic_Exception("attribute 'context' is no instance of 'KontorX_Search_Semantic_Context_Interface'");
    	}

    	if (empty($this->_interpreter)) {
			require_once 'KontorX/Search/Semantic/Exception.php';
			throw new KontorX_Search_Semantic_Exception("No interpreters elements");
		}

		$interpreterContext = clone $context;
    	foreach ($this->_interpreter as $interpreterName => $interpreterInstance) {
    		if (true === $interpreterInstance->interpret($interpreterContext)) {
    			$context->addOutput($interpreterName, $interpreterContext->getOutput());
    			var_dump($interpreterContext->key());
    			$interpreterContext->clearOutput();
    		}
    	}
    }
}