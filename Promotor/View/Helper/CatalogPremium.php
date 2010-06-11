<?php
require_once 'Zend/View/Helper/Abstract.php';
class Promotor_View_Helper_CatalogPremium extends Zend_View_Helper_Abstract {
	const PARTIAL = 'catalogPremium.phtml';

	/**
	 * @param bool $random
	 * @return string
	 */
	public function catalogPremium($random = null) {
		if (is_bool($random)) {
			$this->setRandom($random);
		}
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function render($partial = null) {
		$catalog = new Catalog_Model_CatalogList();
		$catalog->setResultCache('Zend_Cache_Hour');
		$rowset = $catalog->findAllPremium(
			$this->_district,
			$this->_page,
			$this->_rowCount,
			$this->_random
		);
		$partial = (null === $partial) ? self::PARTIAL : $partial;
		return $this->view->partial($partial, array('rowset' => $rowset));
	}

	/**
	 * @return string
	 */
	public function __toString() {
		try {
			return $this->render();
		} catch (Exception $e) {
			$msg = get_class($e) . '::' . $e->getMessage();
			trigger_error($msg, E_USER_WARNING);
			return '';
		}
	}
	
	/**
	 * @var string
	 */
	protected $_district = null;
	
	/**
	 * @param string $district
	 * @return Promotor_View_Helper_CatalogPremium
	 */
	public function setDistrict($district) {
		$this->_district = (string) $district;
		return $this;
	}
	
	/**
	 * @var integer
	 */
	protected $_page = 1;
	
	/**
	 * @param integer $page
	 * @return Promotor_View_Helper_CatalogPremium
	 */
	public function setPage($page) {
		$this->_page = (int) $page;
		return $this;
	}
	
	/**
	 * @var integer
	 */
	protected $_rowCount = 5;
	
	/**
	 * @param integer $rowCount
	 * @return Promotor_View_Helper_CatalogPremium
	 */
	public function setRowCount($rowCount) {
		$this->_rowCount = (int) $rowCount;
		return $this;
	}
	
	/**
	 * @var bool
	 */
	protected $_random = null;

	/**
	 * @param bool $flag
	 * @return Promotor_View_Helper_CatalogPremium
	 */
	public function setRandom($flag = true) {
		$this->_random = (bool) $flag;
		return $this;
	}
}