<?php
abstract class KontorX_Observable_Observer_SendMail implements KontorX_Observable_Observer_Interface {

	/**
	 * @var integer
	 */
	protected $_mailType = null;

	/**
	 * Kolekcja dostępnych typow mailo
	 *
	 * @var array
	 */
	protected $_mailFiles = array();

	/**
	 * Kolekcja tytułów maili
	 *
	 * @var array
	 */
	protected $_mailSubject = array();

	/**
	 * @var array
	 */
	protected $_data = array();

	/**
	 * @var array
	 */
	protected $_config = array();
	
	/**
	 * @var Zend_View_Interface
	 */
	protected $_view = null;
	
	/**
	 * @var string
	 */
	protected $_viewSufix = '.phtml';

	/**
	 * @var Zend_Mail
	 */
	protected $_mail = null;

	public function __construct($mailType, array $config, array $data = array(), Zend_View_Interface $view = null) {
		if (!array_key_exists($mailType, $this->_mailFiles)) {
			$error = "Mail type is unknown";
			throw new KontorX_Observable_Exception($error);
		}
		$this->_mailType = $mailType;

		if (!array_key_exists('from', $config)) {
			$error = "Mail config do not have specified `from` key";
			throw new KontorX_Observable_Exception($error);
		}
		
		if (!array_key_exists('email', $config)) {
			$error = "Mail config do not have specified `email` key";
			throw new KontorX_Observable_Exception($error);
		}

		$this->_config  = array_merge($config, $this->_config);
		$this->_data 	= $data;
		$this->_mail 	= new Zend_Mail('utf-8');

		// inicjowanie widoku
		if (null === $view) {
			$this->_view = new Zend_View();
		} else {
			$this->_view = $view;
		}

		$this->_setupView();
	}

	public function update(KontorX_Observable_Abstract $observable, array $data = array()) {
		$data = array();
		$data += $this->_data;
		$this->_send($data);
	}
	
	/**
	 * Zwraca instancje @see Zend_View_Interface
	 *
	 * @return Zend_View_Interface
	 */
	public function getView() {
		return $this->_view;
	}

	protected function _setupView() {
		if (array_key_exists('scriptPath', $this->_config)) {
			$scriptPath = $this->_config['scriptPath'];
			$scriptPath = str_replace('{{APP_MODULES_PATHNAME}}', APP_MODULES_PATHNAME, $scriptPath);
			$this->_view->setScriptPath($scriptPath);
		}
		if (array_key_exists('viewSufix', $this->_config)) {
			$this->_viewSufix = (string) $this->_config['viewSufix'];
		}
	}

	/**
	 * Zwraca instancje @see Zend_View_Interface
	 *
	 * @return Zend_Mail
	 */
	public function getMail() {
		return $this->_mail;
	}

	/**
	 * Typ wysylanego maila
	 *
	 * @return integet
	 */
	protected function getMailType() {
		return $this->_mailType;
	}

	/**
	 * Nazwa pliku zawierajacego format maila
	 *
	 * @return string
	 */
	protected function getMailTypeScriptView() {
		return $this->_mailFiles[$this->_mailType] . $this->_viewSufix;
	}

	/**
	 * Zwraca temat maila
	 *
	 * @return string
	 */
	protected function getMailTypeSubject() {
		return $this->_mailSubject[$this->_mailType];
	}

	/**
	 * Kompozycja wysysłania mail
	 *
	 * @param array $data
	 * @return bool
	 */
	protected function _send(array $data) {
//		if (!array_key_exists('email', $data)) {
//			$error = "Array `\$data` do not have `email` key";
//			throw new KontorX_Observable_Exception($error);
//		}

		$mailContent = $this->_renderMail($data);
		$this->_sendMail(
			$this->getMailTypeSubject(),
			$mailContent
		);
	}
	
	/**
	 * Renderuje mail
	 *
	 * @param array $data
	 * @return string
	 */
	protected function _renderMail(array $data) {
		$view = $this->getView();
		$view->assign($data);
		return $view->render($this->getMailTypeScriptView());
	}

	/**
	 * Wysyła mail
	 *
	 * @param string $subject
	 * @param string $to
	 * @param string $bodyHtml
	 * @return bool
	 */
	protected function _sendMail($subject, $bodyHtml) {
		$mail = $this->getMail();
		$mail->setSubject($subject);
		$mail->setBodyHtml($bodyHtml,'utf-8');
		$mail->setFrom($this->_config['from']);
		$mail->addTo($this->_config['email']);

		try {
			$mail->send();
		} catch (Zend_Mail_Exception $e) {
			throw new KontorX_Observable_Exception($e->getMessage());
		}
	}
}
?>