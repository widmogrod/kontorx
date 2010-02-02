<?php
class Promotor_View_Helper_ShopPromotions extends Zend_View_Helper_Abstract {

	/**
	 * @var unknown_type
	 */
	protected $_category;

	/**
	 * @var unknown_type
	 */
	protected $_limit;

	/**
	 * @param mixed $category
	 * @param integer $limit
	 * @return Promotor_View_Helper_ShopPromotions
	 */
	public function shopPromotions($category = null, $limit = null) {
		$this->_category = $category;
		$this->_limit = $limit;

		return $this;
	}

	public function setLimit($limit) {
		$this->_limit = $limit;
		return $this;
	}
	
	public function _getData() {
		$model = new Shop_Model_Promotion();
		list($rowset, $select) = $model->findAllInCategoryCache($this->_category, null, $this->_limit);
		return $rowset;
	}
	
	/**
	 * @var string
	 */
	protected $_partial = '_partial/shopPromotion.phtml';

	public function render($partial = null, $module = null) {
		if (!is_string($partial)) {
			$partial = $this->_partial;
		}

		$model = $this->_getData();
		
		/* @var $partialLoop Zend_View_Helper_PartialLoop */
		$partialLoop = $this->view->getHelper('PartialLoop');
		return $partialLoop->partialLoop($partial, $module, $model);
	}

	/**
	 * @return string
	 */
	public function __toString() {
		try {
			return $this->render();
		} catch (Exception $e) {
			trigger_error($e->getMessage(), E_USER_WARNING);
			return '';
		}
	}
}