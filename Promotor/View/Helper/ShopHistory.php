<?php
/**
 * Pomocnik widoku, który umożliwia komunikację modelu
 * z pluginem controllera @see Promotor_Controller_Plugin_ShopHistory
 * 
 * Zapewnia zwrócenie nazwy aktualnego kontrolera
 * świadczącego z jakiej strony przyszedł użytkownik do strony produktu
 * 
 * Wykorzystywany powinien być tylko i wyłączeni na stronie produktu.
 *  
 * @author $Author$
 * @version $Id$
 */
class Promotor_View_Helper_ShopHistory extends Zend_View_Helper_Abstract
{
	/**
	 * @var Promotor_Controller_Plugin_ShopHistory
	 */
	protected $_controllerPlugin;

	public function __construct()
	{
		if (null === $this->_controllerPlugin) 
		{
			$front = Zend_Controller_Front::getInstance();

			$plugin = $front->getPlugin('Promotor_Controller_Plugin_ShopHistory');

			if (is_array($plugin))
				$plugin = $plugin[0];
			
			if (!($plugin instanceof Promotor_Controller_Plugin_ShopHistory))
				throw new Exception('Controller plugin "Promotor_Controller_Plugin_ShopHistory" is not loaded!');

			$this->_controllerPlugin = $plugin;
		}
	}

	/**
	 * @return void|Promotor_View_Helper_ShopHistory
	 */
	public function shopHistory() 
	{
		return $this;
	}

	public function __get($name) 
	{
		$session = $this->_controllerPlugin->getSession();
		return isset($session->$name)
			? $session->$name
			: null;
	}

	public function toArray() 
	{
		$session = $this->_controllerPlugin->getSession();
		return $session->getIterator()->getArrayCopy();
	}
}