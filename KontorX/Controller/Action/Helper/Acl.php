<?php
/**
 * @author gabriel
 */
class KontorX_Controller_Action_Helper_Acl extends Zend_Controller_Action_Helper_Abstract {

	const DEFAULT_ROLE_ID = 'guest';
	
	public function preDispatch() {
		$this->_init();

		// brak zainicjowanych opcji
		if (!$this->_options) {
			return;
		}
		
		if (!$this->isAllowed()) {
			// forwardowanie
			$request = $this->getRequest();
			if (isset($this->_forwardIfDeny['action'])) {
				$request->setActionName($this->_forwardIfDeny['action']);
			}

			if (isset($this->_forwardIfDeny['controller'])) {
				$request->setControllerName($this->_forwardIfDeny['controller']);

				if (isset($this->_forwardIfDeny['module'])) {
					$request->setModuleName($this->_forwardIfDeny['module']);
				}
			}
			
			if (isset($this->_forwardIfDeny['params'])) {
				$request->setParams((array) $this->_forwardIfDeny['params']);
			}
			$request->setDispatched(false);
		}

		$this->_options = false;
	}
	
	/**
	 * @return void
	 */
	protected function _init() {
		$action = $this->getActionController();
		if (isset($action->acl)) {
			$actionName = $this->getRequest()->getActionName();
			if (isset($action->acl[$actionName])) {
				$this->setOptions($action->acl[$actionName]);
			}
		}
	}
	
	/**
	 * @param string $privilage
	 * @param string $resource
	 * @return bool
	 */
	public function isAllowed($privilage = null, $resource = null) {
		$acl = $this->getAcl();
		$role = $this->getRole();

		if (null === $privilage) {
			$privilage = $this->getPrivilage();
		}
		if (null === $resource) {
			$resource = $this->getResource();
		}

		return $acl->isAllowed($role, $resource, $privilage);
	}

	/**
	 * @var bool
	 */
	protected $_options = false;
	
	/**
	 * @param array $options
	 * @return void
	 */
	public function setOptions($options) {
		$this->_options = true;

		if (is_string($options)) {
			$this->setPrivilage($options);
		} else
		if (is_array($options)) {
			foreach ($options as $key => $value) {
				$method = 'set' . ucfirst($key);
				if (in_array($method, array('setResource','setPrivilage'))) {
					if (method_exists($this, $method)) {
						$this->$method($value);
					}
				}
			}
		}
	}

	/**
	 * @var array
	 */
	protected $_forwardIfDeny = array();

	/**
	 * @param string $action
	 * @param string $controller
	 * @param string $module
	 * @param array $params
	 * @return void
	 */
	public function setForwardIfDeny($action, $controller = null, $module = null, array $params = array()) {
		$this->_forwardIfDeny['action'] = (string) $action;
		if (null !== $controller) {
			$this->_forwardIfDeny['controller'] = (string) $controller;
		}
		if (null !== $module) {
			$this->_forwardIfDeny['module'] = (string) $module;
		}
		if (null !== $params) {
			$this->_forwardIfDeny['params'] = (array) $params;
		}
	}
	
	/**
	 * @var string
	 */
	protected $_privilage;
	
	/**
	 * @param string $privilage
	 * @return string
	 */
	public function setPrivilage($privilage) {
		$this->_privilage = (string) $privilage;
	}
	
	/**
	 * @return string
	 */
	public function getPrivilage() {
		if (null === $this->_privilage) {
			$this->_privilage = $this->getRequest()->getActionName();
		}
		return $this->_privilage;
	}
	
	/**
	 * @var string
	 */
	protected $_resource;
	
	/**
	 * @param string $resource
	 * @return string
	 */
	public function setResource($resource) {
		$this->_resource = (string) $resource;
	}
	
	/**
	 * @return string
	 */
	public function getResource() {
		return $this->_resource;
	}

	/**
	 * @var string
	 */
	protected $_role;
	
	/**
	 * @return string
	 */
	public function getRole() {
		if (null === $this->_role) {
			$auth = Zend_Auth::getInstance();
			if ($auth->hasIdentity()) {
				$identity = $auth->getIdentity();
				$this->_role = @$identity->role;
			}
			if(null == $this->_role) {
				$this->_role = self::DEFAULT_ROLE_ID;
			}
		}
		return $this->_role;
	}
	
	/**
	 * @var Zend_Acl
	 */
	protected $_acl = null;
	
	/**
	 * @return Zend_Acl
	 */
	public function getAcl() {
		if (null === $this->_acl) {
			/* @var $bootstrap Zend_Application_Bootstrap_Bootstrap */
			$bootstrap = $this->getFrontController()->getParam('bootstrap');
			if ($bootstrap->hasResource('Acl')) {
				$this->_acl = $bootstrap->getResource('Acl');
			} else {
				if (Zend_Registry::isRegistered('Zend_Acl')) {
					$this->_acl = Zend_Registry::get('Zend_Acl');			
				} else {
					require_once 'Zend/Controller/Action/Exception.php';
					throw new Zend_Controller_Action_Exception('Acl resource is not set');
				}
			}
		}
		return $this->_acl;
	}

	/**
	 * @return Zend_Acl
	 */
	public function direct() {
		return $this->getAcl();
	}
}