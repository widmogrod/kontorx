<?php
/**
 * Promotor_View_Helper_Renderer
 */
class Promotor_View_Helper_Renderer extends Zend_View_Helper_Abstract {
	/**
	 * @param string $content
	 * @return string
	 */
	public function renderer($content = null) {
		if (null !== $content) {
			$this->setContent($content);
		}
		return $this;
	}

	protected $_content = null;
	
	/**
	 * Ustawienie tresci do parsowania
	 *
	 * @param unknown_type $content
	 */
	public function setContent($content) {
		$this->_content = $content;
		return $this;
	}

	/**
	 * Uchwyt przed parsowaniem
	 *
	 * @param string $content
	 * @return string
	 */
	protected function _preContent($content) {
		return $content;
	}

	/**
	 * Uchwyt po parsowaniu
	 *
	 * @param string $content
	 * @return string
	 */
	protected function _postContent($content) {
		return $content;
	}

	/**
	 * Renderowanie
	 *
	 * @param string $content
	 * @return string
	 */
	public function render($content = null) {
		$content = (null === $content)
			? $this->_content
			: $content;

		$content = $this->_preContent($content);
		$content = preg_replace("/{{(\w+):([a-z0-9_\-\.=;:^}}]+)}}/ie","\$this->_parse('$1','$2')", $content);
		$content = $this->_postContent($content);
		return $content;
	}

	/**
	 * Magic method __toString
	 *
	 * @return string
	 */
	public function __toString() {
		try {
			return $this->render();
		} catch (Exception $e) {
			trigger_error($e->getMessage(), E_USER_WARNING);
		}
	}
	
	/**
	 * Parsuje zmienne
	 *
	 * @param string $type
	 * @param string $value
	 * @return string
	 */
	protected function _parse($type, $value) {
		$value = urldecode($value);
		$view = $this->view;
		switch ($type) {
			default:	 return "<!-- $value -->";
			case 'site': return $view->url(array('alias'=>$value),'site');
			case 'action':
				// separator parametrow ;
				$params = explode(';',$value);
				if(count($params) < 3) {
					return '<!-- liczba parametrow jest nieprawidlowa -->';
				} else {
					$action 	= array_shift($params);
					$controoler = array_shift($params);
					$module 	= array_shift($params);
					// (array) $params - bo moze byc 3 parametry i wtedy $params jest null
					$params 	= $this->_prepareParam((array) $params);
					return $view->action($action, $controoler, $module, $params);
				}
		}
	}

	/**
	 * Parsuje i przygotowuje parametry
	 * 
	 * TODO moze dodac obsluge http_query_paras ?
	 *
	 * @param array $params
	 * @return array
	 */
	protected function _prepareParam(array $params) {
		$result = array();
		foreach ($params as $value) {
			// czy jest separator klucz = wartosc ?
			if(strpos($value,'=') !== false) {
				$value = explode('=', $value);
				$result[$value[0]] = $value[1];
			}
		}
		return $result;
	}
}