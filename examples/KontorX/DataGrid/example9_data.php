<?php
/**
 * Generowanie danych testowych
 */

// inicjowanie konfiguracji
require_once '../../bootstrap.php';

// rekordy
$data = array();

for($i = 0; $i < 1000; $i++)
{
	$data[] = array(
		'id' => $i,
		'imie' => 'Gabriel_'.$i,
		'urodzony' => '25-10-1985',
		'pochodzenie' => 'Polska_'.$i);
}

require_once 'Zend/Json.php';
echo Zend_Json::encode(array(
	'success' => true,
	'rowset' => $data
));