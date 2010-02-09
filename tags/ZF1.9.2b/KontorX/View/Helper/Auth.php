<?php
require_once 'Zend/View/Helper/Abstract.php';
class KontorX_View_Helper_Auth extends Zend_View_Helper_Abstract {

	/**
	 * @var Zend_Auth
	 */
	protected $_auth;
	
	/**
	 * @return Zend_Auth
	 */
	public function getAuth() {
		if (null === $this->_auth) {
			$this->_auth = Zend_Auth::getInstance();
		}
		return $this->_auth;
	}

	/**
	 * @return KontorX_View_Helper_Auth
	 */
	public function auth() {
		return $this;
	}

	/**
	 * @param string $key
	 * @param string $default
	 * @return mixed
	 */
	public function getIndentity($key = null, $default = null) {
		$auth = $this->getAuth();
		if (!$auth->hasIdentity()) {
			return null;
		}

		$identity = $auth->getIdentity();

		return (null === $key)
			? $identity
			: (isset($identity->$key)
				? $identity->$key
				: $default);
	}
	
	/**
	 * @return string
	 */
	public function getRole() {
		if (class_exists('KontorX_Controller_Action_Helper_Acl', false)) {
			require_once 'KontorX/Controller/Action/Helper/Acl.php';
		}

		return $this->getIndentity('role',
			KontorX_Controller_Action_Helper_Acl::DEFAULT_ROLE_ID);
	}
	
	/**
	 * @param string $key
	 * @param string $default
	 * @return mixed
	 */
	public function direct($key = null, $default = null) {
		return $this->getIndentity($key, $default);
	}
}