<?php
require_once 'Zend/Controller/Plugin/Abstract.php';
class KontorX_Controller_Plugin_Debug extends Zend_Controller_Plugin_Abstract {

    /**
     * @var Zend_Log
     */
    private $_log = null;

    /**
     * @return Zend_Log
     */
    private function _getLog() {
        if (null === $this->_log) {
            $this->_log = new Zend_Log();
            $this->_log->addWriter(new Zend_Log_Writer_Firebug());
        }
        return $this->_log;
    }

    /**
     * @param string $message
     * @return void
     */
    private function _log($message) {
        $this->_getLog()->log($message, Zend_Log::DEBUG);
    }

    public function routeStartup(Zend_Controller_Request_Abstract $request) {
        $this->_log('routeStartup');
        $this->_log($request->getParams());
    }

    public function routeShutdown(Zend_Controller_Request_Abstract $request) {
        $this->_log('routeShutdown');
        $this->_log($request->getParams());
    }

    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
        $this->_log('dispatchLoopStartup');
        $this->_log($request->getParams());
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $this->_log('preDispatch');
        $this->_log($request->getParams());
    }

    public function postDispatch(Zend_Controller_Request_Abstract $request) {
        $this->_log('postDispatch');
        $this->_log($request->getParams());
    }

    public function dispatchLoopShutdown() {
        $this->_log('dispatchLoopShutdown');
    }
}