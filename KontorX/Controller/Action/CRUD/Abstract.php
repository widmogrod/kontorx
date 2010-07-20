<?php
/**
 * @see KontorX_Controller_Action
 */
require_once 'KontorX/Controller/Action.php';

/**
 * Abstrakcja CRUD
 *
 * TODO Dodać wsparcie dla wielu języków
 *
 * @category 	KontorX
 * @package 	KontorX_Controller_Action
 * @version 	0.2.9
 * @license		GNU GPL
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
 */
abstract class KontorX_Controller_Action_CRUD_Abstract extends KontorX_Controller_Action {

	/**
     * Nazwa klasy modelu
     *
     * @var string
     */
    protected $_modelClass = null;

	/**
	 * Obiekt modelu
         *
	 * @var Zend_Db_Table_Abstract
	 */
    protected $_model = null;
    
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
	 * @Overwrite
	 */
    public function preDispatch() {
        parent::preDispatch();

        // TODO Dodac sprawdzenie poprawnosci url (?!)
        // $referer = (strstr($referer,'://') === false)

        // rezerwowanie sesii
        $session = new Zend_Session_Namespace('KontorX_Controller_Action_CRUD');
        // ustawianie referera w sesii
        $referer = $this->_getParam('referer');
        if (null === $referer) {
            $this->_setParam('referer', $session->referer);
            unset($session->referer);
        } else {
            $session->referer = $referer;
        }
    }
    
    protected $_pluginLoader;

    /**
     * @param Zend_Loader_PluginLoader $loader
     * @return KontorX_Controller_Action_CRUD_Abstract
     */
	public function setPluginLoader(Zend_Loader_PluginLoader $loader) {
    	$this->_pluginLoader = $loader;
    	return $this;
    }
    
	/**
	 * @return Zend_Loader_PluginLoader
	 */
	public function getPluginLoader() {
    	if (null === $this->_pluginLoader) {
    		/* @var Zend_Controller_Request_Http */
    		$rq = $this->getRequest();

    		/* @var Zend_Controller_Front */
    		$fc = $this->getFrontController();

    		$path = $fc->getModuleDirectory($rq->getModuleName());
    		$path += '/modules/';

    		require_once 'Zend/Loader/PluginLoader.php';
    		$this->_pluginLoader = new Zend_Loader_PluginLoader(array($path => ''));
    	}
    	return $this->_pluginLoader;
    }

	/**
	 * Zwraca instancje obiektu modelu
	 *
	 * @return Zend_Db_Table_Abstract
	 */
    protected function _getModel() {
        if (null === $this->_modelClass) {
            require_once 'Zend/Controller/Action/Exception.php';
            $error = 'atrybut _modelClass nie został zdefiniowany';
            throw new Zend_Controller_Action_Exception($error);
        }

        if (null === $this->_model) {
        	if (!class_exists($this->_modelClass)) {
        		/* @var Zend_Controller_Request_Http */
	    		$rq = $this->getRequest();
	
	    		/* @var Zend_Controller_Front */
	    		$fc = $this->getFrontController();
	
	    		$path = $fc->getModuleDirectory($rq->getModuleName());
            	require_once $path . '/models/' . $this->_modelClass . '.php';
        	}
            $this->_model = new $this->_modelClass();
        }
        return $this->_model;
    }

	/**
	 * @param Zend_Db_Table_Abstract $model
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
    protected function _findPrimaryKey(Zend_Db_Table_Abstract $model) {
        $primaryKey = $model->info(Zend_Db_Table::PRIMARY);

        $params = array();
        if (count($primaryKey) > 1) {
            foreach ($primaryKey as $column) {
                $params[] = $this->_getParam($column);
            }
        } else {
            $params[] = $this->_getParam(current($primaryKey));
        }

        return call_user_func_array(array($model,'find'), $params);
    }

	/**
	 * Przygotowanie paginacji
	 *
	 * @param Zend_Db_Table_Select $select
	 */
    protected function _preparePagination(Zend_Db_Table_Select $select) {
        $page = $this->_getParam('page',1);
        $rowCount = $this->_getParam('rowCount',30);

        $select = clone $select;
        $select
        ->reset(Zend_Db_Select::LIMIT_COUNT)
        ->reset(Zend_Db_Select::LIMIT_OFFSET);

        // dlatego clone select zeby nie bylo limitow
        require_once 'Zend/Paginator.php';
        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($rowCount);

        // to view
        $this->view->paginator = $paginator;

        //    	// select referencyjnie
        //    	$select->limitPage($page, $rowCount);
    }

