<?php
require_once 'Zend/Controller/Action/Helper/Abstract.php';
class KontorX_Controller_Action_Helper_Scaffolding extends Zend_Controller_Action_Helper_Abstract {
	/**
     * Trigger type constants
     */
	const TRIGGER_GET_MODEL  = 'TRIGGER_GET_MODEL';
	const TRIGGER_GET_FORM 	 = 'TRIGGER_GET_FORM';
	const TRIGGER_HAS_RECORD = 'TRIGGER_HAS_RECORD';
	const TRIGGER_NO_RECORD  = 'TRIGGER_NO_RECORD';
	const TRIGGER_HAS_DATA   = 'TRIGGER_HAS_DATA';
	const TRIGGER_NO_DATA    = 'TRIGGER_NO_DATA';
	const TRIGGER_IS_VALID 	 = 'TRIGGER_IS_VALID';
	const TRIGGER_NO_VALID 	 = 'TRIGGER_NO_VALID';
	const TRIGGER_PREPARE_DATA 	 = 'TRIGGER_PREPARE_DATA';
	const TRIGGER_CRUD 	 	= 'TRIGGER_CRUD';
	const TRIGGER_SUCCESS 	 = 'TRIGGER_SUCCESS';
	const TRIGGER_ERROR   	 = 'TRIGGER_ERROR';

	protected $_triggers = array(
		self::TRIGGER_GET_MODEL,
		self::TRIGGER_GET_FORM,
		self::TRIGGER_HAS_RECORD,
		self::TRIGGER_NO_RECORD,
		self::TRIGGER_HAS_DATA,
		self::TRIGGER_NO_DATA,
		self::TRIGGER_IS_VALID,
		self::TRIGGER_NO_VALID,
		self::TRIGGER_PREPARE_DATA,
		self::TRIGGER_CRUD,
		self::TRIGGER_SUCCESS,
		self::TRIGGER_ERROR
	);
	
	/**
     * Trigger actions constants
     */
	const ACTION_ADD 	= 'add';
	const ACTION_EDIT 	= 'edit';
	const ACTION_DELETE = 'delete';
	const ACTION_MODIFY = 'modify';
	
	protected $_actions = array();

	protected $_actionsSchema = array(
		self::ACTION_ADD => array(
			'type' => self::ACTION_ADD,
			'callbacks' => array(
				self::TRIGGER_GET_MODEL => 'getModel',
				self::TRIGGER_GET_FORM  => 'addGetForm',
				self::TRIGGER_HAS_DATA  => 'addHasData',
				self::TRIGGER_NO_DATA   => 'addNoData',
				self::TRIGGER_IS_VALID  => 'addIsValid',
				self::TRIGGER_NO_VALID  => 'addNoValid',
				self::TRIGGER_PREPARE_DATA  => 'addPrepareData',
				self::TRIGGER_CRUD  	=> 'addInsert',
				self::TRIGGER_SUCCESS   => 'addSuccess',
				self::TRIGGER_ERROR  	=> 'addError'
			)
		),
		self::ACTION_EDIT => array(
			'type' => self::ACTION_EDIT,
			'callbacks' => array(
				self::TRIGGER_GET_MODEL => 'getModel',
				self::TRIGGER_GET_FORM  => 'editGetForm',
				self::TRIGGER_HAS_RECORD=> 'editHasRecord',
				self::TRIGGER_NO_RECORD => 'editNoRecord',
				self::TRIGGER_HAS_DATA  => 'editHasData',
				self::TRIGGER_NO_DATA   => 'editNoData',
				self::TRIGGER_IS_VALID  => 'editIsValid',
				self::TRIGGER_NO_VALID  => 'editNoValid',
				self::TRIGGER_SUCCESS   => 'editSuccess',
				self::TRIGGER_ERROR  	=> 'editError'
			)
		)
	);

	public $view = null;
	
	/**
	 * @return KontorX_Controller_Action_Helper_Scaffolding
	 */
	public function direct() {
		return $this;
	}

	public function addAction($action, $options = null) {
		if (is_array($options)) {
			$options = $this->_validateOptions($options);
		} else
		if (is_string($options) && array_key_exists($options, $this->_actionsSchema)) {
			$options = $this->_actionsSchema[$options];
		} else {
			// TODO EXCEPTION ?
			$options = array();
		}

		$this->_actions[$action] = $options;
	}

	public function getAction($action) {
		$this->hasAction($action, true);
		return $this->_actions[$action];
	}

	public function hasAction($action,  $throwException = false) {
		if (array_key_exists($action, $this->_actions)) {
			return true;
		}

		if ($throwException) {
            require_once 'Zend/Controller/Action/Exception.php';
            throw new Zend_Controller_Action_Exception(sprintf('Action "%s" does not exist', $action));
		}

		return false;
	}

