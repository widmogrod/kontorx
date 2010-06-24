<?php
/**
 * Przykład pokazuje najprostrzy sposób wygenerowanie tabeli wyników
 */

//inicjowanie konfiguracji
require_once '../../bootstrap.php';

$data = array(
	array('imie' => 'Gabriel',
		'urodzony' => '25-10-1985',
		'pochodzenie' => 'Polska'),
	array('imie' => 'Dominika',
		'urodzony' => '25-10-1985',
		'pochodzenie' => 'Polska'),
	array('imie' => 'Rafał',
		'urodzony' => '25-10-1985',
		'pochodzenie' => 'Polska'),
	array('imie' => 'Gabriel',
		'urodzony' => '11-1-1988',
		'pochodzenie' => 'Polska'),
	array('imie' => 'Vladimir',
		'urodzony' => '1-9-1989',
		'pochodzenie' => 'Ukraina')
);

require_once 'KontorX/DataGrid.php';
$grid = KontorX_DataGrid::factory($data);
print $grid->render();