	/**
	 * Listowanie wszystkick rekordow
	 *
	 * TODO Dodac sortowanie
	 */
    public function listAction() {
        try {
            $this->view->rowset = $this->_listFetchAll();
        } catch (Zend_Db_Table_Exception $e) {
            $this->view->rowset = array();
            $this->_listOnException($e);
        }
    }

    /**
     * Uchwyt zwracajacy liste rekordow
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
    protected function _listFetchAll() {
        $model = $this->_getModel();
        return $model->fetchAll();
    }

    /**
     * Uchwyt dla wyjatku podczas nieudanego zapytania
     *
     * Uchwyt dla wyjatku podczas nieudanego zapytania dla
     * operacji wylistowania rekordow
     *
     * @param Zend_Db_Table_Exception $e
     */
    protected function _listOnException(Zend_Db_Table_Exception $e) {
        // logowanie wyjatku
        $logger = Zend_Registry::get('logger');
        $logger->log($e->getMessage() . "\n" . $e->getTraceAsString(), Zend_Log::ERR);
    }

	/**
	 * Utworzenie nowego rekordu
	 *
	 */
    public function addAction() {
        // storzenie formularza
        $form = $this->_addGetForm();

        // TODO W przyszlosci dodac uchwyt generujacy
        // sprawdzanie danych nie tylko post!
        if (!$this->_request->isPost()) {
            $this->_addOnIsPost($form);
            return;
        }

        if (!$this->_addIsValid($form)) {
            $this->_addOnIsNoValid($form);
            return;
        }

        try {
            $row = $this->_addInsert($form);
            
        	// uduchamianie powiadomień
			if (null !== $this->_addPostObservableName) {
				$manager = Promotor_Observable_Manager::getInstance();
				$list = $manager->notify(
					$this->_addPostObservableName,
					$row
				);

				/* @var $flashMessenger Zend_Controller_Action_Helper_FlashMessenger */
				$flashMessenger = $this->_helper->getHelper('FlashMessenger');
				foreach ($list->getMessages() as $observerName => $messages) {
					$message = sprintf('%s=%s', implode('<br />', $messages), $observerName);
					$flashMessenger->addMessage($message);
				}
			}
            
            $this->_addOnSuccess($form, $row);
        } catch (Zend_Db_Table_Row_Exception $e) {
            $this->_addOnException($e, $form);
        } catch (Zend_Db_Table_Abstract $e) {
            $this->_addOnException($e, $form);
        } catch (Zend_Db_Statement_Exception $e) {
            $this->_addOnException($e, $form);
        }
    }

    /**
     * Generuje formularz
     *
     * Generuje formularz dla akcji ADD wykorzystując klase
     * @see KontorX_Form_DbTable wykonujaca w/w zadanie
     *
     * @return Zend_Form
     */
    protected function _addGetForm() {
        $model = $this->_getModel();

        // pobieranie opcji dla generatora formularza
        $options 		= $this->_addGetFormOptions();
        $ignoreColumns 	= $this->_addGetFormDbTableIgnoreColumns();

		/**
		 * @see KontorX_Form_DbTable
		 */
        require_once 'KontorX/Form/DbTable.php';
        /* @var Zend_Form */
        $form = new KontorX_Form_DbTable($model, $options, $ignoreColumns);
        $form->setMethod('post');

        // To taki dodatek odemnie ;)
//        $form->addElement('submit','Dodaj',array('class' =>'action add','ignore' => true,'accesskey'=>'S'));
        $form->addElement('SubmitButton','action', array('ignore' => true,'label'=>'Dodaj'));

        return $form;
    }

    /**
     * Pobiera opcje dla @see Zend_Form
     *
     * @return Zend_Config|array|null
     */
    protected function _addGetFormOptions() {
        // do indywidualnej implementacji
    }

    /**
     * Pobiera opcje dla @see Zend_Form
     *
     * @return array
     */
    protected function _addGetFormDbTableIgnoreColumns() {
        return array();
    }

    /**
     * Uchwyt dla akcji przed wysłaniem danych POST
     *
     * @param Zend_Form $form
     */
    protected function _addOnIsPost(Zend_Form $form) {
        $this->view->form = $form;
    }

