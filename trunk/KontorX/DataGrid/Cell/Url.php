<?php
require_once 'KontorX/DataGrid/Cell/ViewHelper.php';
class KontorX_DataGrid_Cell_Url extends KontorX_DataGrid_Cell_ViewHelper {

	/**
	 * @var array
	 */
	protected $_primaryKey = null;
	
	/**
	 * @param array|string $primaryKey
	 * @return void
	 */
	public function setPrimaryKey($primaryKey) {
		$this->_primaryKey = (array) $primaryKey;
	}
	
	/**
	 * @return array
	 * @throws KontorX_DataGrid_Exception
	 */
	public function getPrimaryKey() {
		if (null === $this->_primaryKey) {
			require_once 'KontorX/DataGrid/Exception.php';
			throw new KontorX_DataGrid_Exception('Primary key is not set');
		}

		return $this->_primaryKey;
	}

	/**
	 * @var string
	 */
	protected $_name = null;

	/**
	 * @param string $name
	 * @return void
	 */
	public function setName($name) {
		$this->_name = $name;
	}

	/**
	 * @return string
	 */
	public function getName() {
		if (null === $this->_name) {
			$columnName = $this->getColumnName();
			if (null === ($this->_name = $this->getData($columnName))) {
				$this->_name = $columnName;
			}
		}
		return $this->_name;
	}
	
	/**
	 * @return array
	 */
	public function getUrlParams() {
		$pk = $this->getPrimaryKey();
		$pk = array_intersect_key($this->getData(), array_flip($pk));

		$params = $pk;
		if (null !== ($module = $this->getAttrib('module'))) {
			$params['module'] = $module;
		}
		if (null !== ($controller = $this->getAttrib('controller'))) {
			$params['controller'] = $controller;
		}
		if (null !== ($action = $this->getAttrib('action'))) {
			$params['action'] = $action;
		}
		if (null !== ($p = $this->getAttrib('params'))) {
			$params = array_merge($params, $p);
		}

		return $params; 
	}

	public function render() {
		$params = $this->getUrlParams();
		$router = $this->getAttrib('router');

		$view = $this->getView();
		$href = $view->url($params, $router, false, false);
		// sprawdzam, czy parsować..
		if (strstr($href, '{{') !== false) {
			$href = $this->_parseHref($href);
		}

		$name = $this->getName();
		$class = $this->getAttrib('class');

		$format = '<a class="%s" href="%s" title="%s"><span>%s</span></a>';
		return sprintf($format, $class, $href, $name, $name);
	}

	/**
	 * @return 
	 */
	protected function _parseHref($href) {
		return preg_replace('/{{([a-zA-Z0-9_^}}]+)}}/e',"\$this->getData('$1')", $href);
	}
}