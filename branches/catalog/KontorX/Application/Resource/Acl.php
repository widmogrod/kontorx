<?php
/**
 * @author gabriel
 */
class KontorX_Application_Resource_Acl extends Zend_Application_Resource_ResourceAbstract {
	
	const DEFAULT_REGISTRY_KEY = 'Zend_Acl';
	
	/**
	 * @var Zend_Acl
	 */
	protected $_acl;

	/**
	 * @return Zend_Acl
	 */
	public function init() {
		return $this->getAcl();
	}

	/**
	 * @return Zend_Acl
	 */
	public function getAcl() {
		if (null === $this->_acl) {
			$options = $this->getOptions();
			
			if (!isset($options['acls'])) {
				throw new Zend_Application_Resource_Exception('No acls source data provided.');
			}

			$this->_acl = new Zend_Acl();

			$this->_setup((array) $options['acls']);

			$key = (isset($options['registry_key']) && !is_numeric($options['registry_key']))
                 ? $options['registry_key']
                 : self::DEFAULT_REGISTRY_KEY;

			Zend_Registry::set($key, $this->_acl);
		}
		return $this->_acl;
	}
	
	/**
	 * Options schema
	 * <code>
	 * $acls = array(
	 *		'guest' => array(
	 *			'role' => array(
	 *				'name' => 'guest',
	 *				'class' => null,
	 *				'parents' => null
	 * 			),
	 * 
	 * 			'resource' => array(
	 *				'name' => null,
	 *				'class' => null,
	 *				'parent' => null
	 *			),
	 *
	 *			'assert' => array(
	 *				'class' => null
	 *  		),
	 *
	 *			'privileges' => array(
	 *				'allow' => 'show',
	 *				'deny' => 'create'
	 *			)
	 *		),
	 *	);
	 * </code>
	 * 
	 * @param array $options
	 * @return void
	 */
	protected function _setup(array $options) {
		foreach ($options as $key => $value) {
			// group - default
			$roleName    = is_string($key) ? $key : null;
			$roleClass   = 'Zend_Acl_Role';
			$roleParents = null;
			// role - from config
			if (isset($value['role'])) {
				if (strlen(@$value['role']['name']) > 1) {
					$roleName = (string) $value['role']['name'];					
				}
				if (strlen(@$value['role']['class']) > 1) {
					$roleClass = (string) $value['role']['class'];					
				}
				if (isset($value['role']['parents'])) {
					$roleParents = (array) $value['role']['parents'];	
				}
			} else {
				if (null === $roleName) {
					require_once 'Zend/Acl/Exception.php';
					throw new Zend_Acl_Exception('Role name is not set');
				}
			}

			// add role
			$roleInstance = new $roleClass($roleName);
			$this->_acl->addRole($roleInstance, $roleParents);

			// resource - default
			$resourceName = null;
			$resourceClass = 'Zend_Acl_Resource';
			$resourceParent = null;
			// resource - from config
			if (isset($value['resource'])) {
				if (strlen(@$value['resource']['class']) > 1) {
					$resourceClass = (string) $value['resource']['class'];					
				}
				if (strlen(@$value['resource']['name']) > 1) {
					$resourceName = (string) $value['resource']['name'];					
				}
				if (strlen(@$value['resource']['parent']) > 1) {
					$resourceParent = (string) $value['resource']['parent'];					
				}
			}

			// add resource - if exsists
			if (null !== $resourceName) {
				$resourceInstance = new $resourceClass($resourceName);
				$this->_acl->add($resourceInstance, $resourceParent);
			}

			// assert - default
			$assertClass = null;
			$assertInstance = null;
			if (strlen(@$value['assert']['class']) > 1) {
				$assertClass = (string) $value['assert']['class'];
				$assertInstance = new $assertClass();
			}

			// privileges
			if (isset($value['privileges'])) {
				if (isset($value['privileges']['allow'])) {
					$privileges = (array) $value['privileges']['allow'];
					$this->_acl->allow($roleName, $resourceName, $privileges, $assertInstance);
				}
				if (isset($value['privileges']['deny'])) {
					$privileges = (array) $value['privileges']['deny'];
					$this->_acl->deny($roleName, $resourceName, $privileges, $assertInstance);
				}
			} else {
				$this->_acl->allow($roleName, $resourceName, null, $assertInstance);
			}
		}
	}
}