	/**
     * Uchwyt akcji walidujacej formularz
     *
     * @return bool
     */
    protected function _addIsValid(Zend_Form $form) {
        return $form->isValid($this->_request->getPost());
    }

	/**
     * Uchwyt dla akcji gdy dane sa niepoprawne
     *
     * @param Zend_Form $form
     */
    protected function _addOnIsNoValid(Zend_Form $form) {
        $this->view->form = $form;
    }

    /**
     * Przygotowanie danych do insert
     *
     * @param Zend_Form $form
     * @param Zend_Db_Table_Row_Abstract $row
     * @return array
     */
    protected function _addPrepareData(Zend_Form $form) {
        require_once 'KontorX/Filter/MagicQuotes.php';
        $f = new KontorX_Filter_MagicQuotes();
        return $f->filter($form->getValues());
    }

    /**
     * Uchwyt tworzacy nowy rekord
     *
     * @param Zend_Form $form
     * @return Zend_Db_Table_Row_Abstract
     */
    protected function _addInsert(Zend_Form $form) {
        $data = $this->_addPrepareData($form);

        // dodawanie rekordu
        $model = $this->_getModel();
        $row = $model->createRow($data);
        $row->save();

        return $row;
    }

    /**
     * Uchwyt akcji po sukcesie w utworzeniu rekordu
     *
     * @param Zend_Form $form
     * @param Zend_Db_Table_Row_Abstract $row
     */
    protected function _addOnSuccess(Zend_Form $form, Zend_Db_Table_Row_Abstract $row) {
        // tworzenie komunikatu
        $message = 'Rekord został dodany';
        $this->_helper->flashMessenger->addMessage($message);

        $referer = $this->_getParam('referer');
        if (null !== $referer) {
            $this->_helper->redirector->goToUrlAndExit($referer);
        } else {
            $this->_helper->redirector->goToUrlAndExit($this->_helper->url->url(array()));
        }
    }

	/**
     * Uchwyt dla wyjatku podczas nieudanego zapytania
     *
     * Uchwyt dla wyjatku podczas nieudanego zapytania dla
     * operacji tworzenia rekordu
     *
     * @param Zend_Db_Table_Exception $e
     */
    protected function _addOnException(Zend_Exception $e, Zend_Form $form, Zend_Db_Table_Row_Abstract $row = null) {
        // logowanie wyjatku
        $logger = Zend_Registry::get('logger');
        $logger->log(get_class($e) . ' :: ' . $e->getMessage() . "\n" . $e->getTraceAsString(), Zend_Log::ERR);

        // tworzenie komunikatu
        $message = 'Rekord nie został dodany';

        // rendereowanie widoku
        $this->view->messages = array($message);
        $this->view->form = $form->render();

        // refirect
        $this->_helper->flashMessenger->addMessage($message);
        //		$this->_helper->redirector->goToAndExit('add');
    }

	/**
	 * Educja rekordu
	 *
	 */
    public function editAction() 
    {
        // wyszukanie rekordu do edycji
        try {
            $row = $this->_editFindRecord();
        } catch (Zend_Db_Table_Exception $e) {
            $row = $this->_editFindOnException($e);
        }

        // czy rekord istnieje
        if (!$row instanceof Zend_Db_Table_Row_Abstract)
        {
            $this->_editOnRecordNoExsists();
            return;
        }

        $this->view->row = $row;

        // storzenie formularza
        $form = $this->_editGetForm($row);

        // TODO W przyszlosci dodac uchwyt generujacy
        // sprawdzanie danych nie tylko post!
        if (!$this->_request->isPost()) {
            $this->_editOnIsPost($form, $row);
            return;
        }

        if (!$this->_editIsValid($form, $row)) {
            $this->_editOnIsNoValid($form, $row);
            return;
        }

        try {
            $this->_editUpdate($form, $row);
            
        	// uduchamianie powiadomień
			if (null !== $this->_editPostObservableName) {
				$manager = Promotor_Observable_Manager::getInstance();
				$list = $manager->notify(
					$this->_editPostObservableName,
					$row
				);

				/* @var $flashMessenger Zend_Controller_Action_Helper_FlashMessenger */
				$flashMessenger = $this->_helper->getHelper('FlashMessenger');
				foreach ($list->getMessages() as $observerName => $messages) {
					$message = sprintf('%s=%s', implode('<br />', $messages), $observerName);
					$flashMessenger->addMessage($message);
				}
			}
            
            $this->_editOnSuccess($form, $row);
        } catch (Zend_Db_Table_Row_Exception $e) {
            $this->_editOnException($e, $form);
        } catch (Zend_Db_Table_Abstract $e) {
            $this->_editOnException($e, $form);
        } catch (Zend_Db_Statement_Exception $e) {
            $this->_editOnException($e, $form);
        } catch(Exception $e) {
        	$this->_editOnException($e, $form);
        }
    }

