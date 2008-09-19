<?php
require_once 'Zend/Controller/Action/Helper/Abstract.php';
class KontorX_Controller_Action_Helper_System extends Zend_Controller_Action_Helper_Abstract {

    public function init() {

    }

    /**
     * @return KontorX_Controller_Plugin_System
     */
	public function direct() {
		return $this->getPluginInstance();
	}

	/**
	 * @var KontorX_Controller_Plugin_System
	 */
	protected $_pluginInstance = null;

	/**
	 * @return KontorX_Controller_Plugin_System
	 */
	public function getPluginInstance() {
		if (null === $this->_pluginInstance) {
			$front = $this->getFrontController();
			if (!$front->hasPlugin('KontorX_Controller_Plugin_System')) {
				throw new Zend_Controller_Exception('Plugin `KontorX_Controller_Plugin_System` is no exsists');
			}
			$this->_pluginInstance = $fron->getPlugin('KontorX_Controller_Plugin_System');
		}
		return $this->_pluginInstance;
	}
	
	/**
	 * @return KontorX_Controller_Action_Helper_System
	 */
	public  function language() {
		$this->getPluginInstance()->getLanguage();
		return $this;
	}

	/**
	 * @return KontorX_Controller_Action_Helper_System
	 */
    public function template($template) {
        $this->getPluginInstance()->setTemplate($template);
        return $this;
    }

    /**
	 * @return KontorX_Controller_Action_Helper_System
	 */
    public function layout($layout) {
    	$this->getPluginInstance()->setLayout($layout);
    	return $this;
    }
}