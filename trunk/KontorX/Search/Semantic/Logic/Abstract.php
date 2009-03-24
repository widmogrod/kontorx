<?php
/**
 * KontorX_Search_Semantic
 */
require_once 'KontorX/Search/Semantic.php';
/**
 * @see KontorX_Search_Semantic_Logic_Interface
 */
require_once 'KontorX/Search/Semantic/Logic/Interface.php';

/**
 * @author gabriel
 */
abstract class KontorX_Search_Semantic_Logic_Abstract implements KontorX_Search_Semantic_Logic_Interface {
	/**
	 * @param Zend_Config|array $options
	 * @return void
	 */
	public function __construct($options = null) {
		if (is_array($options)) {
            $this->setOptions($options);
        } elseif ($options instanceof Zend_Config) {
            $this->setConfig($options);
        }
	}
	
	/**
     * @param Zend_Config $config
     * @return void
     */
    public function setConfig(Zend_Config $config) {
        $this->setOptions($config->toArray());
    }

    /**
     * @param array $options
     * @return void
     */
    public function setOptions(array $options) {
    	if (isset($options['interpreters'])) {
    		$this->setInterpreters((array) $options['interpreters']);
    		unset($options['interpreters']);
    	}
    }

	/**
	 * @var array
	 */
	protected $_interpreter = array();

	/**
	 * Zaadaptowanie możliwości dodawania iteratorów i logik, umożliwia
	 * tworzenie zagnieżdzonej stróktóry..
	 * 
	 * @todo zastanowić się czy nie ustawić (set|get)PluginLoader z opcji
	 * 		 a nie jako static @see KontorX_Search_Semantic 
	 * 
     * @param array $interpreters
     * @return void
     */
    public function setInterpreters(array $interpreters) {
    	$this->cleanInterpreters();
    	$this->addInterpreters($interpreters);
    }
	
	/**
     * @param array $interpreters
     * @return void
     */
    public function addInterpreters(array $interpreters) {
    	foreach ($interpreters as $name => $options) {
    		// Typ domyślny: interpreter
    		$type = KontorX_Search_Semantic::INTERPRETER;

    		// Przygotowanie podstawowych danych
    		if (is_string($options)) {
    			$interpreter = $options;
    			$options = array();
    		} else
    		if(is_array($options)) {
    			if (isset($options['interpreter'])) {
    				$interpreter = $options['interpreter'];
    				unset($options['interpreter']);
    			}
    			if (isset($options['name'])) {
    				$name = (string) $options['name'];
    				unset($options['name']);
    			}
    			// sprawdz typ: czy interpreter, czy logic
	    		if (isset($options['type'])) {
	    			$type = (string) $options['type'];
	    			unset($options['name']);
	    		}
    			if (isset($options['options'])) {
    				$options = (array) $options['options'];
    			}
    		}

    		// Tworzenie obiektu
	    	if (is_string($interpreter)) {
	    		$className = KontorX_Search_Semantic::getPluginLoader($type)->load($interpreter);
	    		$interpreter = new $className($options);
	    	} elseif (!$interpreter instanceof KontorX_Search_Semantic_Interpreter_Interface) {
	    		require_once 'KontorX/Search/Semantic/Exception.php';
				throw new KontorX_Search_Semantic_Exception("No instance of 'KontorX_Search_Semantic_Interpreter_Interface'");
	    	}

    		$this->addInterpreter($interpreter, $name);
    	}
    }

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
     * @return void
     */
    public function cleanInterpreters() {
    	$this->_interpreter = array();
    }
}