<?php
class KontorX_Controller_Plugin_Debug extends Zend_Controller_Plugin_Abstract {

	/**
	 * @var KontorX_Controller_Plugin_Debug
	 */
	private static $_instance;

    /**
     * @return void
     */
    public function __construct() {
    	if (null !== self::$_instance) {
    		throw new Zend_Controller_Exception('Only one instance of plugin is allowed');
    	}
    }

    public static function getInstance() {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Loguje tylko gdy plugin jest zainicjonowany!
     */
    public static function log($message) {
        if (null !== self::$_instance) {
            self::$_instance->_log($message);
        }
    }

    /**
     * @var integer
     */
    private $_time = null;

    /**
     * @var Zend_Log
     */
    private $log = null;

    /**
     * @return Zend_Log
     */
    private function _getLog() {
        if (null === $this->log) {
            $this->log = new Zend_Log();
            $this->log->addWriter(new Zend_Log_Writer_Firebug());
        }
        return $this->log;
    }

    /**
     * @param string $message
     * @return void
     */
    private function _log($message) {
        $this->_getLog()->log($message, Zend_Log::DEBUG);
    }

    public function routeStartup(Zend_Controller_Request_Abstract $request) {
        $this->_time = microtime(true);
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
        $this->_log('time:' . number_format(microtime(true)-$this->_time, 4));
    }
}