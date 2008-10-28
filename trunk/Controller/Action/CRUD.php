<?php
require_once 'KontorX/Controller/Action/CRUD/Abstract.php';

/**
 * Specjalizacja CRUD
 * 
 * @category 	KontorX
 * @package 	KontorX_Controller_Action
 * @version 	0.0.5
 * @license		GNU GPL
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
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

    	$loader = $this->_helper->loader;
    	if (!$loader->hasConfig($fileName, $module)) {
    		return null;
    	}

    	$config = $loader->config($fileName, $module);
    	// sprawdz czy jest sekcja konfiguracyjna
    	if (!isset($config->form)
    			|| !isset($config->form->$action)) {
    		return null;
    	}

    	return $config;
    }
    
	/**
	 * Return config name filename
	 * 
	 * @author gabriel
	 * 
	 * @param $controller
	 * @return unknown_type
	 */
	protected function _getConfigFormFilename($controller) {
    	$controller = strtolower($controller);
    	return "$controller.ini";
    }
}