	/**
     * Generuje formularz
     *
     * Generuje formularz dla akcji EDIT wykorzystując klase
     * @see KontorX_Form_DbTable wykonujaca w/w zadanie
     *
     * @return Zend_Form
     */
    protected function _editGetForm(Zend_Db_Table_Row_Abstract $row) {
        $model = $this->_getModel();

        // pobieranie opcji dla generatora formularza
        $options        = $this->_editGetFormOptions();
        $ignoreColumns 	= $this->_editGetFormDbTableIgnoreColumns();

	/**
        	 * @see KontorX_Form_DbTable
        	 */
        require_once 'KontorX/Form/DbTable.php';
        $form = new KontorX_Form_DbTable($model, $options, $ignoreColumns);
        $form->setMethod('post');

        // To taki dodatek odemnie ;)
//        $form->addElement('submit','Edytuj',array('class' =>'action edit','ignore' => true,'accesskey'=>'S'));
        $form->addElement('SubmitButton','action', array('ignore' => true,'label'=>'Edytuj'));

        return $form;
    }

	/**
     * Pobiera opcje dla @see Zend_Form
     *
     * @return Zend_Config|array|null
     */
    protected function _editGetFormOptions() {
        // do indywidualnej implementacji
    }

    /**
     * Pobiera opcje dla @see Zend_Form
     *
     * @return array
     */
    protected function _editGetFormDbTableIgnoreColumns() {
        return array();
    }

    /**
     * Uchwyt dla akcji wyszukania rekordu
     *
     * @return Zend_Db_Table_Row_Abstract|bool
     */
    protected function _editFindRecord() {
        $model = $this->_getModel();
        return $this->_findPrimaryKey($model)->current();
    }

    /**
     * Uchwyt dla wyjatku podczas nieudanego zapytania
     *
     * Uchwyt dla wyjatku podczas nieudanego zapytania dla
     * operacji wyszukania rekordu
     *
     * @param Zend_Db_Table_Exception $e
     * @return bool
     */
    protected function _editFindOnException(Zend_Exception $e) {
        // logowanie wyjatku
        $logger = Zend_Registry::get('logger');
        $logger->log($e->getMessage() . "\n" . $e->getTraceAsString(), Zend_Log::ERR);
        return false;
    }

    /**
     * Uchwyt dla akcji gdy nie zostanie znaleziony rekord o edycji
     *
     */
    protected function _editOnRecordNoExsists() {
        $message = 'Rekord o podanym kluczu głównym nie istnieje w systemie';
        $this->_helper->flashMessenger->addMessage($message);
        $this->_helper->redirector->goToUrlAndExit(getenv('HTTP_REFERER'));
    }

    /**
     * Uchwyt dla akcji przed wysłaniem danych POST
     *
     * @param Zend_Form $form
     * @param Zend_Db_Table_Row_Abstract
     */
    protected function _editOnIsPost(Zend_Form $form, Zend_Db_Table_Row_Abstract $row) {
        $form->setDefaults($row->toArray());
        $this->view->form = $form->render();
    }

	/**
     * Uchwyt akcji walidujacej formularz
     *
     * @return bool
     */
    protected function _editIsValid(Zend_Form $form, Zend_Db_Table_Row_Abstract $row) {
        return $form->isValid($this->_request->getPost());
    }

	/**
     * Uchwyt dla akcji gdy dane sa niepoprawne
     *
     * @param Zend_Form $form
     * @param Zend_Db_Table_Row_Abstract
     */
    protected function _editOnIsNoValid(Zend_Form $form, Zend_Db_Table_Row_Abstract $row) {
        $this->view->form = $form->render();
    }