	public function removeAction($action) {
		$this->hasAction($action, true);
		unset($this->_actions[$action]);
	}

	public function clearActions() {
		$this->_actions = null;
		$this->_actions = array();
	}

	public function setType($type, $action) {
		$this->hasAction($action, true);
		$this->_validateType($type,true);
		$this->_actions[$action]['type'] = $type;
	}

	public function getType($action) {
		$this->hasAction($action, true);
		return $this->_actions[$action]['type'];
	}

	public function hasType($action) {
		$this->hasAction($action, true);
		return array_key_exists('type', $this->_actions[$action]);
	}
	
	public function removeType($action) {
		$this->hasAction($action, true);
		unset($this->_actions[$action]['type']);
	}

	public function clearType($action) {
		$this->hasAction($action, true);
		$this->_actions[$action]['type'] = null;
	}
	
	public function addCallback($callback, $action, $trigger) {
		$this->hasAction($action, true);
		$this->hasCallback($action, $trigger, true);

		if (!array_key_exists('callbacks', $this->_actions[$action])) {
			$this->_actions[$action]['callbacks'] = array();
		}
		$this->_actions[$action]['callbacks'][$trigger] = $callback;
	}
	
	public function getCallback($action, $trigger) {
		$this->hasAction($action, true);
		$this->hasCallback($action, $trigger);
		return $this->_actions[$action]['callbacks'][$trigger];
	}

	public function getCallbacks($action) {
		$this->hasAction($action, true);
		if (array_key_exists('callbacks', $this->_actions[$action])) {
			return $this->_actions[$action]['callbacks'];
		}
		return null;
	}

	public function hasCallback($action, $trigger, $throwException = false) {
		$this->hasAction($action, true);
		$this->_validateTrigger($trigger, true);

		if (array_key_exists($action, $this->_actions)) {
			return true;
		}

		if (array_key_exists('callbacks', $this->_actions[$action])) {
			return true;
		}

		if ($throwException) {
            require_once 'Zend/Controller/Action/Exception.php';
            throw new Zend_Controller_Action_Exception(sprintf('Callback "%s" does not exist', $trigger));
		}

		return false;
	}

	public function removeCallback($action, $trigger) {
		$this->hasAction($action, true);
	}

	public function clearCallback($action) {
		$this->hasAction($action, true);
	}

	const STATUS_SUCCESS = 'STATUS_SUCCESS';
	const STATUS_ERROR = 'STATUS_ERROR';

	protected $_status = null;

	public function setStatus($status) {
		switch ($status) {
			case self::STATUS_ERROR:
			case self::STATUS_SUCCESS:
				$this->_status = $status;
				break;
		}
	}

	public function getStatus() {
		return $this->_status;
	}

	protected function _validateOptions(array $options) {
		$result = array(
			'type' => null,
			'callbacks' => array()
		);
		foreach ($options as $key => $value) {
			if (is_string($value)) {
				if (array_key_exists($value, $this->_actionsSchema)) {
					$result = array_merge($result, $this->_actionsSchema[$value]);
				}
			} else
			if (is_array($value)) {
				switch ($key) {
					case 'type':
						if (array_key_exists((string) $value, $this->_actionsSchema)) {
							$result['callbacks']['type'] = (string) $value;
						}
						break;
					case 'callbacks':
						foreach ($value as $trigger => $callback) {
							if (in_array($trigger, $this->_triggers)) {
								$result['callbacks'][$trigger] = $callback;
							}
						}
						break;
				}
			}
		}
		return $result;
	}
	
	protected function _validateTrigger($trigger, $throwException = false) {
		if (in_array($trigger, $this->_triggers)) {
			return true;
		}

		if ($throwException) {
            require_once 'Zend/Controller/Action/Exception.php';
            throw new Zend_Controller_Action_Exception(sprintf('Trigger "%s" does not exist', $trigger));
		}

		return false;
	}

	protected function _validateType($type, $throwException = false) {
		if (array_key_exists($type, $this->_actionsSchema)) {
			return true;
		}

		if ($throwException) {
            require_once 'Zend/Controller/Action/Exception.php';
            throw new Zend_Controller_Action_Exception(sprintf('Action type "%s" does not exist', $type));
		}

		return false;
	}

	const CALLBACK_NO_EXSISTS = 'CALLBACK_NO_EXSISTS';

	public function runCallback($action, $trigger) {
		if (func_num_args() > 2) {
			$params = func_get_args();
			array_shift($params);
			array_shift($params);
			$params = is_array($params) ? $params : array($params);
		} else {
			$params = array();
		}

		$callback = $this->getCallback($action, $trigger);
		$result = $this->_runCallback($callback, $params);
		
		if (self::CALLBACK_NO_EXSISTS === $result) {
			require_once 'Zend/Controller/Action/Exception.php';
			throw new Zend_Controller_Action_Exception(sprintf('Invalid action callback "%s" for trigger "%s" registered for action "%s"', $callback, $trigger, $action));
		}
		return $result;
	}
	
