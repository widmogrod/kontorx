<?php
/**
 * Głównym zadaniem pluginu jest zapisanie w sesji stanu skąd 
 * uzytwkonik trafił na stronę produktu. Czy jest to:
 * - spis wszystkich produktów
 * - spis produktów w kategorii
 * - spis produktów oznaczonych etykietą 
 * 
 * Dane są przekazywane do pomocnika widoku @see Promotor_View_Helper_ShopHistory
 * 
 * @author $Author$
 * @version $Id$
 */
class Promotor_Controller_Plugin_ShopHistory extends Zend_Controller_Plugin_Abstract
{
	/**
	 * Przestrzeń nazwy dla sessji
	 * 
	 * @var string
	 */
	const NS = 'Promotor_Controller_Plugin_ShopHistory';
	
	/**
	 * Lista nazw kontrolerów, które maja być zapamiętywane
	 * w historii produktu.
	 * 
	 * @var array
	 */
	protected $_avalibleControllers = array(
		// product - produkt jest nieaktywny ze względu na to że
		// prześjcie w prevNext traciło by sens.. bo przechodziło
		// by się z produktu do porduktu... i wartość która powinna
		// zostać zahowana była by zgubiona
		'category' => Promotor_View_Helper_ShopPrevNext::CATEGORY,
		'producttag' => Promotor_View_Helper_ShopPrevNext::TAG
	);

	/**
	 * @var Zend_Session_Namespace
	 */
	protected $_session;
	
	/**
	 * @return Zend_Session_Namespace
	 */
	public function getSession() 
	{
		if (null === $this->_session)
			$this->_session = new Zend_Session_Namespace(self::NS);

		return $this->_session;
	}

	/**
     * Called after Zend_Controller_Router exits.
     *
     * Called after Zend_Controller_Front exits from the router.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request) 
    {
    	$moduleName = $request->getModuleName();
    	if ('shop' != $moduleName)
    		return;

    	$controllerName = $request->getControllerName();
    	if (array_key_exists($controllerName, $this->_avalibleControllers))
    	{
    		$session = $this->getSession();
    		$session->type = $this->_avalibleControllers[$controllerName];
    		$session->id = $request->getParam('id', $request->getParam('alias'));
    	}
    }
}