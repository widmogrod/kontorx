<?php
/**
 * Bardzo przyjemny dodatek, który przekierowywuje akcję
 * dopuki nie będzie spełniony warunek
 * 
 * @author $Author$
 */
class KontorX_Controller_Action_Helper_RedirectUntil extends Zend_Controller_Action_Helper_Redirector
{
	/**
	 * @var bool
	 */
	protected $_condition;
	
	/**
	 * @param bool $flag
	 * @return void
	 */
	public function setCondition($flag)
	{
		$this->_condition = (bool) $flag;
	}
	
	/**
     * Set redirect in response object
     *
     * @return void
     */
    protected function _redirect($url)
    {
    	$condition = $this->_condition;

    	// zerowanie warunku
    	$this->_condition = null;

    	if (true === $condition)
    	{
    		// nie może być exit
    		if (true === $this->_exit)
    		{
    			$this->setExit(false);
    		}

    		// warunek spełniony wtedy przekierowanie
    		// nie jest potrzebne..
    		return;
    	}
    	
    	parent::_redirect($url);
    }
	
	/**
     * direct(): Perform helper when called as
     * $this->_helper->redirector($action, $controller, $module, $params)
     *
     * @param  bool   $condition
     * @param  string $action
     * @param  string $controller
     * @param  string $module
     * @param  array  $params
     * @return void
     */
    public function direct($condition, $action, $controller = null, $module = null, array $params = array())
    {
    	$this->setCondition($condition);
        $this->gotoSimple($action, $controller, $module, $params);
    }
    
	/**
     * Overloading
     *
     * If method will contain "until" keyword
     * then first argument pass to method 
     * will be a confition
     * 
     * gotoUntil()
     * 
     *
     * @param  string $method
     * @param  array $args
     * @return mixed
     * @throws Zend_Controller_Action_Exception for invalid methods
     */
    public function __call($method, $args)
    {
        if (false !== strstr($method,'Until'))
        {
        	// shift condition
        	$condition = array_shift($args);
        	$this->setCondition($condition);

        	$method = str_replace('Until','',$method);
        }

        if (method_exists($this, $method))
        {
        	return call_user_func_array(array($this, $method), $args);
        }

        $method = strtolower($method);
        if ('goto' == $method) {
            return call_user_func_array(array($this, 'gotoSimple'), $args);
        }
        if ('setgoto' == $method) {
            return call_user_func_array(array($this, 'setGotoSimple'), $args);
        }
        if ('gotoandexit' == $method) {
            return call_user_func_array(array($this, 'gotoSimpleAndExit'), $args);
        }

        require_once 'Zend/Controller/Action/Exception.php';
        throw new Zend_Controller_Action_Exception(sprintf('Invalid method "%s" called on redirector', $method));
    }
}