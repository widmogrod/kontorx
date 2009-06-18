<?php
/**
 * @author gabriel
 *
 */
class Promotor_Controller_Action_Scaffold extends Promotor_Controller_Action {

	/**
	 * @var string
	 */
	protected $_modelClass = '';

	/**
	 * @var string
	 */
	protected $_formAddClass = '';

	/**
	 * @var string
	 */
	protected $_formEditClass = '';

	/**
	 * @var string
	 */
	protected $_formRemoveClass = '';
	
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
	 * @return void
	 */
	public function addAction() {
		$model = new $this->_modelClass();
		$dbTable = $model->getDbTable();

		$form = new $this->_formAddClass();
		
		$scaffold = $this->_helper->getHelper('scaffold');
		$scaffold->setDbTable($dbTable)
				 ->setForm($form)
				 ->setSuppressArrayNotation(true);

		if ($this->_hasParam('id')) {
			$scaffold->setRowPK($this->_getAllParams());
		}

		$scaffold->run(KontorX_Controller_Action_Helper_Scaffold::CREATE);

		$flashMessenger = $this->_helper->getHelper('FlashMessenger');
		
		$status = $scaffold->getStatus();
		if ($status === KontorX_Controller_Action_Helper_Scaffold::SUCCESS) {
			$flashMessenger->addMessage($status);
			
			// uduchamianie powiadomień
			if (null !== $this->_addPostObservableName) {
				$manager = Promotor_Observable_Manager::getInstance();
				$list = $manager->notify(
					$this->_addPostObservableName,
					$scaffold->getResult());
	
				$flashMessenger->addMessage($status);
				foreach ($list->getMessages() as $observerName => $messages) {
					// TODO ...
					$flashMessenger->addMessage(sprintf("%s=%s", implode("<br/>", $messages), $observerName));
				}
			}

			$this->_helper->redirector->goTo('add');
		} else {
			$flashMessenger->addMessage($status);
			$this->view->form = $form;
		}
	}

	/**
	 * @return void
	 */
	public function editAction() {
		$model = new $this->_modelClass();
		$dbTable = $model->getDbTable();

		$form = new $this->_formEditClass(array('primaryKey' => $this->_getParam('id')));

		$scaffold = $this->_helper->getHelper('scaffold');
		$scaffold
			->setDbTable($dbTable)
			->setForm($form)
			->setRowPk($this->_getAllParams())
			->run(KontorX_Controller_Action_Helper_Scaffold::UPDATE);

		$flashMessenger = $this->_helper->getHelper('FlashMessenger');
			
		$status = $scaffold->getStatus();
		if ($status === KontorX_Controller_Action_Helper_Scaffold::SUCCESS) {
			$flashMessenger->addMessage($status);

			// uduchamianie powiadomień
			if (null !== $this->_editPostObservableName) {
				$manager = Promotor_Observable_Manager::getInstance();
				$list = $manager->notify(
					$this->_editPostObservableName,
					$scaffold->getRowPK());
	
				$flashMessenger->addMessage($status);
				foreach ($list->getMessages() as $observerName => $messages) {
					// TODO ...
					$flashMessenger->addMessage(sprintf("%s=%s", implode("<br/>", $messages), $observerName));
				}
			}

			$this->_helper->redirector->goTo('edit',null,null,$scaffold->getRowPK());			
		} else
		if ($status === KontorX_Controller_Action_Helper_Scaffold::NO_EXSISTS) {
			$flashMessenger->addMessage($status);
			$this->_helper->redirector->goTo('list');
		} else {
			$flashMessenger->addMessage($status);
			$this->view->form = $form;
		}
	}

	/**
	 * @return void
	 */
	public function deleteAction() {
		$model = new $this->_modelClass();
		$dbTable = $model->getDbTable();

		$form = new $this->_formRemoveClass();

		$scaffold = $this->_helper->getHelper('scaffold');
		$scaffold
			->setDbTable($dbTable)
			->setForm($form)
			->setRowPk($this->_getAllParams())
			->run(KontorX_Controller_Action_Helper_Scaffold::DELETE);

		$flashMessenger = $this->_helper->getHelper('FlashMessenger');
			
		$status = $scaffold->getStatus();
		if ($status === KontorX_Controller_Action_Helper_Scaffold::SUCCESS) {
			$flashMessenger->addMessage($status);
			
			// uduchamianie powiadomień
			if (null !== $this->_deletePostObservableName) {
				$manager = Promotor_Observable_Manager::getInstance();
				$list = $manager->notify(
					$this->_deletePostObservableName,
					$scaffold->getResult());
	
				$flashMessenger->addMessage($status);
				foreach ($list->getMessages() as $observerName => $messages) {
					// TODO ...
					$flashMessenger->addMessage(sprintf("%s=%s", implode("<br/>", $messages), $observerName));
				}
			}
			
			$this->_helper->redirector->goTo('list');
		} else
		if ($status === KontorX_Controller_Action_Helper_Scaffold::NO_EXSISTS) {
			$flashMessenger->addMessage($status);
			$this->_helper->redirector->goTo('list');
		} else {
			$flashMessenger->addMessage($status);
			$this->view->form = $form;
		}
	}
	
	/**
	 * @param KontorX_DataGrid $grid
	 * @return void
	 */
	protected function _setupDataGridPaginator(KontorX_DataGrid $grid) {
		$page = $this->_getParam('page',1);
		$count = $this->_getParam('itemCountPerPage', 30);
		$grid->setPagination($page, $count);
	}
}