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

		$status = $scaffold->getStatus();
		if ($status === KontorX_Controller_Action_Helper_Scaffold::SUCCESS) {
			$this->_helper->flashMessenger($status);
			$this->_helper->redirector->goTo('add');
		} else {
			$this->_helper->flashMessenger($status);
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

		$status = $scaffold->getStatus();
		if ($status === KontorX_Controller_Action_Helper_Scaffold::SUCCESS) {
			$this->_helper->flashMessenger($status);
			$this->_helper->redirector->goTo('edit',null,null,$scaffold->getRowPK());			
		} else
		if ($status === KontorX_Controller_Action_Helper_Scaffold::NO_EXSISTS) {
			$this->_helper->flashMessenger($status);
			$this->_helper->redirector->goTo('list');
		} else {
			$this->_helper->flashMessenger($status);
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

		$status = $scaffold->getStatus();
		if ($status === KontorX_Controller_Action_Helper_Scaffold::SUCCESS) {
			$this->_helper->flashMessenger($status);
			$this->_helper->redirector->goTo('list');
		} else
		if ($status === KontorX_Controller_Action_Helper_Scaffold::NO_EXSISTS) {
			$this->_helper->flashMessenger($status);
			$this->_helper->redirector->goTo('list');
		} else {
			$this->_helper->flashMessenger($status);
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