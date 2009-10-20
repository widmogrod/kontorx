<?php
class Promotor_View_Helper_ShopCart extends Zend_View_Helper_Abstract {

	/**
	 * @var Shop_Model_Cart
	 */
	protected $_cart;
	
	public function __construct() {
		$this->_cart = Shop_Model_Cart::getInstance();
	}
	
	public function shopCart() {
		return $this;
	}

	/**
	 * @return array
	 */
	public function getProducts() {
		return $this->_cart->getProducts();
	}

	public function render() {
		if (($count = $this->_cart->hasProducts()) > 0) {
			$message = '<p>W Twoim koszyku znajduje się <b>%d produktów</b>, <br/>na łączną kwotę <b>%s zł</b></p>';
			$total = $this->_cart->getTotalPrice();
			return sprintf($message, $count, $total);
		} else {
			return '<p>Twój koszyk na zakupy jest pusty!</p>';
		}
	}

	public function _toString() {
		try {
			return $this->render();
		} catch (Exception $e) {
			$error = sprintf('%s::%s[%d]', get_class($e), $e->getMessage(), $e->getLine());
			trigger_error($error, E_USER_WARNING);
		}
		return '';
	}
}