<?php
class KontorX_Form_Element_Link extends Zend_Form_Element_Xhtml 
{
	public function init() 
	{
		$this->addPrefixPath(
			'KontorX_Form_Decorator',
			'KontorX/Form/Decorator',
			self::DECORATOR
		);
		$this->addPrefixPath(
			'KontorX_Validate',
			'KontorX/Validate',
			self::VALIDATE
		);

		$this->setIsArray(false);
		$this->setIgnore(true);
	}

	public function loadDefaultDecorators() 
	{
		if ($this->loadDefaultDecoratorsIsDisabled()) {
            return;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('Link')
                ->addDecorator('Errors')
                ->addDecorator('Description', array('tag' => 'p', 'class' => 'description'))
                ->addDecorator('HtmlTag', array('tag' => 'dd',
                                                'id'  => $this->getName() . '-element'))
                ->addDecorator('Label', array('tag' => 'dt'));
        }
	}

	/**
	 * @var string
	 */
	protected $_action;
	
	/**
	 * @param string $action
	 * @return KontorX_Form_Element_Link
	 */
	public function setAction($action) {
		$this->_action = (string) $action;
		return $this;
	}
	
	/**
	 * @var string
	 */
	protected $_controller;
	
	/**
	 * @param string $controller
	 * @return KontorX_Form_Element_Link
	 */
	public function setController($controller) {
		$this->_controller = (string) $controller;
		return $this;
	}
	
	/**
	 * @var string
	 */
	protected $_module;
	
	/**
	 * @param string $module
	 * @return KontorX_Form_Element_Link
	 */
	public function setModule($module) {
		$this->_module = (string) $module;
		return $this;
	}

	/**
	 * @var array
	 */
	protected $_params = array();
	
	/**
	 * @param array $params
	 */
	public function setParams(array $params) {
		$this->_params = $params;
	}
	
	/**
	 * @return array
	 */
	public function getUrlOptions() {
		$params = array();
		
		if (null !== $this->_action)
			$params['action'] = $this->_action;
			
		if (null !== $this->_controller)
			$params['controller'] = $this->_controller;
			
		if (null !== $this->_module)
			$params['module'] = $this->_module;
		
		return $params + $this->_params;
	}

	/**
	 * @var string
	 */
	protected $_route;

	/**
	 * @param string $route
	 */
	public function setRouteName($route) {
		$this->_route = (string) $route;
	}
	
	/**
	 * @return string
	 */
	public function getRouteName() {
		return $this->_route;
	}
	
	/**
	 * @var string
	 */
	protected $_reset = false;

	/**
	 * @param bool $reset
	 */
	public function setReset($reset) {
		$this->_reset = (bool) $reset;
	}
	
	/**
	 * @return bool
	 */
	public function getReset() {
		return $this->_reset;
	}
	
	/**
	 * @var bool
	 */
	protected $_encode = false;

	/**
	 * @param bool $reset
	 */
	public function setEncode($reset) {
		$this->_encode = (bool) $reset;
	}
	
	/**
	 * @return bool
	 */
	public function getEncode() {
		return $this->_encode;
	}
}