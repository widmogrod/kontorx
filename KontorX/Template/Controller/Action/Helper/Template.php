<?php
class KontorX_Template_Controller_Action_Helper_Template extends Zend_Controller_Action_Helper_Abstract {
	public function postDispatch() {
    	$this->_init();
    }

	public function _init() {
        $action  = $this->getActionController();
        $request = $action->getRequest();
        $template  = $this->getTemplate();

        $actionName = $request->getActionName();

        // setup template
        if (isset($action->skin)) {
            if (is_array($action->skin)) {
                $options = $action->skin;

                // layout name
                if (isset($options['layout'])) {
                    $template->setLayoutName($options['layout']);
                }

                // template name
                if (isset($options['theme'])) {
                    $template->setThemeName($options['theme']);
                }

                // dodatkowa konfiguracja
                if (isset($options['config']) && is_array($options['config'])) {
                    $template->setOptions($options['config']);
                }
            } else
            if (is_string($action->skin)) {
                // template name
                $template->setThemeName($action->skin);
            }
        }
    }

	/**
	 * @var KontorX_Template
	 */
	protected $_template;
	
	public function setTemplate(KontorX_Template $template) {
		$this->_template = $template;
	}
	
	/**
	 * @return KontorX_Template
	 */
	public function getTemplate() {
		if (null === $this->_template) {
			$this->_template = KontorX_Template::getInstance();
		}
		return $this->_template;
	}

	/**
	 * @return KontorX_Template
	 */
	public function direct() {
		return $this->getTemplate();
	}
	
	public function __call($name, $params = array()) {
        $template = $this->getTemplate();
        if(method_exists($template, $name)) {
            return call_user_func_array(array($template,$name), $params);
        }

        require_once 'Zend/Controller/Exception.php';
        throw new Zend_Controller_Exception("Method '$name' not exsists");
    }
}