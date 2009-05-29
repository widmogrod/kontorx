<?php
class KontorX_Controller_Action_Helper_System extends Zend_Controller_Action_Helper_Abstract {

    public function _init() {
        $action  = $this->getActionController();
        $request = $action->getRequest();
        $plugin  = $this->getPluginInstance();

        $actionName = $request->getActionName();

        // setup template
        if (isset($action->skin)) {
            if (is_array($action->skin)) {
                $options = $action->skin;

                // sprawdz czy nazwa akcji nie jest zastrzezona
                // i czy istnieje jako oddzielna konfiguracja skórki dla akcji
                if (!in_array($actionName, array('layout', 'dynamic', 'template'))
                    && array_key_exists($actionName, $options)) {
                    $options = $action->skin[$actionName];
                }

                // layout name
                if (isset($options['layout'])) {
                    $plugin->setLayoutName($options['layout']);
                } else
                // dynamic layout name
                if (isset($options['dynamic'])) {
                    $dynamicName = $request->getControllerName() . '_' . $request->getActionName();
                    $plugin->setLayoutName($dynamicName);
                }

                // template name
                if (isset($options['template'])) {
                    $plugin->setTemplateName($options['template']);
                }

                // czy zablokować zmiane layout z poziomu konfiguracji szablonu
                if (isset($options['lock'])) {
                    $plugin->lockLayoutName((bool) $options['lock']);
                }

                // dodatkowa konfiguracja
                if (isset($options['config']) && is_array($options['config'])) {
                    $plugin->setConfig(
                        array('config' => $options['config']),
                        KontorX_Controller_Plugin_System::TEMPLATE);
                }
            } else
            if (is_string($action->skin)) {
                // template name
                $this->getPluginInstance()->setTemplateName($action->skin);
            }
        }
    }

    public function postDispatch() {
    	$this->_init();
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
            $this->_pluginInstance = $front->getPlugin('KontorX_Controller_Plugin_System');
        }
        return $this->_pluginInstance;
    }

    /**
     * Ustawia instancje obiektu
     *
     * @param KontorX_Controller_Plugin_System $plugin
     */
    public function setPluginInstance(KontorX_Controller_Plugin_System $plugin) {
        $this->_pluginInstance = $plugin;
    }

    /**
     * @return string
     */
    public  function language() {
        return $this->getPluginInstance()->getLanguage();
    }

    /**
     * @return KontorX_Controller_Action_Helper_System
     */
    public function template($template) {
        $this->getPluginInstance()->setTemplateName($template);
        return $this;
    }

    /**
     * @return KontorX_Controller_Action_Helper_System
     */
    public function layout($layout) {
        $this->getPluginInstance()->setLayoutName($layout);
        return $this;
    }

    public function __call($name, $params = array()) {
        $plugin = $this->getPluginInstance();
        if(method_exists($plugin, $name)) {
            return call_user_func_array(array($plugin,$name), $params);
        }

        require_once 'Zend/Controller/Exception.php';
        throw new Zend_Controller_Exception("Method '$name' not exsists");
    }
}