<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
ini_set('display_startup_error',1);

set_include_path(implode(PATH_SEPARATOR, array(
	'/usr/share/php/',
	'/usr/share/php/Zend/',
    '/usr/share/php/Zend/trunk/',
	dirname(__FILE__) . '/../'
)));

define('SETUP_TEST',true);

// simpletest
require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');