<?php
class Promotor_View_Helper_CatalogOptions extends Zend_View_Helper_Abstract {

	public function catalogOptions($page = null, $rowCount = null) {
		if (is_integer($page)) {
			$this->setPage($page);
		}
		if (is_integer($rowCount)) {
			$this->setRowCount($rowCount);
		}

		return $this;
	}

	/**
	 * @var integer
	 */
	protected $_page = null;
	
	/**
	 * @return Promotor_View_Helper_CatalogOptions
	 */
	public function setPage($page) {
		$this->_page = (int) $page;
		return $this;
	}

	/**
	 * @var integer
	 */
	protected $_rowCount = null;

	/**
	 * @return Promotor_View_Helper_CatalogOptions
	 */
	public function setRowCount($rowCount) {
		$this->_rowCount = (int) $rowCount;
		return $this;
	}

	protected $_partial = null;

	/**
	 * @param string $partial
	 * @return Promotor_View_Helper_CatalogOptions
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
		$partial = (null === $partial)
			? $this->_partial
			: (string) $partial;

		$model = new Catalog_Model_Options();
		$data = $model->findAllCache($this->_page, $this->_rowCount);

		return $this->view->partial($partial, array('rowset' => $data));
	}

	public function __toString() {
		try {
			return $this->render();
		} catch (Exception $e) {
			trigger_error(get_class($this) . '::' . $e->getMessage());
			return '';
		}
	}
}