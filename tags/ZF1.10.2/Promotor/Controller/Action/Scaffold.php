<?php
/**
 * @version $Id$
 * @author $Author$
 */
class Promotor_Controller_Action_Scaffold extends Promotor_Controller_Action {

	/**
	 * @var string
	 */
	protected $_modelClass;

	/**
	 * @var string
	 */
	protected $_formAddClass;

	/**
	 * @var string
	 */
	protected $_formEditClass;

	/**
	 * @var string
	 */
	protected $_formRemoveClass;
	
	/**
	 * @var string
	 */
	protected $_addPostObservableName;
	
	/**
	 * @var string
	 */
	protected $_editPostObservableName;
	
	/**
	 * @var string
	 */
	protected $_deletePostObservableName;
	
	/**
	 * @var bool
	 */
	protected $_pagination = true;

	/**
	 * @var string
	 */
	protected $_configFilename;
	
	
	/**
	 * @return string
	 */
	protected function _getConfigFilename() {
		// ustawienie dynamicznie nazwy pliku konfiguracujnego
		if (null === $this->_configFilename) {
			$this->_configFilename = $this->getRequest()->getControllerName() . '.xml';
		}
		return $this->_configFilename;
	}
	
	/**
	 * @param string $suffix
	 * @return void
	 */
	protected function _noticeObserver($suffix = null) {
		$params = func_get_args();
		// gdy więcej niż 1 parametr przekazany do methody
		if (count($params) > 1) {
			// pietwszy parametr to suffix! 
			$suffix = array_shift($params);
		} else {
			// zerowanie parametrów
			$params = array();
		}

		$rq = $this->getRequest();

		$name = $rq->getModuleName() . '_' .
				$rq->getControllerName() . '_' .
				$rq->getActionName();

		if (null !== $suffix) {
			$name .= '_' . trim($suffix,'_'); 
		}
				
		$name = strtolower($name);
		array_unshift($params, $name); 

		$manager = Promotor_Observable_Manager::getInstance();
		try {
			// notify observers
			$list = call_user_func_array(array($manager,'notify'), $params);
			$this->_addMessages($list->getMessages());
		} catch (KontorX_Observable_Exception $e) {
			$this->_addMessages($e->getMessage());
		}
	}

	/**
	 * @param array $messages
	 * @return void
	 */
	protected function _addMessages($messages) {
		/* @var $flashMessenger Zend_Controller_Action_Helper_FlashMessenger */
		$flashMessenger = $this->_helper->getHelper('flashMessenger');

		foreach ((array) $messages as $key => $message) {
			if (is_array($message)) {
				$flashMessenger->addMessage(sprintf("%s::%s", $key, print_r($messages, true)));
			} else {
				$flashMessenger->addMessage($message);
			}
		}
	}
	
	/**
	 * @var array
	 */
	protected $_defaultRedirectActions = array(
		'add' => array('save','next','exit'),
		'edit' => array('save','next','exit'),
		'delete' => array('exit'),
	);
	
	/**
	 * user customizable
	 * @var array
	 */
	protected $_redirectAction = array();
	
	/**
	 * user customizable
	 * @var array
	 */
	protected $_redirectActionParams = array();

	/**
	 * @param string $type
	 * @param array $rowPK
	 * @return void
	 */
	protected function _redirectAction($type, array $rowPK = null) {
		$redirectAction = array_merge(
				$this->_defaultRedirectActions, 
				$this->_redirectAction);

		if (!isset($redirectAction[$type])) 
		{
			throw new Zend_Controller_Action_Exception(
				'action type "'.$type.'"for redirect do not exsists or is not defined');
		}

		/* @var $rq Zend_Controller_Request_Http */
		$rq = $this->getRequest();

		$action = $rq->getPost('__kx_action');
		$redirects = (array) $redirectAction[$type];
		
		/* @var $redirector Zend_Controller_Action_Helper_Redirector */
		$redirector = $this->_helper->getHelper('Redirector');

		$params = array();
		if (isset($this->_redirectActionParams[$type])
		 		&& isset($this->_redirectActionParams[$type][$action]))
		{
			$params = $rq->getParams();
			$params = array_intersect_key($params, array_flip((array) $this->_redirectActionParams[$type][$action]));
		}
		
		switch($action) 
		{
			case 'next':
				if (false !== ($id = $rq->getParam('id', false))) {
					$params['id'] = $id;
				}

				$this->_helper->redirector->goTo('add', null, null, $params);
				break;

			case 'save':
				$params = array_merge($rowPK, $params);
				$this->_helper->redirector->goTo('edit', null, null, $params);
				break;

			case 'exit':
			default:
				$this->_helper->redirector->goTo('list');
				break;
		}
	}

