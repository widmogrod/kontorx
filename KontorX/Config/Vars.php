<?php
/**
 * Description of Vars
 *
 * @author gabriel
 */
class KontorX_Config_Vars {
    /**
     * @var array|null
     */
    private static $_definedConstants = null;

    /**
     * @param Zend_Config $config
     * @param array $vars
     * @return KontorX_Config_Vars
     */
    public static function decorate(Zend_Config $config, array $vars = null) {
        // inicjowanie stalych
        if (null === self::$_definedConstants) {
            $definedConstants = get_defined_constants(true);
            self::$_definedConstants = (array) $definedConstants['user'];
        }

        return new self($config, $vars);
    }

    /**
     * @param Zend_Config $config
     * @param array $vars
     * @return void
     */
    private function  __construct(Zend_Config $config, array $vars = null) {
        $this->setConfig($config);

        if (null !== $vars) {
            $this->setVars($vars);
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
     * @var Zend_Config
     */
    private $_config = array();

    /**
     * @param array $vars
     */
    public function setConfig(Zend_Config $config) {
        $this->_config = $config;
    }

    /**
     * @return Zend_Config
     * @throws Zend_Config_Exception
     */
    public function getConfig() {
        if (!$this->_config instanceof Zend_Config) {
            $message = "Zend_Config is not set";
            require_once 'Zend/Config/Exception.php';
            throw new Zend_Config_Exception($message);
        }
        return $this->_config;
    }

    /**
     * @return array
     */
    public function toArray() {
        $result = array();
        foreach ($this->getConfig() as $key => $val) {
            if ($val instanceof Zend_Config) {
                $clone = clone $this;
                $clone->setConfig($val);
                $result[$key] = $clone->toArray();
            } else {
                $result[$key] = $this->_getVar($val);
            }
        }
        return $result;
    }

    /**
     * @Override
     */
    public function  __get($name) {
        $config = $this->getConfig();
        $value = $config->get($name);

        if ($value instanceof Zend_Config) {
            $clone = clone $this;
            $clone->setConfig($value);
            $value = $clone;
        }

        return $value;
    }

    /**
     * @return mixed
     */
    public function __call($name, array $params = array()) {
        $config = $this->getConfig();
        if (!method_exists($config, $name)) {
            $message = "Zend_Config method '$name' do not exsists";
            require_once 'Zend/Config/Exception.php';
            throw new Zend_Config_Exception($message);
        }

        return call_user_func_array(array($config, $name), $params);
    }

    /**
     * @return void
     */
    public function __clone() {
        $this->_config = null;
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
