<?php
require_once 'KontorX/DataGrid/Renderer/Abstract.php';

/**
 * @author gabriel
 * @todo może - dodać default partial datagrid file!
 */
class KontorX_DataGrid_Renderer_HtmlTable extends KontorX_DataGrid_Renderer_Abstract {

	public function render($partial = null) {
		if (is_string($partial)) {
			$this->setPartial($partial);
		}
		$partial = $this->getPartial();

		/* @var $view Zend_View */
		$view = $this->getView();
		if(!$view->getHelperPath('KontorX_View_Helper_')) {
        	$view->addHelperPath('KontorX/View/Helper', 'KontorX_View_Helper_');
        }

        // wywolanie helpera widoku @see KontorX_View_Helper_DataGrid
        return $view->dataGrid($this->getDataGrid(), $partial);
	}

	/**
     * Default name of partial file @see Zend_View_Helper_Partial
     * @todo dodać partial w zasobach biblioteki!
     * @var string
     */
    private $_partial = '_partial/dataGrid.phtml';

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
			if (Zend_Registry::isRegistered('Zend_View')) {
				$this->_view = Zend_Registry::get('Zend_View');
			} elseif(Zend_Registry::isRegistered('view')) {
				$this->_view = Zend_Registry::get('view');
			} else {
				require_once 'Zend/View.php';
				$this->_view = new Zend_View();				
			}
		}
		return $this->_view;
	}
} 