	/**
	 * @return void
	 */
	public function listAction() {
		/* @var $model Promotor_Model_Scaffold */
		$model = new $this->_modelClass();

		$rq = $this->getRequest();
		if ($rq->isPost()) {
			switch ($rq->getPost('action_type')) {
				case 'update':
					if (null !== $rq->getPost('editable')) {
						if ($this->_helper->acl->isAllowed('update')) {
							$data = $rq->getPost('editable');
							$model->editableUpdate($data);
							$this->_helper->flashMessenger($model->getStatus());
						}
					}
					$this->_helper->redirector->goToUrlAndExit(getenv('HTTP_REFERER'));
					return;

				case 'delete':
					if (null !== $rq->getPost('action_checked')) {
						if ($this->_helper->acl->isAllowed('delete')) {
							$data = $rq->getPost('action_checked');
							$model->editableDelete($data);
							$this->_helper->flashMessenger($model->getStatus());
						}
					}

					$this->_helper->redirector->goToUrlAndExit(getenv('HTTP_REFERER'));
					return;
			}
		}

		// setup data grid
		$config = $this->_helper->config($this->_getConfigFilename());
		$grid = KontorX_DataGrid::factory($model->selectList(), $config->grid);
		$grid->setValues($this->_getAllParams());

		$this->_setupDataGridPaginator($grid);

		$this->view->grid = $grid;
		
//		/* @var $viewRenderer Zend_Controller_Action_Helper_ViewRenderer  */
//		$viewRenderer = $this->_helper->getHelper('ViewRenderer');
//		$viewRenderer->
	}

	/**
	 * @return void
	 */
	public function addAction() {
		$model = new $this->_modelClass();
		$dbTable = $model->getDbTable();

		/* @var $scaffold KontorX_Controller_Action_Helper_Scaffold */
		$scaffold = $this->_helper->getHelper('Scaffold');
		$scaffold->setDbTable($dbTable)
				 ->setRowPK($this->_getAllParams());

		/* @var $form Zend_Form */
		$form = new $this->_formAddClass(array(
			'primaryKey' => $scaffold->getRowPK(),
			'request' => $this->getRequest()
		));

		$scaffold->setForm($form)
				 ->setSuppressArrayNotation(true)
				 ->run(KontorX_Controller_Action_Helper_Scaffold::CREATE);

		/* @var $flashMessenger Zend_Controller_Action_Helper_FlashMessenger */
		$flashMessenger = $this->_helper->getHelper('FlashMessenger');
		
		$status = $scaffold->getStatus();
		$result = $scaffold->getResult();
		if ($status === KontorX_Controller_Action_Helper_Scaffold::SUCCESS) {
			$flashMessenger->addMessage($status);
			
			// uduchamianie powiadomień
			if (null !== $this->_addPostObservableName) {
				$manager = Promotor_Observable_Manager::getInstance();
				$list = $manager->notify(
					$this->_addPostObservableName,
					$result,
					$form);
	
				$flashMessenger->addMessage($status);
				foreach ($list->getMessages() as $observerName => $messages) {
					$flashMessenger->addMessage(sprintf("%s::%s", $observerName, print_r($messages, true)));
				}
			} else {
				$this->_noticeObserver('post', 
					$result,
					$form);
			}

			/**
			 * Przekierowywanie akcji! 
			 */
			$this->_redirectAction('add', $scaffold->getRowPK());

		} else {
			$flashMessenger->addMessage($status);
			if ($result instanceof Exception) {
				$flashMessenger->addMessage($result->getMessage());
			}

			$this->view->form = $form;
		}
	}

