<?php
require_once 'Zend/View/Helper/Abstract.php';

/**
 * KontorX_View_Helper_Renderer
 * 
 * @category 	KontorX
 * @package 	KontorX_View
 * @subpackage  Helper
 * @version 	0.1.8
 * @license		GNU GPL
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
 */
class KontorX_View_Helper_Renderer extends Zend_View_Helper_Abstract {
	/**
	 * "Konstruktor"
	 *
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
		$content = preg_replace("/{{(\w+):(.+[^}}]+)}}/ie","\$this->_parse('$1','$2')", $content);
		$content = $this->_postContent($content);
		return $content;
	}

	/**
	 * Magic method __toString
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->render();
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
			case 'page': return $view->url(array('url'=>$value),'page');
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
?>