    /**
     * Przygotowanie danych do update
     *
     * @param Zend_Form $form
     * @param Zend_Db_Table_Row_Abstract $row
     * @return array
     */
    protected function _editPrepareData(Zend_Form $form, Zend_Db_Table_Row_Abstract $row) {
        require_once 'KontorX/Filter/MagicQuotes.php';
        $f = new KontorX_Filter_MagicQuotes();
        return $f->filter($form->getValues());
    }

    /**
     * Uchwyt edytujacy rekord
     *
     * @param Zend_Form $form
     * @param Zend_Db_Table_Row_Abstract
     */
    protected function _editUpdate(Zend_Form $form, Zend_Db_Table_Row_Abstract $row) {
        $data = $this->_editPrepareData($form, $row);

        // aktualizacja rekordu
        $row->setFromArray($data);
        $row->save();
    }

	/**
     * Uchwyt akcji po sukcesie w aktualizacji rekordu
     *
     * @param Zend_Form $form
     * @param Zend_Db_Table_Row_Abstract $row
     */
    protected function _editOnSuccess(Zend_Form $form, Zend_Db_Table_Row_Abstract $row) {
        $primaryKey = $this->_getParam('id');

        // tworzenie komunikatu
        $message = 'Rekord został zedytowany';
        $this->_helper->flashMessenger->addMessage($message);

        $referer = $this->_getParam('referer');
        if (null != $referer)
        {
            $this->_helper->redirector->goToUrlAndExit($referer);
        } else {
            // tworzenie parametrow przekierowania
            // TESTUJE! (i obecnie działa OK)
            $model = $this->_getModel();
            $primaryKey = $model->info(Zend_Db_Table::PRIMARY);

            $params = array();
            foreach ($primaryKey as $column) {
                $params[$column] = $form->getValue($column);
            }
            
            $this->_helper->redirector->goToUrlAndExit($_SERVER['HTTP_REFERER']);
            
//            $this->_helper->redirector->goToUrlAndExit(
//                    $this->_helper->url->url($params)
//            );
        }
    }

    /**
     * Uchwyt dla wyjatku podczas nieudanego zapytania
     *
     * Uchwyt dla wyjatku podczas nieudanego zapytania dla
     * operacji aktualizacji rekordu
     *
     * @param Zend_Db_Table_Exception $e
     */
    protected function _editOnException(Zend_Exception $e, Zend_Form $form) {
        // logowanie wyjatku
        $logger = Zend_Registry::get('logger');
        $logger->log($e->getMessage() . "\n" . $e->getTraceAsString(), Zend_Log::ERR);

        $primaryKey = $this->_getParam('id');

        $message = 'Rekord nie został zedytowany';
        $this->_helper->flashMessenger->addMessage($message);
        $this->_helper->redirector->goToAndExit('edit',null,null,array('id'=>$primaryKey));
    }

    /**
     * Kasowanie rekordu
     *
     * TODO Dodać obsluge widoku
     * TODO Możliwość wylaczenie/ wlaczenie widoku
     * na podstawie parametru format
     */
    public function deleteAction() {
        // wyszukanie rekordu do edycji
        try {
            $row = $this->_deleteFindRecord();
        } catch (Zend_Db_Table_Exception $e) {
            $row = $this->_editFindOnException($e);
        }

        // czy rekord istnieje
        if (false === $row || null === $row) {
            $this->_deleteOnRecordNoExsists();
            return;
        }

        try {
            $this->_deleteDelete($row);
            
        	// uduchamianie powiadomień
			if (null !== $this->_deletePostObservableName) {
				$manager = Promotor_Observable_Manager::getInstance();
				$list = $manager->notify(
					$this->_deletePostObservableName,
					$row
				);

				/* @var $flashMessenger Zend_Controller_Action_Helper_FlashMessenger */
				$flashMessenger = $this->_helper->getHelper('FlashMessenger');
				foreach ($list->getMessages() as $observerName => $messages) {
					$message = sprintf('%s=%s', implode('<br />', $messages), $observerName);
					$flashMessenger->addMessage($message);
				}
			}
            
            $this->_deleteOnSuccess($row);
        } catch (Zend_Db_Table_Row_Exception $e) {
            $this->_deleteOnException($e);
        } catch (Zend_Db_Table_Exception $e) {
            $this->_deleteOnException($e);
        } catch (Zend_Db_Statement_Exception $e) {
            $this->_deleteOnException($e);
        }
    }

