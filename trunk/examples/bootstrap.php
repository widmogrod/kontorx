<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
ini_set('display_startup_error',1);

set_include_path(implode(PATH_SEPARATOR, array(
	'/usr/share/php/Zend/1.10.4/',
	'/usr/share/php/',
	dirname(__FILE__) . '/../' // KontroX
)));