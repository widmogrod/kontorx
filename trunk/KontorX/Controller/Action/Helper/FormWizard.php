<?php
class KontorX_Controller_Action_Helper_FormWizard 
		extends Zend_Controller_Action_Helper_Abstract
			/**
			 * TODO:
			 * Iteracja miała by sens gdyby, current()
			 * zwracał obiekt z krórego można by było się dostać 
			 * do odpowiednich dancyh typu:
			 * - form - Zend_Form object
			 * - option - opcje akcji
			 * - name - nazwa akcji
			 * 
			 *  Na chwilę obecną jest to zbędne.. ale
			 *  praktyka pokaże.
			 */
			implements Iterator, Countable
{

	/**
	 * @var string
	 */
	const NS = 'KontorX_Controller_Action_Helper_FormWizard';
				
	/**
	 * @var Zend_Session_Namespace
	 */
	protected static $_storage;

	/**
	 * @var Zend_Session_Namespace
	 */
	protected $_storedData;

	/**
	 * @var string
	 */
	protected $_currentAction;
	
	/**
	 * @var array
	 */
	protected $_actionsList;
	
	/**
	 * @var int
	 */
	protected $_count = 0;

	/**
	 * @var int
	 */
	protected $_pointer = -0;
	
	/**
	 * @var array
	 */
	protected $_options;
	
	/**
	 * @var array of Zend_Forms
	 */
	protected $_forms = array();

	public function init() {
		$action = $this->getActionController();
		if (!isset($action->formWizard)
				|| !is_array($action->formWizard))
		{
			return;
		}

		// ustawienie opcji
		$this->setOptions($action->formWizard);

		// inicjuje przechowywanie danych..
		$this->_initStorageData();

		// konfigurowanie ..
		$this->_currentAction = $this->getRequest()->getActionName();

		$this->_actionsList = array_keys($this->_options);
		$this->_count = count($this->_actionsList);
		$this->_pointer = array_search($this->_currentAction, $this->_actionsList, true);
	}

	/**
	 * Inicjowanie przechowywania danych
	 * @return void
	 */
	protected function _initStorageData() {
		if (null === self::$_storage) {
			self::$_storage = new Zend_Session_Namespace(self::NS, true);
		}

		if (null === $this->_storedData) {
			$ns = get_class($this->getActionController());
			
			if (!isset(self::$_storage->$ns)
					|| !is_array(self::$_storage->$ns))
			{
				self::$_storage->$ns = array();
			}

			$this->_storedData = &self::$_storage->$ns;

		}
	}

	/**
	 * @param array $aOptions
	 * @return void
	 */
	public function setOptions(array $aOptions) {
		$this->_options = $aOptions;
	}
	
	/**
	 * Pobierz przechowywane dane dla konkretnej akcji lub dla
	 * wszystkich akcji
	 * 
	 *  @param string|null $action
	 *  @return array|null
	 */
	public function getStoredData($action = null) {
		if (null === $action) {
			return $this->_storedData;
		} else
		if (isset($this->_storedData[$action])) {
			return $this->_storedData[$action];
		}
	}

	/**
	 * Dodaj dane do przechowywania
	 * 
	 * @param string $action
	 * @param array $data
	 * @return void
	 */
	public function setStoredData($action, array $data) {
		$action = (null === $action)
			? $this->_currentAction : $action;

		$this->_storedData[$action] = $data;
	}

	/**
	 * Resetuje przechowywane dane dla tego kreatora,
	 * 
	 * @param string|bool $action 	- null   - resetuje wszystkie dane
	 * 								- string - resetuje dane tylko dla przekazanej akcji (jesli istnieje)
	 * 								- true   - resetuje dane dla aktualnej akcji
	 * @return bool
	 */
	
	public function resetStoredData($action = null) {
		if (null === $action) {
			$this->_storedData = array();
		} else
		if (true === $action) {
			$this->_storedData[$this->_currentAction] = array();
		} else 
		if (isset($this->_storedData[$action])){
			$this->_storedData[$action] = array();
		} else {
			return false;
		}

		return true;
	}

	/**
	 * Sprawdź czy przekazana nazwa akcji jest akcją aktualną
	 * @param string $actionName
	 * @return boolean
	 */
	public function isCurrentAction($actionName) {
		return $this->_currentAction == (string) $actionName;
	}

	/**
	 * Zwraca aktualną nazwę akcji
	 * @return string
	 */
	public function getCurrentActionName() {
		return $this->_currentAction;
	}
	
	/**
	 * Zwraca następną nazwę akcji
	 * @return string or null
	 */
	public function getNextActionName() {
		++$this->_pointer;
		$nextAction = $this->key();
		--$this->_pointer;

		return $nextAction;
	}

	/**
	 * Zwraca poprzednią nazwę akcji
	 * @return string or null
	 */
	public function getPreviewActionName() {
		--$this->_pointer;
		$nextAction = $this->key();
		++$this->_pointer;

		return $nextAction;
	}
	
	/**
	 * @var array
	 */
	protected $_names;
	
	/**
	 * Pobierz nazwy poszczególnych akcji kreatora 
	 * @return array 
	 */
	public function getNames() {
		if (null === $this->_names) {
			foreach ($this->_options as $action => $options) {
				$this->_names[$action] = 
						isset($options['name'])
							? $options['name'] : $action;
			}
		}

		return $this->_names;
	}

	/**
	 * @return Zend_Form
	 * @throws Exception
	 */
	public function current () {
		// sprawdź, czy akcja jest skonfigurowana w opcjach kreatora
		if (!isset($this->_options[$this->_currentAction])) {
			throw new Exception(sprintf('Current form for action "%s" do not exsists or is not configured in wizard', $this->_currentAction));
		}

		// sprawdź, czy akcja korzysta z elementu Zend_Form
		if (!isset($this->_options[$this->_currentAction]['formClass'])) {
			require_once 'KontorX/Controller/Action/Helper/FormWizard/ActionHasNoFormException.php';
			throw new KontorX_Controller_Action_Helper_FormWizard_ActionHasNoFormException();
		}

		// sprawdź, czy nie ma już utworzonego obiektu Zend_Form dla akcji 
		if (!isset($this->_forms[$this->_currentAction])) {
			$formClass = (string) $this->_options[$this->_currentAction]['formClass'];

			// sprawdź czy klasa istnieje
			if (!class_exists($formClass)) {
				throw new Exception(sprintf('Form class "%s" do not exsists for action "%s"', $formClass, $this->_currentAction));
			}

			$this->_forms[$this->_currentAction] = new $formClass();

			// sprawdź, czy utworzony obiekt jest instancją Zend_Form
			if (!($this->_forms[$this->_currentAction] instanceof Zend_Form)) {
				$errorClass = get_class($this->_forms[$this->_currentAction]);
				unset($this->_forms[$this->_currentAction]);

				throw new Exception(sprintf('Form class "%s" for action "%s" is not Zend_Form instacje! is "%s"', $formClass, $this->_currentAction, $errorClass));
			}
		}
		
		return $this->_forms[$this->_currentAction];
	}

	/**
	 * Akcja sprawdza poprawność formularza a nastepnie,
	 * przekierowywuje do nastepnej akcji
	 *  
	 * @return void
	 */
	public function next() {
		if (!$this->valid()) {
			return;
		}

		$rq = $this->getRequest();
		
		try {
			// może być Zend_Form ale nie musi! dlatego gdy
			// KontorX_Controller_Action_Helper_FormWizard_ActionHasNoFormException
			$form = $this->current();
			
			$action = $this->key();
	
			// POST jest standardem!
			if (!$rq->isPost()) {
				$form->setDefaults($this->getStoredData($action));
				return;
			}
	
			if (!$form->isValid($rq->getPost())) {
				return;
			}
	
			$this->setStoredData($action, $form->getValues());

		} catch(KontorX_Controller_Action_Helper_FormWizard_ActionHasNoFormException $e) {
			// Akcja w kreatorze nie korzysta z Zend_Form
		}

		++$this->_pointer;

		$nextAction = $this->key();

		/* @var Zend_Controller_Action_Helper_Redirector */
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
		$redirector->goTo($nextAction);
	}

	/**
	 * Zwraca nazwę aktualnej akcji lub null
	 * @return string 
	 */
	public function key() {
		if ($this->valid())
			return $this->_actionsList[$this->_pointer];
	}

	/**
	 * Czy są akcjie w kreatorze
	 * @return bool 
	 */
	public function valid() {
		return $this->_pointer <= $this->_count;
	}

	/**
	 * Przewija kreator do początku i resetuje przechowywane dane
	 * @return void 
	 */
	public function rewind() {
		$this->_pointer = 0;
		// reset przechowywanych danych
		$this->resetStoredData();
	}

	/**
	 * Licza elementów w kreatorze
	 * @return int 
	 */
	public function count() {
		return $this->_count;
	}
}