    /**
     * Uchwyt dla akcji wyszukania rekordu
     *
     * @return Zend_Db_Table_Row_Abstract|bool
     */
    protected function _deleteFindRecord() {
        $model = $this->_getModel();
        return $this->_findPrimaryKey($model)->current();
    }

    /**
     * Uchwyt dla akcji gdy nie zostanie znaleziony rekord
     *
     */
    protected function _deleteOnRecordNoExsists() {
        $message = 'Rekord o podanym kluczu głównym nie istnieje w systemie';
        $this->_helper->flashMessenger->addMessage($message);
        $this->_helper->redirector->goToUrlAndExit(getenv('HTTP_REFERER'));
    }

    /**
     * Uchwyt akcji podczas DELETE
     *
     */
    protected function _deleteDelete(Zend_Db_Table_Row_Abstract $row) {
        // wylaczenie widoku
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        // usuniecie rekordu
        $row->delete();
    }

    /**
     * Uchwyt akcji po sukcesie po skasowaniu rekordu
     *
     * @param Zend_Db_Table_Row_Abstract $row
     */
    protected function _deleteOnSuccess(Zend_Db_Table_Row_Abstract $row) {
        // tworzenie komunikatu
        $message = 'Rekord został usunięty';
        $this->_helper->flashMessenger->addMessage($message);
        $this->_helper->redirector->goToUrlAndExit(getenv('HTTP_REFERER'));
    }

    /**
     * Uchwyt dla wyjatku podczas nieudanego zapytania
     *
     * Uchwyt dla wyjatku podczas nieudanego zapytania dla
     * operacji aktualizacji rekordu
     *
     * @param Zend_Db_Table_Exception $e
     */
    protected function _deleteOnException(Zend_Exception $e) {
        // logowanie wyjatku
        $logger = Zend_Registry::get('logger');
        $logger->log($e->getMessage() . "\n" . $e->getTraceAsString(), Zend_Log::ERR);

        // tworzenie komunikatu
        $message = 'Rekord nie został usunięty';
        $this->_helper->flashMessenger->addMessage($message);
        $this->_helper->flashMessenger->addMessage($e->getMessage() . "\n" . $e->getTraceAsString());
        $this->_helper->redirector->goToUrlAndExit(getenv('HTTP_REFERER'));
    }

    ////////////////////////////////////////////////////

    /**
     * Modyfikuj w zakresie BOOLEAN (prawda-fałsz)
     */
    const MODIFY_BOOL = 'modify_bool';

    /**
     * Modyfikuj w zakresie STRING (prawda-fałsz)
     */
    const MODIFY_STRING = 'modify_string';

    /**
     * Dozwolone modyfikatory
     *
     * @var unknown_type
     */
    protected $_allowModifications = array(
        self::MODIFY_BOOL,
        self::MODIFY_STRING
    );

    /**
     * Przechowuje regóły modyfikacji rekordów
     *
     * @var array
     */
    protected $_modificationRules = array();

    /**
     * Prefix kluczy, które mogą być modyfikowane
     *
     * @var string
     */
    protected $_modificationPrefix = 'm_';

    /**
     * Przechowuje dane do modyfikacji
     *
     * @var unknown_type
     */
    protected $_modificationData = array();

    /**
     * Dodaje modyfikator dla pola klucza danych (!? ;])
     *
     * @param string $field
     * @param string $rule
     * @param array $params
     */
    protected function _addModificationRule($field, $rule, array $params = array()) {
        // przygotowanie nazwy pola
        $field = strtolower($field);
        // czy regóła modyfikacji jest dozwolona
        if (!in_array($rule, $this->_allowModifications)) {
            $message = "Modification rule `$rule` do not exsists";
            require_once 'Zend/Controller/Action/Exception.php';
            throw new Zend_Controller_Action_Exception($message);
        }
        // czy już nie została ustawiona regóła modyfikacji
        if (array_key_exists($field, $this->_modificationRules)) {
            $message = "Field `$field` has modification rule, use _removeModificationRule()";
            require_once 'Zend/Controller/Action/Exception.php';
            throw new Zend_Controller_Action_Exception($message);
        }
        $this->_modificationRules[$field] = $rule;
    }

    /**
     * Usówa modyfikator dla pola
     *
     * @param string $field
     */
    protected function _removeModificationRule($field) {
        if (array_key_exists($field, $this->_modificationRules)) {
            unset($this->_modificationRules[$field]);
        }
    }

