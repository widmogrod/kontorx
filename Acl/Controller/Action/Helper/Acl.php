<?php
require_once 'Zend/Controller/Action/Helper/Abstract.php';

/**
 * KontorX_Acl_Controller_Action_Helper_Acl
 *
 * @category 	KontorX
 * @package 	KontorX_Acl_Controller_Action_Helper_Acl
 * @version 	0.1.5
 * @license		GNU GPL
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
 */
class KontorX_Acl_Controller_Action_Helper_Acl extends Zend_Controller_Action_Helper_Abstract {
    /**
     * @return KontorX_Acl_Controller_Action_Helper_Acl
     */
    public function direct() {
        return $this;
    }

    protected $_isForwarding = false;

    public function init() {
        $controller = $this->getActionController();
        if (!isset($controller->access)) {
            return;
        }

        $access = $controller->access;
        $action = $controller->getRequest()->getActionName();

        if (isset($access[$action]) && is_array($access[$action])) {
            $options = $access[$action];
            // requredAuthorization
            if (in_array('requredAuthorization', $options)) {
                $this->requiredAuthorization($options['requredAuthorization']);
            }
        }
    }

    public function preDispatch() {
        if ($this->_isForwarding) {
            // continue without forwarding
            return;
        }

        $plugin  = $this->getPluginInstance();
        if ($this->isRequiredAuthorization()) {
            $action     = $plugin->getNoAuthAction();
            $controller = $plugin->getNoAuthController();
            $module     = $plugin->getNoAuthModule();
        } else
        if ($this->accessDeny()) {
            $action     = $plugin->getNoAclAction();
            $controller = $plugin->getNoAclController();
            $module     = $plugin->getNoAclModule();
        } else {
            // continue without forwarding
            return;
        }

        $this->_isForwarding = true;
        // forwardowanie
        $this->getActionController()
            ->getRequest()
            ->setActionName($action)
            ->setControllerName($controller)
            ->setModuleName($module)
            ->setDispatched(false);
    }

    protected $_access = true;

    public function setAccess($flag = true) {
        $this->_access = (bool) $flag;
    }

    /**
     * Zwraca boolean czy jest dostep do akcji
     * @return bool
     */
    public function accessAllow() {
        return $this->_access;
    }

    /**
     * Zwracz boolean czy nie ma dostępu do akcji
     * @return bool
     */
    public function accessDeny() {
        return !$this->_access;
    }

    /**
     * @var bool
     */
    protected $_requiredAuthorization = false;

    /**
     * Ustawia flage boolean czy dostęp wymaga autoryzacji!
     *
     * Czy użytkownik powinien być zalogowany poprzez @see Zend_Auth
     *
     * @return KontorX_Acl_Controller_Action_Helper_Acl
     */
    public function requiredAuthorization($flag = true) {
        $this->_requiredAuthorization = (bool) $flag;
        return $this;
    }

    /**
     * Czy jest wymagana autoryzacja i czy jest autoryzowany użytkownik
     * @return bool
     */
    public function isRequiredAuthorization() {
        if ($this->_requiredAuthorization) {
             $auth = $this->getAuth();
             return !$auth->hasIdentity();
        }
        return false;
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
     * Zwraca @see Zend_Acl
     * @return KontorX_Acl
     */
    public function getAclMVCInstance() {
        if (null === $this->_acl) {
            require_once 'KontorX/Acl.php';
            $this->_acl = KontorX_Acl::getInstance();
        }
        return $this->_acl;
    }

    /**
     * @var KontorX_Acl_Controller_Plugin_Acl
     */
    protected $_pluginInstance;

    /**
     * Ustawienie pluginu @see KontorX_Acl_Controller_Plugin_Acl
     * @param KontorX_Acl_Controller_Plugin_Acl $plugin
     */
    public function setPluginInstance(KontorX_Acl_Controller_Plugin_Acl $plugin) {
        $this->_pluginInstance = $plugin;
    }

    /**
     * Zwraca plugin @see KontorX_Acl_Controller_Plugin_Acl
     * @return KontorX_Acl_Controller_Plugin_Acl
     */
    public function getPluginInstance() {
        if (null === $this->_pluginInstance) {
            $acl = $this->getAclMVCInstance();
            $this->_pluginInstance = $acl->getPluginInstance();
        }
        return $this->_pluginInstance;
    }
}