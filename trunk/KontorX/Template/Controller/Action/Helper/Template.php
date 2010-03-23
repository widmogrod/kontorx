<?php
class KontorX_Template_Controller_Action_Helper_Template extends Zend_Controller_Action_Helper_Abstract {
	
	/**
	 * @param KontorX_Template $template
	 * @return void
	 */
	public function __construct(KontorX_Template $template) {
		$this->setTemplate($template);
	}
	
	/**
	 * @var KontorX_Template
	 */
	protected $_template;
	
	/**
	 * @param KontorX_Template $template
	 * @return void
	 */
	public function setTemplate(KontorX_Template $template) {
		$this->_template = $template;
	}
	
	/**
	 * @return KontorX_Template
	 */
	public function getTemplate() {
		return $this->_template;
	}
	
	public function preDispatch() {
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
                
            	if (array_key_exists($actionName, $options)) {
                    $options = $action->skin[$actionName];
                }

                // layout name
                if (isset($options['layout'])) {
                    $template->setLayoutName($options['layout']);
                }

                // template name
            	if (isset($options['template'])) {
                    $template->setTemplateName($options['template']);
                }
                
            	// template name
            	if (isset($options['style'])) {
                    $template->setStyleName($options['style']);
                }

                if (array_key_exists('disableTemplate', (array) $options)) {
                	$template->disableTemplate($options['disableTemplate']);
                }
                
            	// Czy widok modułu może być personalizowany
            	// w template
                if (array_key_exists('personalized', (array) $options)
                		&& true === $options['personalized'])
                {
                    $templatePaths = $template->getTemplatePaths(true);
                    $styleName 	   = $template->getStyleName();
                    $moduleName    = $request->getModuleName();

                    foreach($templatePaths as $templatePath)
                    {
                    	$scriptPath = sprintf('%s/styles/%s/scripts/%s', rtrim($templatePath,'/'), $styleName, $moduleName);
                    	$action->view->addScriptPath($scriptPath);	
                    }
                }

                // dodatkowa konfiguracja
                if (isset($options['config']) && is_array($options['config'])) {
                	if(isset($options['config']['filename'])) {
                		$template->setStyleConfigFilename($options['config']['filename']);
                	}
                	/*else {
                		$template->setOptions($options['config']);
                	}*/
                }
            } else
            if (is_string($action->skin)) {
                // template name
                $template->setTemplateName($action->skin);
            }
        }
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