	/**
	 * Czyści modyfikatory
	 *
	 */
    protected function _cleanModificationRules() {
        $this->_modificationRules = null;
        $this->_modificationRules = array();
    }

    /**
     * Zwraca wartośc do modyfikacji
     *
     * @param string $field
     * @param mixed $default
     * @return mixed
     */
    protected function _getModificationValue($field, $default = null) {
        return array_key_exists($field, $this->_modificationData)
        ? $this->_modificationData[$field]
        : $default;
    }

    /**
     * Zwraca dane do modyfikacji
     *
     * @return array
     */
    protected function _getModificationData() {
        return $this->_modificationData;
    }

    /**
     * Ustawia dane do modyfikacji
     *
     * @param array $data
     * @param array $keys
     */
    protected function _setModifcationData(array $data, array $keys) {
        if (empty($data)) {
            return;
        }

        // wyszukanie danych do modyfikacji
        $data = $this->_findModificationData($data);
        // tylko dane, które są też kluczami rekordu
        $data = array_intersect_key($data, $keys);

        $this->_modificationData = $data;
    }

    /**
     * Wyszukuje dane do modyfikacji
     *
     * @param array $data
     * @return array
     */
    protected function _findModificationData(array $data) {
        $result = array();
        $prefixLenght = strlen($this->_modificationPrefix);

        // wyszukwanie danych, ktore są do modyfikacji
        foreach ($data as $key => $value) {
            $keyPrefix = substr($key, 0, $prefixLenght);
            // czy wlasciwy prefix ?
            if ($this->_modificationPrefix !== $keyPrefix) {
                continue;
            }
            $field = substr($key, $prefixLenght);
            // czy pole jest obslugiwane
            if ($this->_isValidModificationValue($field, $value)) {
                $result[$field] = $value;
            }
        }

        return $result;
    }

    protected function _isValidModificationValue($field, &$value) {
        // nie istnieje regula modyfikacji zatem pole jest wylkuczona
        if (!array_key_exists($field, $this->_modificationRules)) {
            $message = "Field `$field` has not modification rule";
            require_once 'Zend/Controller/Action/Exception.php';
            throw new Zend_Controller_Action_Exception($message);
        }

        $return = false;

        $rule = $this->_modificationRules[$field];
        switch ($rule) {
            case self::MODIFY_BOOL:
                if (is_bool($value)) {
                    $return = true;
                } else
                if (is_numeric($value)) {
                    $return = true;
                    $value = (bool) $value;
                } else
                if (in_array($value, array('true','on'))) {
                    $return = true;
                    $value  = true;
                } else
                if (in_array($value, array('false','off'))) {
                    $return = true;
                    $value  = false;
                }
                break;
    }

    return $return;
}

	/**
	 * Modyfikuje wartoś(ć|ci) klucz(a|y) rekord(u|ów)
	 *
	 * TODO Dodać obsługę widoku
	 * TODO Dodać wybur widoku za pomoca parametru
	 * T\ODO Dodać definiowanie jakie parametry kolumna moze przyjmować
	 */
public function modifyAction() {
    // wyszukanie rekordu do edycji
    try {
        $row = $this->_modifyFindRecord();
    } catch (Zend_Db_Table_Exception $e) {
        $row = $this->_modifyFindOnException($e);
    }

    // czy rekord istnieje
    if (false === $row || null === $row) {
        $this->_modifyOnRecordNoExsists();
        return;
    }

    $this->view->row = $row;

    // inicjowanie daych do modyfikacji
    $this->_modifyInit();

    // ustawienie danych do modyfikacji
    $data = $this->_modifyGetData();
    $this->_setModifcationData($data, $row->toArray());

    if (!$this->_modifyIsValid($row)) {
        $this->_modifyOnIsNoValid($row);
        return;
    }

    try {
        $this->_modifyUpdate($row);
        $this->_modifyOnSuccess($row);
    } catch (Zend_Db_Table_Row_Exception $e) {
        $this->_modifyOnException($e);
    } catch (Zend_Db_Table_Abstract $e) {
        $this->_modifyOnException($e);
    } catch (Zend_Db_Statement_Exception $e) {
        $this->_modifyOnException($e);
    }
}

