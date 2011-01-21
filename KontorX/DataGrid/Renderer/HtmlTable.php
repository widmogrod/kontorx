<?php
require_once 'KontorX/DataGrid/Renderer/Abstract.php';

/**
 * @author Gabriel
 */
class KontorX_DataGrid_Renderer_HtmlTable extends KontorX_DataGrid_Renderer_Abstract 
{
	const STYLE_DEFAULT = 'defaultDataGrid.phtml';

	public function render($partial = null, $module = null) 
	{
		$grid = $this->getDataGrid();

		// Setup default ViewRender
		if (null === $partial)
		{
			require_once 'Zend/View.php';
			$view = new Zend_View();
			$view->setScriptPath(dirname(__FILE__) . '/HtmlTable');
			$view->assign($grid->getVars());

			$partialStyle = $this->getPartial();
			switch ($partialStyle)
			{
				case null:
					return $view->render(self::STYLE_DEFAULT);

				case self::STYLE_DEFAULT:
					return $view->render($partialStyle);

				default:
					return $view->render(self::STYLE_DEFAULT);
					// continue
			}
		}

		if (is_string($partial))
			$this->setPartial($partial);

		if (is_string($module))
			$this->setPartial($module);
		
		$partial = $this->getPartial();
		$module  = $this->getModule();

		/* @var $view Zend_View */
		$view = $this->getView();
		if (!$view->getHelperPath('KontorX_View_Helper_'))
        	$view->addHelperPath('KontorX/View/Helper', 'KontorX_View_Helper_');

        // wywolanie helpera widoku @see KontorX_View_Helper_DataGrid
        $result = $view->dataGrid($grid, $partial, $module);
        
        return $result;
	}

	/**
     * Default name of partial file @see Zend_View_Helper_Partial
     * @var string
     */
    private $_partial;

    /**
     * Set name of partial file @see Zend_View_Helper_Partial
     * @var string
     */
    public function setPartial($partial) {
        $this->_partial = (string) $partial;
    }

    /**
     * Return name of partial file @see Zend_View_Helper_Partial
     */
    public function getPartial() {
        return $this->_partial;
    }
    
	/**
     * Default name of module name @see Zend_View_Helper_Partial
     * @var string
     */
    private $_module = null;

    /**
     * Set name of partial file @see Zend_View_Helper_Partial
     * @var string
     * @return void
     */
    public function setModule($module) {
        $this->_module = (string) $module;
    }

    /**
     * Return name of module
     * @return string
     */
    public function getModule() {
        return $this->_module;
    }
	
	/**
	 * @var Zend_View
	 */
	protected $_view;

	/**
	 * @param Zend_View $view
	 * @return KontorX_DataGrid_Renderer_HtmlTable
	 */
	public function setView(Zend_View $view) {
		$this->_view = $view;
		return $this;
	}

	/**
	 * @return Zend_View
	 */
	public function getView() {
		if (null === $this->_view) {
			require_once 'Zend/Controller/Action/HelperBroker.php';
			/* @var $viewRenderer Zend_Controller_Action_Helper_ViewRenderer */
			$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
			$viewRenderer->initView();
			$this->_view = $viewRenderer->view;
		}
		return $this->_view;
	}
} 