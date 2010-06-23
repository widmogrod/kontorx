<?php
if (!defined('SETUP_TEST')) {
	require_once '../../setupTest.php';
}

require_once 'KontorX/DataGrid.php';

class KontorX_DataGrid_DataGridTest extends UnitTestCase 
{
	protected $_data = array(
		array(
			'imie' => 'Gabriel',
			'urodzony' => '25-10-1985',
			'pochodzenie' => 'Polska'
		),
		array(
			'imie' => 'Dominika',
			'urodzony' => '25-10-1985',
			'pochodzenie' => 'Polska'
		),
		array(
			'imie' => 'Rafał',
			'urodzony' => '25-10-1985',
			'pochodzenie' => 'Polska'
		),
		array(
			'imie' => 'Gabriel',
			'urodzony' => '11-1-1988',
			'pochodzenie' => 'Polska'
		),
		array(
			'imie' => 'Vladimir',
			'urodzony' => '1-9-1989',
			'pochodzenie' => 'Ukraina'
		)
	);
	
	public function setUp() 
	{
	}
	
	public function tearDown() 
	{
	}

	public function testSetup()
	{
		$dataGrid = KontorX_DataGrid::factory($this->_data, array());
		$this->assertIsA($dataGrid, 'KontorX_DataGrid', "Obiekt nies jest instancją 'KontorX_DataGrid'");
	}
	
	public function testIsCreatedCorrectDataAdapter_Array()
	{
		$dataGrid = KontorX_DataGrid::factory($this->_data, array());
		$adapter = $dataGrid->getAdapter();
		$this->assertIsA($adapter, 'KontorX_DataGrid_Adapter_Array', "Obiekt nies jest instancją 'KontorX_DataGrid_Adapter_Array'");
	}
}

$r = new KontorX_DataGrid_DataGridTest();
$r->run(new TextReporter());
