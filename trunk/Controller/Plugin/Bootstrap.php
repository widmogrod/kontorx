<?php
require_once 'Zend/Controller/Plugin/Abstract.php';
class KontorX_Controller_Plugin_Bootstrap extends Zend_Controller_Plugin_Abstract {

    public function __construct(Zend_Config $config = null) {
        if (null !== $config) {
            $this->setConfig($config);
        }
    }

    private $_config = null;

    public function setConfig(Zend_Config $config) {
        $this->_config = $config;
    }

    public function getConfig() {
        return $this->_config;
    }

//    public function routeStartup(Zend_Controller_Request_Abstract $request)
//    {}
//
//    public function routeShutdown(Zend_Controller_Request_Abstract $request)
//    {}

    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $viewRenderer->initView();
        $viewRenderer->view->addHelperPath('advertising/views/helpers','Advertising_View_Helper_');
    }

//    public function preDispatch(Zend_Controller_Request_Abstract $request) {
//    	
//    }
//
//    public function postDispatch(Zend_Controller_Request_Abstract $request) {
//
//    }

    public function dispatchLoopShutdown() {
        if (class_exists('Advertising_Model_Advertising', false)) {
            $advertising = Advertising_Model_Advertising::getInstance();
            $advertising->updateData();
        }
    }
}