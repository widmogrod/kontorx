<?php
/**
 * Plugin_Acl
 *
 * // TODO Dodać możliwośc definiwania jaki modul definiuje jaka akcje noauth noacl
 *
 * @category 	KontorX
 * @package 	KontorX_Acl_Controller_Plugin
 * @version 	0.1.6
 * @license		GNU GPL
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
 */
class KontorX_Acl_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract {
    const WILDCARD = '*';

    protected $_default = array(
        'acl' => array(
            'module'      => self::WILDCARD,
            'controller'  => 'auth',
            'action'      => 'login'
        ),
        'auth' => array(
            'module'      => 'default',
            'controller'  => 'error',
            'action'      => 'privileges'
        )
    );

    public function __construct(Zend_Acl $acl = null) {
        if (null !== $acl) {
            $this->setAcl($acl);
        }
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        // pobieranie roli
        $auth = $this->getAuth();
        $identity = $auth->getIdentity();
        if (null !== $identity || isset($identity->role)) {
            $role = $auth->getIdentity()->role;
        } else {
            $role = $this->getDefaultRole();
        }

        // przygotowanie resource
        $resource = $this->prepareResource(
            $request->getControllerName(),
            $request->getModuleName());

        // sprawdzam prawa dostepu
        $acl = $this->getAcl();
        if ($acl->has($resource)) {
            // czy rola istnieje
            if ($acl->hasRole($role)) {
                $helper = $this->getAcl()->getHelperInstance();
                $helper->setAccess(
                    $acl->isAllowed($role, $resource, $request->getActionName())
                );
            }
        }
    }

    /**
     * Przygotowuje resource
     * @return string $resource
     */
    public function prepareResource($controller, $module) {
        $resource = $module . '_' . $controller;
        $resource = strtolower($resource);
        return $resource;
    }

    /**
     * @var KontorX_Acl
     */
    protected $_acl = null;

    /**
     * Ustawienie @see Zend_Acl
     */
    public function setAcl(Zend_Acl $acl) {
        $this->_acl = $acl;
    }

    /**
     * Zwraca @see KontorX_Acl
     *
     * @return KontorX_Acl
     */
    public function getAcl() {
        if (null === $this->_acl) {
            require_once 'KontorX/Acl.php';
            $this->_acl = KontorX_Acl::getInstance();
        }
        return $this->_acl;
    }

    /**
     * @var Zend_Auth
     */
    protected $_auth = null;

    /**
     * Zwraca @see Zend_Auth
     *
     * @return Zend_Auth
     */
    public function getAuth() {
        if (null === $this->_auth) {
            require_once 'Zend/Auth.php';
            $this->_auth = Zend_Auth::getInstance();
        }
        return $this->_auth;
    }

    /**
     * Ustawienie uchwytu dla braku uprawnień
     *
     * @return KontorX_Acl_Controller_Plugin_Acl
     */
    public function setNoAuthErrorHandler($action, $controller, $module) {
        $this->_default['auth'] = array(
            'module' => (string) $module,
            'controller' => (string) $controller,
            'action' => (string) $action
        );
        return $this;
    }

    /**
     * Ustawienie modułu dla braku uprawnień
     *
     * @return KontorX_Acl_Controller_Plugin_Acl
     */
    public function setNoAuthModule($module) {
        $this->_default['auth']['module'] = (string) $module;
        return $this;
    }

    /**
     * Zwraca nazwe modułu dla braku uprawnień
     *
     * @return string $module
     */
    public function getNoAuthModule() {
        $module = $this->_default['auth']['module'] ;
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

    /**
     * Ustawienie kontrollera dla braku uprawnień
     *
     * @return KontorX_Acl_Controller_Plugin_Acl
     */
    public function setNoAuthController($controller) {
        $this->_default['auth']['controller'] = (string) $controller;
        return $this;
    }

    /**
     * Zwraca nazwe kontrollera dla braku uprawnień
     *
     * @return string $controller
     */
    public function getNoAuthController() {
        $controller = $this->_default['auth']['controller'] ;
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

    /**
     * Ustawienie akcji dla braku uprawnień
     *
     * @return KontorX_Acl_Controller_Plugin_Acl
     */
    public function setNoAuthAction($action) {
        $this->_default['auth']['action'] = (string) $action;
    }

     /**
     * Zwraca nazwe kontrollera dla braku uprawnień
     *
     * @return string $action
     */
    public function getNoAuthAction() {
        return $this->_default['auth']['action'];
    }

    /**
     * Ustwienie uchwytu dla niepoprawnych uprawnień
     *
     * @return KontorX_Acl_Controller_Plugin_Acl
     */
    public function setNoAclErrorHandler($action, $controller, $module) {
        $this->_default['acl'] = array(
            'module' => (string) $module,
            'controller' => (string) $controller,
            'action' => (string) $action
        );
        return $this;
    }

    /**
     * Ustawienie modułu niepoprawnych uprawnień
     *
     * @return KontorX_Acl_Controller_Plugin_Acl
     */
    public function setNoAclModule($module) {
        $this->_default['acl']['module'] = (string) $module;
        return $this;
    }

    /**
     * Zwraca nazwe modułu dla niepoprawnych uprawnień
     *
     * @return string $module
     */
    public function getNoAclModule() {
        $module = $this->_default['acl']['module'];
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

    /**
     * Ustawienie kontrollera niepoprawnych uprawnień
     *
     * @return KontorX_Acl_Controller_Plugin_Acl
     */
    public function setNoAclController($controller) {
        $this->_default['acl']['controller'] = (string) $controller;
        return $this;
    }

    /**
     * Zwraca nazwe kontrollera dla niepoprawnych uprawnień
     *
     * @return string $controller
     */
    public function getNoAclController() {
        $controller = $this->_default['acl']['controller'];
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

    /**
     * Ustawienie akcji niepoprawnych uprawnień
     *
     * @return KontorX_Acl_Controller_Plugin_Acl
     */
    public function setNoAclAction($action) {
        $this->_default['acl']['action'] = (string) $action;
        return $this;
    }

    /**
     * Zwraca nazwe akcji dla niepoprawnych uprawnień
     *
     * @return string $action
     */
    public function getNoAclAction() {
        return $this->_default['acl']['action'];
    }

    /**
     * @var string
     */
    protected $_defaultRole = 'guest';

    /**
     * Ustawia nazwe domyślnej roli
     *
     * @return KontorX_Acl_Controller_Plugin_Acl
     */
    public function setDefaultRole($role) {
        $this->_defaultRole = (string) $role;
        return $this;
    }

    /**
     * Zwraca nazwe domyślnej roli
     *
     * @return string $role
     */
    public function getDefaultRole() {
        return $this->_defaultRole;
    }

    protected $_frontController;

    /**
     * @return Zend_Controller_Front
     */
    public function getFrontController() {
        if (null === $this->_frontController) {
            $this->_frontController = Zend_Controller_Front::getInstance();
        }
        return $this->_frontController;
    }
}