	protected function _runCallback($callback, array $params = array()) {
		if (is_string($callback) && method_exists($this, $callback)) {
			return call_user_func_array(array($this, $callback), $params);
		} elseif (is_string($callback) && method_exists($this->getActionController(), $callback)) {
			return call_user_func_array(array($this->getActionController(), $callback), $params);
		} elseif (is_string($callback) && function_exists($callback)) {
			return call_user_func_array($callback, $params);
		} elseif (is_array($callback)) {
			return call_user_func_array($callback, $params);
		}
		
		return self::CALLBACK_NO_EXSISTS;
	}

	public function init() {
		$controller = $this->getActionController();
		$this->view = $controller->view;
		if (!isset($controller->scaffolding)
				|| !is_array($controller->scaffolding)) {
			return;
		}
		foreach ($controller->scaffolding as $action => $options) {
			$this->addAction($action, $options);
		}
	}
	
	public function preDispatch() {
		$controller = $this->getActionController();
		$request	= $this->getRequest();
		$action		= $request->getActionName();

		if (!$this->hasAction($action)) {
			return;
		}

		$type = $this->getType($action);
		switch ($type) {
			case self::ACTION_ADD:
				$this->add($action);
//			default:
//				require_once 'Zend/Controller/Action/Exception.php';
//                throw new Zend_Controller_Action_Exception(sprintf('Invalid action type "%s" for action "%s"', $type, $action));
		}
	}

	public function add($action){
		// storzenie formularza
		$model = $this->runCallback($action, self::TRIGGER_GET_MODEL);
		// storzenie formularza
		$form = $this->runCallback($action, self::TRIGGER_GET_FORM, $model);

		// TODO W przyszlosci dodac uchwyt generujacy
		// sprawdzanie danych nie tylko post!
    	if (!$this->runCallback($action, self::TRIGGER_HAS_DATA)) {
    		$this->runCallback($action, self::TRIGGER_NO_DATA, $form);
    		return;
    	}

		if (!$this->runCallback($action, self::TRIGGER_IS_VALID, $form)) {
    		$this->runCallback($action, self::TRIGGER_NO_VALID, $form);
    		return;
    	}

		try {
			$data = $this->runCallback($action, self::TRIGGER_PREPARE_DATA, $form);
			$row = $this->runCallback($action, self::TRIGGER_CRUD, $data, $form, $model);
			$row = $this->runCallback($action, self::TRIGGER_SUCCESS, $row);
		} catch (Zend_Db_Table_Row_Exception $e) {
			 $this->runCallback($action, self::TRIGGER_ERROR, $e, $row);
		} catch (Zend_Db_Table_Abstract $e) {
			$this->runCallback($action, self::TRIGGER_ERROR, $e, $row);
		} catch (Zend_Db_Statement_Exception $e) {
			$this->runCallback($action, self::TRIGGER_ERROR, $e, $row);
		}
    }
	
	public function addGetForm(Zend_Db_Table_Abstract $model) {
    	/**
		 * @see KontorX_Form_DbTable
		 */
		require_once 'KontorX/Form/DbTable.php';
		$form = new KontorX_Form_DbTable($model);

		// To taki dodatek odemnie ;)
		$form->addElement('submit','Dodaj',array('class' =>'action add','ignore' => true));

		return $form;
	}

	public function addHasData() {
		return $this->getRequest()->isPost();
	}

	public function addNoData(Zend_Form $form) {
    	$this->view->form = $form;
    }

	public function addIsValid(Zend_Form $form) {
    	return $form->isValid($this->getRequest()->getPost());
    }

	public function addNoValid(Zend_Form $form) {
    	$this->view->form = $form;
    }

	protected function addPrepareData(Zend_Form $form) {
    	// parsowanie danych
    	$data = $form->getValues();
    	$data = get_magic_quotes_gpc() ? array_map('stripslashes', $data) : $data;
    	return $data;
    }

    protected function addInsert(array $data, Zend_Form $form, Zend_Db_Table_Abstract $model) {
    	// dodawanie rekordu
    	$row = $model->createRow($data);
    	$row->save();

    	return $row;
    }
    
	public function addSuccess(Zend_Db_Table_Row_Abstract $row) {
		// tworzenie komunikatu
    	$message = 'Rekord został dodany';

		$this->setStatus(self::STATUS_SUCCESS);
	}

	public function addError(Exception $e, Zend_Form $form) {
		// tworzenie komunikatu
		$message = 'Rekord nie został dodany';

		$this->view->form = $form;
		$this->setStatus(self::STATUS_SUCCESS);
	}
}