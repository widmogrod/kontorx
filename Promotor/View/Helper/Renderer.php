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
	 * Renderowanie
	 *
	 * @param string $content
	 * @return string
	 */
	public function render($content = null) {
		$content = (null === $content)
			? $this->_content
			: $content;

		if (strlen($content) > 10) {
			$content = preg_replace("/{{(\w+):([^}}]+)}}/ie","\$this->_parse('$1','$2')", $content);
		}
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
			return '';
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

		/* @var $view Zend_View */
		$view = $this->view;

		switch ($type) {
			default:
				trigger_error(sprintf('undefinded parser type "%s" with value "%s"', $type, $value), E_USER_NOTICE);
				break;
			case 'site': return $view->url(array('alias'=>$value),'site', true);
			case 'album':
				$params = explode(';',$value);
				$params = $this->_prepareParam((array) $params);

				$alias = $params[0];
				unset($params[0]);

				$partial = isset($params[1])
					? $params[1]
					: '_partial/galleryAlbum.phtml';

				return $view->galleryAlbum($alias)->render($partial);
			case 'action':
				// separator parametrow ;
				$params = explode(';',$value);
				if(count($params) < 3) {
					trigger_error(
						sprintf('parset parameters to fiew for parser type "%s" with value "%s"', $type, $value),
						E_USER_NOTICE);
					return;
				} else {
					$action 	= array_shift($params);
					$controoler = array_shift($params);
					$module 	= array_shift($params);
					// (array) $params - bo moze byc 3 parametry i wtedy $params jest null
					$params 	= $this->_prepareParam((array) $params);
					return $view->action($action, $controoler, $module, $params);
				}

			case 'helper':
				// separator parametrow ;
				$params = (array) explode(';',$value);

				$name   = array_shift($params);
				$params = $this->_prepareParam((array) $params);

				$helper = $view->getHelper($name);
				return (string) call_user_func_array(array($helper, $name), (array) $params)->render();
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
			} else {
				$result[] = $value;
			}
		}
		return $result;
	}
}