<?php
/**
 * Promotor_View_Helper_ShopCart
 * 
 * @version $Id$
 * @author $Author$
 */
class Promotor_View_Helper_ShopCart extends Zend_View_Helper_Abstract {

	/**
	 * @var Shop_Model_Cart
	 */
	protected $_cart;
	
	/**
	 * @return void
	 */
	public function __construct() {
		$this->_cart = Shop_Model_Cart::getInstance();
	}

	/**
	 * @param string $partial
	 * @param string $module
	 * @return Promotor_View_Helper_ShopCart
	 */
	public function shopCart($partial = null, $module = null) {
		$this->setPartial($partial);
		$this->setModule($module);

		return $this;
	}

	/**
	 * @return array
	 */
	public function getProducts() {
		return $this->_cart->getProducts();
	}

	/**
	 * @var string
	 */
	protected $_module;
	
	/**
	 * @param string $module
	 * @return Promotor_View_Helper_ShopCart
	 */
	public function setModule($module) {
		$this->_module = (string) $module;
		return $this;
	}
	
	/**
	 * @var string
	 */
	protected $_partial;
	
	/**
	 * @param string $partial
	 * @return Promotor_View_Helper_ShopCart
	 */
	public function setPartial($partial) {
		$this->_partial = (string) $partial;
		return $this;
	}
	
	public function render($partial = null, $module = null) {
		if (null !== $partial) {
			$this->setPartial($partial);
		}

		// zachowanie domyślne
		if (true === $module 
				|| null === $this->_partial)
		{
			// są produkty
			if (($this->_cart->hasProducts()) > 0) {
				$message = '<p>W Twoim koszyku znajduje się <b>%d produktów</b>, <br/>na łączną kwotę <b>%s zł</b></p>';
				$amount = $this->_cart->getTotalQuantity();
				$total  = $this->_cart->getTotalPrice();

				$result = sprintf($message, $amount, $total) . $append;
			} else {
				$result = '<p>Twój koszyk na zakupy jest pusty!</p>';
			}
			
			return $result;
		}

		$model = array(
			'products' => $this->_cart->getProducts(),
			'amount'   => $this->_cart->getTotalQuantity(),
			'total'    => $this->_cart->getTotalPrice(),
		);

		/* @var $partial Zend_View_Helper_Partial */
		$partial = $this->view->getHelper('Partial');
		return $partial->partial($this->_partial, $this->_module, $model);
	}

	public function _toString() 
	{
		try {
			return $this->render();
		} catch (Exception $e) {
			$error = sprintf('%s::%s[%d]', get_class($e), $e->getMessage(), $e->getLine());
			trigger_error($error, E_USER_WARNING);
		}
		return '';
	}
}