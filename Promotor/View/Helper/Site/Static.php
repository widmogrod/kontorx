<?php
class Promotor_View_Helper_Site_Static extends Promotor_View_Helper_Site_Abstract {

	/**
	 * @var Zend_Navigation
	 */
	protected $_partial;
	
	/**
	 * @param string $partial
	 * @return Promotor_View_Helper_Site_Static
	 */
	public function setPartial($partial) {
		$this->_partial = (string) $partial;
		return $this;
	}
	
	/**
	 * @param string $partial
	 * @return string  
	 */
	public function render($partial = null) {
		// 
		if (null === $partial) {
			$partial = $this->_partial;
		}

		/* @var $model Site_Model_Site */
		$model = $this->_site->getModel();
		if (!$row = $model->findOneCache($this->getIdentification())) {
			return '';
		}

		return (null === $partial) ? $row['content'] : $this->_site->view->getHelper('partial')->partial($partial, $row);
	}
}