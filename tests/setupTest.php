<?php
set_include_path(implode(PATH_SEPARATOR, array(
	'/usr/share/php/Zend/1.7.6/',
	'/usr/share/php/',
	dirname(__FILE__) . '/../'
)));

// simpletest
require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');