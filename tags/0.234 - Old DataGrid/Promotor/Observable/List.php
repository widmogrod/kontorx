<?php
require_once 'Promotor/Observable/Interface.php';
class Promotor_Observable_List implements Promotor_Observable_Interface {
	
	public function __construct($options = null) {
		if (is_array($options)) {
			$this->setOptions($options);
		} elseif ($options instanceof Zend_Config) {
			$this->setOptions($options->toArray());
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
	 * @var array
	 */
	protected $_observers = array();

	/**
	 * @param  array $observers
	 * @return Promotor_Observable_List
	 */
	public function setObservers(array $observers) {
		$this->clearObservers();
		$this->_observers = $observers;
		return $this;
	}

	/**
	 * @param  Promotor_Observable_Observer_Abstract $observer
	 * @return Promotor_Observable_List
	 */
	public function addObserver(Promotor_Observable_Observer_Abstract $observer) {
		$this->_observers[] = $observer;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getObservers() {
		return $this->_observers;
	}

	/**
	 * @return Promotor_Observable_List
	 */
	public function clearObservers() {
		$this->_observers = array();
		return $this;
	}
	
	/**
	 * @param string $name
	 * @return string
	 */
	protected function _normalizeName($name) {
		$name = strtolower($name);
	}

	/**
	 * @param Promotor_Observable_Observer_Abstract|string $observer
	 * @return string|array
	 */
	public function getStatus($observer = null) {
		if (null === $observer) {
			$retult = array();
			foreach (self::$_loadedObservers as $name => $observer) {
				$retult[$name] = $observer->getStatus();
			}
			return $retult;
		} elseif ($observer instanceof Promotor_Observable_Observer_Abstract) {
			$observer->getName();
		}

		return isset(self::$_loadedObservers[$observer])
			? self::$_loadedObservers[$observer]->getStatus()
			: null;
	}
	
	/**
	 * @param Promotor_Observable_Observer_Abstract|string $observer
	 * @param bool $withExceptions
	 * @return string|array
	 */
	public function getMessages($observer = null, $withExceptions = true) {
		if (null === $observer) {
			$retult = array();
			foreach (self::$_loadedObservers as $name => $observer) {
				$retult[$name] = $observer->getMessages($withExceptions);
			}
			return $retult;
		} elseif ($observer instanceof Promotor_Observable_Observer_Abstract) {
			$observer->getName();
		}

		return isset(self::$_loadedObservers[$observer])
			? self::$_loadedObservers[$observer]->getMessages($withExceptions)
			: null;
	}

	/**
	 * @param Promotor_Observable_Observer_Abstract|string $observer
	 * @return string|array
	 */
	public function getExceptions($observer = null) {
		if (null === $observer) {
			$retult = array();
			foreach (self::$_loadedObservers as $name => $observer) {
				$retult[$name] = $observer->getExceptions();
			}
			return $retult;
		} elseif ($observer instanceof Promotor_Observable_Observer_Abstract) {
			$observer->getName();
		}

		return isset(self::$_loadedObservers[$observer])
			? self::$_loadedObservers[$observer]->getExceptions()
			: null;
	}
	
	/**
	 * @return bool
	 */
	public function notify() {
		$args = func_get_args();
		array_unshift($args, $this);

		foreach ($this->_observers as $key => $observer) {
			/* @var $observer Promotor_Observable_Observer_Abstract */
			if (is_string($observer)){
				$observer = $this->_getObserver($observer);
			} elseif (is_array($observer)) {
				if (!isset($observer['class'])) {
					throw new KontorX_Observable_Exception(
						'Observer options attribute "class" do not exsists');
				}
				$observer = $this->_getObserver($observer['class']);
			} elseif(!$observer instanceof Promotor_Observable_Observer_Abstract) {
				throw new KontorX_Observable_Exception(
					'Unknown observer param');
			}

			call_user_func_array(array($observer, 'update'), $args);

			$this->_observers[$key] = $observer;
			$this->_lastObserver 	= $observer;

			if ($observer->getStatus() === Promotor_Observable_Observer_Abstract::FAILURE
					&& true === $this->_stopOnFailure) {
				return false;
			}
		}
		return true;
	}

	/**
	 * @var Promotor_Observable_Observer_Abstract
	 */
	protected $_lastObserver;

	/**
	 * @return Promotor_Observable_Observer_Abstract
	 */
	public function getLastNoticedObserver() {
		return $this->_lastObserver;
	}
	
	/**
	 * @var bool
	 */
	protected $_stopOnFailure = true;

	/**
	 * @param bool $flag
	 * @return Promotor_Observable_Manager
	 */
	public function setStopOnFailure($flag = true) {
		$this->_stopOnFailure = (bool) $flag;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function stopOnFailure() {
		return $this->_stopOnFailure;
	}
	
	/**
	 * @var array
	 */
	protected static $_loadedObservers = array();

	/**
	 * @param string $class
	 * @return Promotor_Observable_Observer_Abstract
	 * @throws KontorX_Observable_Exception
	 */
	protected function _getObserver($class) {
		if (!isset(self::$_loadedObservers[$class])) {
			$instance = new $class();
			if (!$instance instanceof Promotor_Observable_Observer_Abstract) {
				throw new KontorX_Observable_Exception(
					sprintf('Class "%s" is not instance of Promotor_Observable_Observer_Abstract', $class));
			}
			self::$_loadedObservers[$class] = $instance;
		}
		return self::$_loadedObservers[$class];
	}
}