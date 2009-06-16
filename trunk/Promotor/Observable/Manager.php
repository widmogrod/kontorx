<?php
/**
 * @author gabriel
 */
class Promotor_Observable_Manager {

	/**
	 * @var array
	 */
	protected $_list = array();

	/**
	 * @var Promotor_Observable_Manager
	 */
	protected static $_instance;
	
	/**
	 * @var Zend_Config
	 */
	protected static $_config;
	
	/**
	 * @param  Zend_Config|array $options
	 */
	protected function __construct($options = null) {
		if (is_array($options)) {
			$this->setOptions($options);
		} elseif ($options instanceof Zend_Config) {
			$this->setOptions($options->toArray());
		}

		if (null !== self::$_config) {
			$this->setOptions(self::$_config);
		}
	}

	/**
	 * @param  Zend_Config|array $options
	 * @return Promotor_Observable_Manager
	 */
	public static function getInstance($options = null) {
		if (null === self::$_instance) {
			self::$_instance = new self($options);
		}
		return self::$_instance;
	}
	
	/**
	 * @param  Zend_Config $config
	 * @return void
	 */
	public static function setConfig($config) {
		if (is_array($config)) {
			self::$_config = $config;
		} elseif ($config instanceof Zend_Config) {
			self::$_config = $config->toArray();
		}
	}

	/**
	 * @param array $options 
	 */
	public function setOptions(array $options) {
		foreach ($options as $name => $value) {
			$method = 'set' . ucfirst($name);
			if(method_exists($this, $method)) {
				$this->$method($value);
				unset($options[$name]);
			}
		}
	}
	
	/**
	 * @param array $observersList
	 * @return Promotor_Observable_Manager
	 */
	public function setObserversList(array $observersList) {
		$this->clearObserversList();
		foreach ($observersList as $name => $observers) {
			$this->addObserverList($name, $observers);
		}
		return $this;
	}

	/**
	 * @return Promotor_Observable_Manager
	 */
	public function addObserverList($name, $options) {
		$name = $this->_normalizeName($name);

		if (isset($this->_list[$name])) {
			throw new KontorX_Observable_Exception(
				sprintf('List name "%s" already exsists', $name));
		}

		$this->_list[$name] = new Promotor_Observable_List((array) $options);
		return $this;
	}

	/**
	 * @return Promotor_Observable_Manager
	 */
	public function getObserversList($name) {
		$name = $this->_normalizeName($name);
		return (isset($this->_list[$name]))
			? $this->_list[$name] : null;
	}

	/**
	 * @return Promotor_Observable_Manager
	 */
	public function clearObserversList() {
		$this->_list = array();
		return $this;
	}
	
	/**
	 * @param string $name
	 * @return string
	 */
	protected function _normalizeName($name) {
		return strtolower($name);
	}

	public function notify($name) {
		$name = $this->_normalizeName($name);
		if (!isset($this->_list[$name])) {
			throw new KontorX_Observable_Exception(
				sprintf('List name "%s" do not exsists', $name));
		}
		$this->_list[$name]->notify();
	}
}