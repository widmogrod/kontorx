<?php
/**
 * Przykład pokazuje grupowanie rekordów według daty urodzenia
 */

// inicjowanie konfiguracji
require_once '../../bootstrap.php';

// rekordy
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

// ustawienie kolumn
$options = array('columns' => array(
	'imie' => array(
		'name' => 'Imie',
	),
	'urodzony' => array(
		'name' => 'Data urodzin',
		'group' => true // grupowanie włączone w tej kolumnie!
	),
	'pochodzenie' => array(
		'name' => 'Pochodzenie',
	)
));

require_once 'KontorX/DataGrid.php';
$grid = KontorX_DataGrid::factory($data, $options);
print $grid->render();
