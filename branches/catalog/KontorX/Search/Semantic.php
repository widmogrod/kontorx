<?php
/**
 * KontorX_Search_Semantic
 *
 * @author gabriel
 */
class KontorX_Search_Semantic {
	
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
        /*foreach ($options as $name => $value) {
            $method = 'set'.ucfirst($name);
            if (method_exists($this, $method)) {
                 call_user_func_array(array($this, $method), (array) $value);
            }
        }*/
    }
	
    /**
     * @var array 
     */
    private $_interpreter = array();
    
    /**
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
    	foreach ($interpreters as $options) {
    		$name = null;
    		// Typ domyślny: interpreter
    		$type = self::INTERPRETER;

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
	    			unset($options['type']);
	    		}
    			if (isset($options['options'])) {
    				$options = (array) $options['options'];
    			}
    		}

    		// Tworzenie obiektu
	    	if (is_string($interpreter)) {
	    		$className = self::getPluginLoader($type)->load($interpreter);
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
    public function addInterpreter(KontorX_Search_Semantic_Interpreter_Interface $interpreter, $name = null) {
    	if (null === $name) {
    		$this->_interpreter[] = $interpreter;
    	} else {
    		$this->_interpreter[(string)$name] = $interpreter;
    	}
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
    		$interpreterContext->clearOutput();
    		$interpreterContext->rewind();

    		$r = $interpreterInstance->interpret($interpreterContext);
    		if (true === $r) {
    			// przekazanie głównemu kontekstowi, output'a dla interpretatora
    			$context->addOutput($interpreterName, $interpreterContext->getOutput());
    		}
    	}
    	
    	$context->setInput($interpreterContext->getInput());
    }

	const INTERPRETER = 'interpreter';

    const LOGIC = 'logic';

    /**
     * @var array
     */
    private static $_pluginLoader = array();

    /**
     * Set @see Zend_Loader_PluginLoader
     * @param Zend_Loader_PluginLoader $loader
     * @param string $type
     */
    public static function setPluginLoader(Zend_Loader_PluginLoader $loader, $type) {
        switch ($type) {
            case self::INTERPRETER:
            case self::LOGIC:
                self::$_pluginLoader[$type] = $loader;
                break;
        default:
            require_once 'KontorX/Search/Semantic/Exception.php';
            throw new KontorX_Search_Semantic_Exception(sprintf('Invalid type "%s" provided to getPluginLoader()', $type));
        }
    }

    /**
     * Get @see Zend_Loader_PluginLoader
     *
     * @param string $type
     * @return Zend_Loader_PluginLoader
     */
    public static function getPluginLoader($type) {
        if (!isset(self::$_pluginLoader[$type])) {
            switch ($type) {
                case self::INTERPRETER:
                    $prefixSegment = 'Search_Semantic_Interpreter';
                    $pathSegment   = 'Search/Semantic/Interpreter';
                    break;
                case self::LOGIC:
                    $prefixSegment = 'Search_Semantic_Logic';
                    $pathSegment   = 'Search/Semantic/Logic';
                    break;
                default:
                    require_once 'KontorX/Search/Semantic/Exception.php';
                    throw new KontorX_Search_Semantic_Exception(sprintf('Invalid type "%s" provided to getPluginLoader()', $type));
            }

            require_once 'Zend/Loader/PluginLoader.php';
            self::$_pluginLoader[$type] = new Zend_Loader_PluginLoader(array(
                "KontorX_$prefixSegment" => "KontorX/$pathSegment"
            ));
        }

        return self::$_pluginLoader[$type];
    }
}