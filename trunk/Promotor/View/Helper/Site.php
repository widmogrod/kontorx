<?php
class Promotor_View_Helper_Site extends Zend_View_Helper_Abstract {
	
	const STATIC_REGISTRY_NAME = 'Promotor_View_Helper_Site';

	/**
	 * @return Promotor_View_Helper_Site
	 */
	public function site() {
		return $this;
	}

	/**
	 * @var Site_Model_Site
	 */
	protected $_model;

	/**
	 * @return Site_Model_Site
	 */
	public function getModel() {
		if (null === $this->_model) {
			$this->_model = new Site_Model_Site();
		}
		return $this->_model;
	}
	
	/**
	 * @var Zend_Loader_PluginLoader
	 */
	protected $_pluginLoader;

	/**
	 * @param Zend_Loader_PluginLoader $loader
	 */
	public function setPluginLoader(Zend_Loader_PluginLoader $loader) {
		$this->_pluginLoader = $loader;
	}

	/**
	 * @return Zend_Loader_PluginLoader
	 */
	public function getPluginLoader() {
		if (null === $this->_pluginLoader) {
			$this->_pluginLoader = new Zend_Loader_PluginLoader(
				array('Promotor_View_Helper_Site' => 'Promotor/View/Helper/Site'),
				self::STATIC_REGISTRY_NAME
			);
		}
		return $this->_pluginLoader;
	}

	/**
	 * @var array
	 */
	protected $_plugin = array();
	
	/**
	 * @param string $name
	 * @param array $options
	 * @return Promotor_View_Helper_Site_Abstract
	 */
	public function get($name, array $options = null) {
		if (!isset($this->_plugin[$name])) {
			$class = $this->getPluginLoader()->load($name);

			$this->_plugin[$name] = new $class($this);
		}
		
		// patch na array 1st param
		if (count($options) == 1) {
			$options = (array) $options[0];
		}

		if (null !== $options) {
			$this->_plugin[$name]->setOptions($options);
		}
		
		return $this->_plugin[$name];
	}

	/**
	 * @param string $name
	 * @param array $params
	 * @return Site_Model_Site
	 */
	public function __call($name, array $params = null) {
		return $this->get($name, $params);
	}
	
	/**
	 * @param string $name
	 * @param array $params
	 */
	public function __get($name) {
		return $this->get($name);
	}
}