	/**
	 * @return void
	 */
	public function editAction() {
		$model = new $this->_modelClass();
		$dbTable = $model->getDbTable();
		
		/* @var $scaffold KontorX_Controller_Action_Helper_Scaffold */
		$scaffold = $this->_helper->getHelper('Scaffold');
		$scaffold->setDbTable($dbTable)
				 ->setRowPK($this->_getAllParams());

		/* @var $form Zend_Form */
		$form = new $this->_formEditClass(array(
			'primaryKey' => $scaffold->getRowPK(),
			'request' => $this->getRequest()
		));

		$scaffold->setForm($form)
				 ->run(KontorX_Controller_Action_Helper_Scaffold::UPDATE);

		/* @var $flashMessenger Zend_Controller_Action_Helper_FlashMessenger */
		$flashMessenger = $this->_helper->getHelper('FlashMessenger');
			
		$status = $scaffold->getStatus();
		$result = $scaffold->getResult();
		if ($status === KontorX_Controller_Action_Helper_Scaffold::SUCCESS) {
			$flashMessenger->addMessage($status);

			// uduchamianie powiadomień
			if (null !== $this->_editPostObservableName) {
				$manager = Promotor_Observable_Manager::getInstance();
				$list = $manager->notify(
					$this->_editPostObservableName,
					$scaffold->getRowPK(),
					$scaffold->getForm());
	
				$flashMessenger->addMessage($status);
				foreach ($list->getMessages() as $observerName => $messages) {
					$flashMessenger->addMessage(sprintf("%s::%s", $observerName, print_r($messages, true)));
				}
			} else {
				$this->_noticeObserver('post',
					$scaffold->getRowPK(),
					$scaffold->getForm());
			}
			$this->view->form = $form;
			/**
			 * Przekierowywanie akcji! 
			 */
			$this->_redirectAction('edit', $scaffold->getRowPK());		
		} else
		if ($status === KontorX_Controller_Action_Helper_Scaffold::NO_EXSISTS) {
			$flashMessenger->addMessage($status);
			/**
			 * Przekierowywanie akcji! 
			 */
			$this->_redirectAction('edit', $scaffold->getRowPK());
		} else {
			$flashMessenger->addMessage($status);
			if ($result instanceof Exception) {
				$flashMessenger->addMessage($result->getMessage());
			}

			$this->view->form = $form;
		}
	}

	/**
	 * @return void
	 */
	public function deleteAction() {
		$model = new $this->_modelClass();
		$dbTable = $model->getDbTable();

		/* @var $scaffold KontorX_Controller_Action_Helper_Scaffold */
		$scaffold = $this->_helper->getHelper('Scaffold');
		$scaffold->setDbTable($dbTable)
				 ->setRowPK($this->_getAllParams());
		
		/* @var $form Zend_Form */
		$form = new $this->_formRemoveClass(array(
			'primaryKey' => $scaffold->getRowPK(),
			'request' => $this->getRequest()
		));

		$scaffold->setForm($form)
				 ->run(KontorX_Controller_Action_Helper_Scaffold::DELETE);

		 /* @var $flashMessenger Zend_Controller_Action_Helper_FlashMessenger */
		$flashMessenger = $this->_helper->getHelper('FlashMessenger');
			
		$status = $scaffold->getStatus();
		$result = $scaffold->getResult();
		if ($status === KontorX_Controller_Action_Helper_Scaffold::SUCCESS) {
			$flashMessenger->addMessage($status);
			
			// uduchamianie powiadomień
			if (null !== $this->_deletePostObservableName) {
				$manager = Promotor_Observable_Manager::getInstance();
				$list = $manager->notify(
					$this->_deletePostObservableName,
					$scaffold->getRowPK(),
					$scaffold->getForm());
	
				$flashMessenger->addMessage($status);
				foreach ($list->getMessages() as $observerName => $messages) {
					$flashMessenger->addMessage(sprintf("%s::%s", $observerName, print_r($messages, true)));
				}
			} else {
				$this->_noticeObserver('post', 
					$scaffold->getRowPK(),
					$scaffold->getForm());
			}
			
			/**
			 * Przekierowywanie akcji! 
			 */
			$this->_redirectAction('delete', $scaffold->getRowPK());
		} else
		if ($status === KontorX_Controller_Action_Helper_Scaffold::NO_EXSISTS) {
			$flashMessenger->addMessage($status);
			/**
			 * Przekierowywanie akcji! 
			 */
			$this->_redirectAction('delete');
		} else {
			$flashMessenger->addMessage($status);
			if ($result instanceof Exception) {
				$flashMessenger->addMessage($result->getMessage());
			}

			$this->view->form = $form;
		}
	}
	
	/**
	 * @param KontorX_DataGrid $grid
	 * @return void
	 */
	protected function _setupDataGridPaginator(KontorX_DataGrid $grid) {
		if ($this->_pagination) {
			$page = $this->_getParam('page',1);
			$count = $this->_getParam('itemCountPerPage', 30);
			$grid->setPagination($page, $count);
		}
	}
}