<?php
/**
 * Przykład pokazuje:
 * - możliwość rendeowania danych via Ext.data
 * 
 * Wymagania:
 * - ExtJs
 */

// inicjowanie konfiguracji
require_once '../../bootstrap.php';

// Wstawka potrzebana do działania Zend_View_Helper_Url
require_once 'Zend/Controller/Front.php';
$front = Zend_Controller_Front::getInstance();
$front->setBaseUrl($_SERVER['SCRIPT_NAME']);

/* @var $router Zend_Controller_Router_Rewrite */
$router = $front->getRouter();
$router->addDefaultRoutes();

// rekordy
$data = array();

// ustawienie kolumn
$options = array('columns' => array(
	'id' => array(
		'type' => 'ChechboxManager',
		'cell' => array(
			'type' => 'Editable_FormCheckbox',
			'options' => array(
				'primaryKey' => array('urodzony', 'imie')
			)
		)
	),
	'imie' => array(
		'name' => 'Imie',
	),
	'urodzony' => array(
		'name' => 'Data urodzin',
		'type' => 'Order',
	),
	'pochodzenie' => array(
		'name' => 'Pochodzenie',
		'type' => 'Order',
	),
	'akcja' => array(
		'name' => '&nbsp;',
		'cell' => array(
			'type' => 'url',
			'options' => array(
				'primaryKey' => array('urodzony', 'imie'),
				'name' => 'edytuj'
				//'router' => 'default'// wartość podstawowa
				//'target' => '_self', // wartość podstawowa
			)
		)
	)
));

require_once 'KontorX/DataGrid.php';
$grid = KontorX_DataGrid::factory($data, $options);

// Cała sztuczka związan z zmianą sposobu renderowania danychś
require_once 'KontorX/DataGrid/Renderer/ExtGrid/Json.php';
$renderer = new KontorX_DataGrid_Renderer_ExtGrid_Json(array(
	'renderToId' => 'myDivId',
	'url' => 'KontorX/DataGrid/example9_data.php'
));

?>

<div id="myDivId"></div>

<script type="text/javascript">
<!--
<?php print $grid->render($renderer);?>
//-->
</script>