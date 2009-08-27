<?php
/**
 * KontorX_Observable_Form
 * 
 * @category 	KontorX
 * @package 	KontorX_Observable
 * @version 	0.1.1
 * @license		GNU GPL
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
 */
class KontorX_Observable_Form extends KontorX_Observable_Abstract {
	/**
	 * @var Zend_Form
	 */
	protected $_form = null;

	/**
	 * Konstruktor
	 *
	 * @param Zend_Form $form
	 */
	public function __construct(Zend_Form $form) {
		$this->_form = $form;
	}

	/**
	 * Sprawdza czy formularz jest wypelniony prawidlowo
	 * 
	 * Sprawdza czy formularz jest wypelniony prawidlowo,
	 * gdy tak powiadamia obserwatorow o tym fakcie ;)
	 *
	 * @param array $data
	 * @param unknown_type $type
	 */
	public function isValid(array $data) {
		$form = $this->getForm();

		// czy dane sa podane prawidlowo
		if (!$form->isValid($data)) {
			return false;
		}

		// powiadom obserwatorów, przekazujac
		// jako parametr 2 metody update() obiekt Zend_Form
		$this->notify($form);
		return true;
	}

	/**
	 * Zwraca obiekt {@see Zend_Form}
	 *
	 * @return Zend_Form
	 */
	public function getForm() {
		return $this->_form;
	}
}
?>