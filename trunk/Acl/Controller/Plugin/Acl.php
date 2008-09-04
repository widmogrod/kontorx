<?php
/**
 * Plugin_Acl
 * 
 * // TODO Dodać możliwośc definiwania jaki modul definiuje jaka akcje noauth noacl
 * 
 * @category 	KontorX
 * @package 	KontorX_Acl_Controller_Plugin
 * @version 	0.1.4
 * @license		GNU GPL
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
 */
class KontorX_Acl_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract {
	const NO_ACL = 1;
	const NO_AUTH = 2;
	
	const WILDCARD = '*';

	/**
	 * @var Zend_Auth
	 */
	protected $_auth;
    /**
     * @var Zend_Acl
     */
    protected $_acl;
    
    /**
     * @var Zend_Controller_Front
     */
    protected $_frontController = null;

	/**
	 * @var array
	 */
	protected $_noauth = array('module' => self::WILDCARD,
                             'controller' => 'auth',
                             'action' => 'login');

	/**
	 * @var array
	 */
    protected $_noacl = array('module' => 'default',
                            'controller' => 'error',
                            'action' => 'privileges');

	/**
	 * @var string
	 */
	protected $_defaultRole = 'guest';
	
	/**
	 * @var string
	 */
	protected $_defaultResource = null;
    
	public function __construct(Zend_Acl $acl) {
        $this->_acl = $acl;
        $this->_auth = Zend_Auth::getInstance();
        $this->_frontController = Zend_Controller_Front::getInstance();
	}
	
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		if ($hasIdentity = $this->_auth->hasIdentity()) {
			$role = $this->_auth->getIdentity()->role;
		} else {
			$role = $this->getDefaultRole();
		}

		$action 	= $request->getActionName();
		$action		= strtolower($action);
		$controller = $request->getControllerName();
		$controller	= strtolower($controller);
		$module 	= $request->getModuleName();
		$module		= strtolower($module);

		$resource = $module . '_' . $controller;
		if (!$this->_acl->has($resource)) {
			// nie wprowadzam  tego, poniewaz nie przekazuje informacji o tym
			// czy strona istnieje czy tez nie ..
//			$resource = $this->getDefaultResource();
			return;
		}

		if (!$this->_acl->hasRole($role)) {
			$error->type = '';
			return;
		}

		if ($this->_acl->isAllowed($role, $resource, $action)) {
			return;
		}

//		$error = new ArrayObject(array());
		if ($hasIdentity){
//			$error->type = self::NO_ACL;
			$errorModule		= $this->getNoAclModule();
			$errorController	= $this->getNoAclController();
			$errorAction		= $this->getNoAclAction();
		} else {
//			$error->type = self::NO_AUTH;
			$errorModule		= $this->getNoAuthModule();
			$errorController	= $this->getNoAuthController();
			$errorAction		= $this->getNoAuthAction();
		}

//		$request->setParam('acl_handler', $error)
        $request->setModuleName($errorModule)
				->setControllerName($errorController)
				->setActionName($errorAction)
				->setDispatched(false);
	}

	public function setDefaultRole($role) {
		$this->_defaultRole = (string) $role;
	}

	public function getDefaultRole() {
		return $this->_defaultRole;
	}

	public function setDefaultResource($resource) {
		$this->_defaultResource = (string) $resource;
	}

	public function getDefaultResource() {
		return $this->_defaultResource;
	}
	
	public function setNoAclErrorHandler($action, $controller, $module) {
		$this->_noacl = array(
			'module' => strtolower($module),
			'controller' => strtolower($controller),
			'action' => strtolower($action)
		);
	}

	public function setNoAuthErrorHandler($action, $controller, $module) {
		$this->_noauth = array(
			'module' => strtolower($module),
			'controller' => strtolower($controller),
			'action' => strtolower($action)
		);
	}

	public function setNoAuthModule($module) {
		$this->_noauth['module'] = (string) $module;
	}

	public function getNoAuthModule() {
		$module = $this->_noauth['module'] ;
		if ($module === self::WILDCARD) {
			$front = $this->getFrontController();
			$request = $front->getRequest();
		
			$module = $request->getModuleName();
			if (null == $module) {
				$module = $front->getDefaultModule();
			}
		}
		return $module;
	}

	public function setNoAuthController($controller) {
		$this->_noauth['controller'] = (string) $controller;
	}
	
	public function getNoAuthController() {
		$controller = $this->_noauth['controller'] ;
		if ($controller === self::WILDCARD) {
			$front = $this->getFrontController();
			$request = $front->getRequest();

			$controller = $request->getControllerName();
			if (null == $controller) {
				$controller = $front->getDefaultControllerName();
			}
		}
		return $controller;
	}

	public function setNoAuthAction($action) {
		$this->_noauth['action'] = (string) $action;
	}

	public function getNoAuthAction() {
		return $this->_noauth['action'];
	}

	public function setNoAclModule($module) {
		$this->_noAcl['module'] = (string) $module;
	}

	public function getNoAclModule() {
		$module = $this->_noacl['module'] ;
		if ($module === self::WILDCARD) {
			$front = $this->getFrontController();
			$request = $front->getRequest();
		
			$module = $request->getModuleName();
			if (null == $module) {
				$module = $front->getDefaultModule();
			}
		}
		return $module;
	}

	public function setNoAclController($controller) {
		$this->_noacl['controller'] = (string) $controller;
	}

	public function getNoAclController() {
		$controller = $this->_noacl['controller'] ;
		if ($controller === self::WILDCARD) {
			$front = $this->getFrontController();
			$request = $front->getRequest();

			$controller = $request->getControllerName();
			if (null == $controller) {
				$controller = $front->getDefaultControllerName();
			}
		}
		return $controller;
	}

	public function setNoAclAction($action) {
		$this->_noacl['action'] = (string) $action;
	}

	public function getNoAclAction() {
		return $this->_noacl['action'];
	}

	/**
	 * @return Zend_Controller_Front
	 */
	protected function getFrontController() {
		return $this->_frontController;
	}
}
?>