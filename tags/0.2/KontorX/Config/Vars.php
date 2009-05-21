<?php
require_once 'Zend/Config.php';

/**
 * Description of Vars
 *
 * @author gabriel
 */
class KontorX_Config_Vars extends Zend_Config {
    /**
     * @var array|null
     */
    protected static $_definedConstants = null;

    /**
     * @param Zend_Config|array $config
     * @param array|bool $vars
     * @return void
     */
    public function __construct($config, array $vars = null) {
	    // inicjowanie stalych
        if (null === self::$_definedConstants) {
            $definedConstants = get_defined_constants(true);
            self::$_definedConstants = (array) $definedConstants['user'];
        }

        if (null !== $vars) {
        	$this->setVars($vars);
        }
        
    	if ($config instanceof Zend_Config) {
			parent::__construct(array(), true);
			$this->merge($config);
        } elseif (is_array($config)) {
        	parent::__construct($config, true);
        } else {
        	require_once 'Zend/Config/Exception.php';
        	throw new Zend_Config_Exception("Decorated config is not 'array' or instance of 'Zend_Config'");
        }
    }

    /**
     * @var array
     */
    private $_vars = array();

    /**
     * @param array $vars
     */
    public function setVars(array $vars) {
        $this->_vars = $vars;
    }

    /**
     * @Override
     */
    public function toArray() {
        $array = array();
        foreach ($this->_data as $key => $value) {
            if ($value instanceof Zend_Config) {
            	$vars = new self($value, $this->_vars);
            	$array[$key] = $vars->toArray();
            } else
            if ($value instanceof KontorX_Config_Vars) {
                $array[$key] = $value->toArray();
            } else {
                $array[$key] = $this->_getVar($value);
            }
        }
        return $array;
    }

    /**
     * @Override
     */
    public function  get($name) {
    	$value  = parent::get($name);
    	if (is_string($value)) {
    		$value = $this->_getVar($value);
    	} else
    	if ($value instanceof Zend_Config) {
    		$value = new self($value, $this->_vars);
    	}
    	return $value;
    }

    /**
     * @param string $content
     * @return string
     */
    protected function _getVar($content) {
        return preg_replace("/{{([a-z0-9_\-\.^}}]+)}}/ie","\$this->_findVar('$1')", (string) $content);
    }

    /**
     * @param string $var
     * @return mixed
     */
    protected function _findVar($var) {
        $var = (string) $var;
        if (isset($this->_vars[$var])) {
            return self::$_definedConstants[$var];
        } elseif (isset(self::$_definedConstants[$var])) {
            return self::$_definedConstants[$var];
        }
        return $var;
    }
}
