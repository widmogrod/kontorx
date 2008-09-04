<?php
/**
 * KontorX_Config_Controller_Plugin_Action
 * 
 * @category 	KontorX
 * @package 	KontorX_Config_Controller_Plugin
 * @version 	0.1.0
 * @license		GNU GPL
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
 */
class KontorX_Config_Controller_Plugin_Action extends Zend_Controller_Plugin_Abstract {
	/**
	 * @var Zend_Config
	 */
	protected $_config = null;
	
	public function __construct(Zend_Config $config) {
		$this->_config = $config;
	}

	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		$actionName 	= $request->getActionName();
		$controllerName = $request->getControllerName();
		$moduleName 	= $request->getModuleName();
		
		// sprawdzenie czy jest zkonfigurowany chce controllera
		if (!isset($this->_config->{$moduleName})
				OR !isset($this->_config->{$moduleName}->{$controllerName})
					OR !isset($this->_config->{$moduleName}->{$controllerName}->options)) {
			$this->_response->appendBody('<p>brak informacji o cache kontrollera w konfiguracji (: .. ach te rymy</p>');
			return;	
		}

		$options = $this->_config->{$moduleName}->{$controllerName}->options;
		
		// sprawdzenie czy jest specjalizacja konfiguracji cache akcji
		if (isset($this->_config->{$moduleName}->{$controllerName}->{$actionName})
				AND isset($this->_config->{$moduleName}->{$controllerName}->{$actionName}->options)) {
			$options = $this->_config->{$moduleName}->{$controllerName}->{$actionName}->options;
		}
	}
}
?>