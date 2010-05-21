<?php
require_once 'Zend/Controller/Action/Helper/Json.php';
class KontorX_Controller_Action_Helper_Json extends Zend_Controller_Action_Helper_Json
{
	/**
	 * @var Zend_Filter_Alpha
	 */
	protected $_filter;

	/**
	 * @param string $callback
	 * @return string
	 */
	protected function _filterCallback($callback)
	{
		if (null === $this->_filter)
		{
			require_once 'Zend/Filter/Alpha.php';
			$this->_filter = new Zend_Filter_Alpha();
		}
		
		$callback = trim($callback);
		$callback = $this->_filter->filter($callback);

		if (empty($callback))
		{
			$callback = null;
		}
		
		return $callback;
	}

	/**
	 * @var string
	 */
	protected $_callback;

	/**
	 * @param string $callback
	 * @return KontorX_Controller_Action_Helper_Json
	 */
	public function setCallback($callback) 
	{
		$this->_callback = $this->_filterCallback($callback);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCallback()
	{
		return $this->_callback;
	}

	/**
	 * @return KontorX_Controller_Action_Helper_Json
	 */
	public function clearCallback()
	{
		$this->_callback = null;
		return $this;
	}

	/**
     * Encode JSON response and immediately send
     *
     * @param  mixed   $data
     * @param  boolean|array $keepLayouts
     * NOTE:   if boolean, establish $keepLayouts to true|false
     *         if array, admit params for Zend_Json::encode as enableJsonExprFinder=>true|false
     *         if $keepLayouts and parmas for Zend_Json::encode are required
     *         then, the array can contains a 'keepLayout'=>true|false
     *         that will not be passed to Zend_Json::encode method but will be passed
     *         to Zend_View_Helper_Json
     * @return string|void
     */
    public function sendJson($data, $keepLayouts = false)
    {
        $data = $this->encodeJson($data, $keepLayouts);
        
        /**
         *  Dodawanie dekoratora
         */
        if (null !== $this->_callback)
        {
        	$data = $this->_callback . '('.$data.')';
        	$this->_callback = null;
        }
        
        $response = $this->getResponse();
        $response->setBody($data);

        if (!$this->suppressExit) {
            $response->sendResponse();
            exit;
        }

        return $data;
    }
    
	/**
     * Strategy pattern: call helper as helper broker method
     *
     * Allows encoding JSON. If $sendNow is true, immediately sends JSON
     * response.
     *
     * @param  mixed   $data
     * @param  string  $callback
     * @param  boolean $sendNow
     * @param  boolean $keepLayouts
     * @return string|void
     */
    public function direct($data, $callback = null, $sendNow = true, $keepLayouts = false)
    {
    	if (null !== $callback)
    	{
    		$this->setCallback($callback);
    	}

        if ($sendNow) 
        {
            return $this->sendJson($data, $keepLayouts);
        }
        return $this->encodeJson($data, $keepLayouts);
    }
}