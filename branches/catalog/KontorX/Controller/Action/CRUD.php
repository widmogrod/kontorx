<?php
require_once 'KontorX/Controller/Action/CRUD/Abstract.php';

/**
 * Specjalizacja CRUD
 *
 * @category 	KontorX
 * @package 	KontorX_Controller_Action
 * @version 	0.0.5
 * @license	GNU GPL
 * @author 	Marcin `widmogror` Habryn, widmogrod@gmail.com
 */
abstract class KontorX_Controller_Action_CRUD extends KontorX_Controller_Action_CRUD_Abstract {

    /**
     * @Overwrite
     */
    protected function _addGetFormOptions() {
        $config = $this->_getConfigForm();
        if (!$config instanceof Zend_Config) {
            return null;
        }

        $action = $this->getRequest()->getActionName();
        return $config->form->$action;
    }

    /**
     * @Overwrite
     */
    protected function _addGetFormDbTableIgnoreColumns() {
        $config = $this->_getConfigForm();
        if (!$config instanceof Zend_Config) {
            return array();
        }

        $action = $this->getRequest()->getActionName();
        return isset($config->form->$action->ignore)
        ? $config->form->$action->ignore->toArray()
        : array();
    }

    /**
     * @Overwrite
     */
    protected function _editGetFormOptions() {
        return $this->_addGetFormOptions();
    }

    /**
     * @Overwrite
     */
    protected function _editGetFormDbTableIgnoreColumns() {
        return $this->_addGetFormDbTableIgnoreColumns();
    }

    /**
     * Zwraca obiekt konfiguracji @see Zend_Config
     *
     * @return Zend_Config
     */
    protected function _getConfigForm() {
        $request = $this->getRequest();

        $action 	= $request->getActionName();
        $controller = $request->getControllerName();
        $module 	= $request->getModuleName();

        $fileName = $this->_getConfigFormFilename($controller);

        try {
        	$config = $this->_helper->config($fileName);
        } catch (Zend_Config_Exception $e) {
        	return null;
        }

        // sprawdz czy jest sekcja konfiguracyjna
        if (!isset($config->form)
            || !isset($config->form->$action)) {
            return null;
        }

        return $config;
    }

    protected $_configFilenameExtension = 'ini';

    /**
     * Return config name filename
     *
     * @author gabriel
     *
     * @param string $controller
     * @return string
     */
    protected function _getConfigFormFilename($controller) {
        return strtolower("$controller.$this->_configFilenameExtension");
    }
}