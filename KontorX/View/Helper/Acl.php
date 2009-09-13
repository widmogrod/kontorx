<?php
require_once 'Zend/View/Helper/Abstract.php';
class KontorX_View_Helper_Acl extends Zend_View_Helper_Abstract {

	/**
	 * @var KontorX_Controller_Action_Helper_Acl
	 */
	protected $_aclActionHelper;

	/**
	 * @return KontorX_Controller_Action_Helper_Acl
	 */
	public function getAclActionHelper() {
		if (null === $this->_aclActionHelper) {
			require_once 'Zend/Controller/Action/HelperBroker.php';
			if (!Zend_Controller_Action_HelperBroker::getStaticHelper('Acl')) {
				require_once 'KontorX/Controller/Action/Helper/Acl.php';
				$this->_aclActionHelper = new KontorX_Controller_Action_Helper_Acl();
				Zend_Controller_Action_HelperBroker::addHelper($this->_acl);
			} else {
				$this->_aclActionHelper = Zend_Controller_Action_HelperBroker::getExistingHelper('Acl');
			}
		}
		return $this->_aclActionHelper;
	}
	
	/**
	 * @var Zend_Auth
	 */
	protected $_acl;

	/**
	 * @return Zend_Auth
	 */
	public function getAcl() {
		if (null === $this->_acl) {
			$this->_acl = $this->getAclActionHelper()->getAcl();
		}
		return $this->_acl;
	}

	/**
	 * @return KontorX_View_Helper_Auth
	 */
	public function acl() {
		return $this;
	}

	/**
	 * @param string $privilage
	 * @param string $resource
	 * @return bool
	 */
	public function isAllowed($privilage = null, $resource = null) {
		$helper = $this->getAclActionHelper();
		return $helper->isAllowed($privilage, $resource);
	}
	
	/**
	 * @param string $privilage
	 * @param string $resource
	 * @return bool
	 */
	public function direct($privilage = null, $resource = null) {
		return $this->isAllowed($privilage, $resource);
	}
}