    /**
     * Uchwyt dla akcji wyszukania rekordu
     *
     * @return Zend_Db_Table_Row_Abstract|bool
     */
protected function _modifyFindRecord() {
    $model = $this->_getModel();
    return $this->_findPrimaryKey($model)->current();
}

    /**
     * Uchwyt dla wyjatku podczas nieudanego zapytania
     *
     * Uchwyt dla wyjatku podczas nieudanego zapytania dla
     * operacji modyfikacji rekordu
     *
     * @param Zend_Db_Table_Exception $e
     * @return bool
     */
protected function _modifyFindOnException(Zend_Exception $e) {
    // logowanie wyjatku
    $logger = Zend_Registry::get('logger');
    $logger->log($e->getMessage() . "\n" . $e->getTraceAsString(), Zend_Log::ERR);
    return false;
}

    /**
     * Uchwyt dla akcji gdy nie zostanie znaleziony rekord o edycji
     *
     */
protected function _modifyOnRecordNoExsists() {
    $message = 'Rekord o podanym kluczu głównym nie istnieje w systemie';
    $this->_helper->flashMessenger->addMessage($message);
    $this->_helper->redirector->goToUrlAndExit(getenv('HTTP_REFERER'));
}

    /**
     * Inicjowanie informacji o modyfikacjach
     *
     */
protected function _modifyInit() {

}

    /**
     * Zwraca dane modyfikacyjne
     *
     * @param Zend_Db_Table_Row_Abstract $row
     * @return array
     */
protected function _modifyGetData() {
    return $this->_getAllParams();
}

	/**
     * Uchwyt akcji walidujacej danych
     *
     * @return bool
     */
protected function _modifyIsValid(Zend_Db_Table_Row_Abstract $row) {
    $data = $this->_getModificationData();
    return !empty($data);
}

	/**
     * Uchwyt dla akcji gdy dane sa niepoprawne
     *
     * @param Zend_Db_Table_Row_Abstract
     */
protected function _modifyOnIsNoValid(Zend_Db_Table_Row_Abstract $row) {
    $message = 'Modyfikowane dane są niepoprawne';
    $this->_helper->flashMessenger->addMessage($message);
    $this->_helper->redirector->goToUrlAndExit(getenv('HTTP_REFERER'));
}

    /**
     * Przygotowanie danych do update
     *
     * @param Zend_Db_Table_Row_Abstract $row
     * @return array
     */
protected function _modifyPrepareData(Zend_Db_Table_Row_Abstract $row) {
    // parsowanie danych
    $data = $this->_getModificationData();
    require_once 'KontorX/Filter/MagicQuotes.php';
    $f = new KontorX_Filter_MagicQuotes();
    $data = $f->filter($data);
    return $data;
}

    /**
     * Uchwyt modyfikacji rekord
     *
     * @param Zend_Form $form
     * @param Zend_Db_Table_Row_Abstract
     */
protected function _modifyUpdate(Zend_Db_Table_Row_Abstract $row) {
    $data = $this->_modifyPrepareData($row);

    // aktualizacja rekordu
    $row->setFromArray($data);
    $row->save();
}

	/**
     * Uchwyt akcji po sukcesie w modyfikacji rekordu
     *
     * @param Zend_Form $form
     * @param Zend_Db_Table_Row_Abstract $row
     */
protected function _modifyOnSuccess(Zend_Db_Table_Row_Abstract $row) {
    $primaryKey = $this->_getParam('id');

    // tworzenie komunikatu
    $message = 'Rekord został zmodyfikowany';
    $this->_helper->flashMessenger->addMessage($message);
    $this->_helper->redirector->goToUrlAndExit(getenv('HTTP_REFERER'));
}

	/**
     * Uchwyt dla wyjatku podczas nieudanego zapytania
     *
     * Uchwyt dla wyjatku podczas nieudanego zapytania dla
     * operacji modyfikacji rekordu
     *
     * @param Zend_Db_Table_Exception $e
     */
protected function _modifyOnException(Zend_Exception $e) {
    // logowanie wyjatku
    $logger = Zend_Registry::get('logger');
    $logger->log($e->getMessage() . "\n" . $e->getTraceAsString(), Zend_Log::ERR);

    $primaryKey = $this->_getParam('id');

    $message = 'Rekord nie został zmodyfikowany';
    $this->_helper->flashMessenger->addMessage($message);
    $this->_helper->redirector->goToUrlAndExit(getenv('HTTP_REFERER'));
}
}
