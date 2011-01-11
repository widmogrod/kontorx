<?php
date_default_timezone_set('Europe/Warsaw');

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/example10'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'default'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    '/usr/share/php/KontorX/trunk',
    '/usr/share/php/Zend/trunk',
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/application.ini'
);

# inicjowanie Doctrine

$application->bootstrap('Doctrine');

# potrzebne tylko w momęcie instalacji Doctrine

//$application->getBootstrap()->getPluginResource('Doctrine')->runCli();
//die;

# potrzebne w momęcie działania aplikacji

$application->bootstrap('Router');
/* @var $router Zend_Controller_Router_Rewrite */
$router = $application->getBootstrap()->getPluginResource('Router')->getRouter();
$router->addDefaultRoutes();

# dodawanie rekordów
//$collection = Doctrine_Collection::create('User');
//$collection[0]->username = 'Gabriel';
//$collection[0]->password = 'SuperTajne!';
//$collection[0]->email    = 'test1@email.com';
//
//$collection[1]->username = 'Joanna';
//$collection[1]->password = 'SuperTajne!';
//$collection[1]->email    = 'test2@email.com';
//
//$collection[2]->username = 'Grzegorz';
//$collection[2]->password = 'SuperTajne!';
//$collection[2]->email    = 'test3@email.com';
//
//$collection[3]->username = 'Dominika';
//$collection[3]->password = 'SuperTajne!';
//$collection[3]->email    = 'test4@email.com';
//
//$collection[4]->username = 'Mateusz';
//$collection[4]->password = 'SuperTajne!';
//$collection[4]->email    = 'test5@email.com';
//
//$collection[5]->username = 'Wladimir';
//$collection[5]->password = 'SuperTajne!';
//$collection[5]->email    = 'test6@email.com';
//$collection->save();
//
//$collectionGroup = Doctrine_Collection::create('Group');
//$collectionGroup[0]->name = 'Pierwsza grupa';
//$collectionGroup[1]->name = 'Druga grupa';
//$collectionGroup[2]->name = 'Trzecia grupa';
//$collectionGroup[3]->name = 'Czwarta grupa';
//$collectionGroup->save();
//
//$collection[0]->UserGroup[0]->fk_group_id = $collectionGroup[0]->group_id;
////$collection[0]->UserGroup[1]->fk_group_id = $collectionGroup[1]->group_id;
//
//$collection[1]->UserGroup[0]->fk_group_id = $collectionGroup[2]->group_id;
//$collection[2]->UserGroup[0]->fk_group_id = $collectionGroup[2]->group_id;
//$collection[3]->UserGroup[0]->fk_group_id = $collectionGroup[2]->group_id;
//
//$collection[3]->UserGroup[0]->fk_group_id = $collectionGroup[1]->group_id;
//$collection[4]->UserGroup[0]->fk_group_id = $collectionGroup[2]->group_id;
//$collection[5]->UserGroup[0]->fk_group_id = $collectionGroup[3]->group_id;
//$collection->save();

$query = Doctrine_Query::create();
$query->select('u.username, u.email, g.name')
	  ->from('User u')
	  ->innerJoin('u.UserGroup ug')
	  ->innerJoin('ug.Group g');

$array = $query->execute(array(), Doctrine::HYDRATE_SCALAR);

// ustawienie kolumn
$options = array('columns' => array(
    'u_username' => array(
        'name' => 'Imie',
		'type' => 'Order'
    ),
    'u_email' => array(
        'name' => 'Email',
    	'type' => 'Order'
    ),
    'g_name' => array(
        'name' => 'Gupa',
    	'group' => true, // grupowanie włączone w tej kolumnie!
    	'order' => 'desc'
    )
));

$grid = KontorX_DataGrid::factory($array, $options);
$grid->setValues($_GET);
echo $grid->render();