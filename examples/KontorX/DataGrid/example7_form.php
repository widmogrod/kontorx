<?php
/**
 * Przykład pokazuje:
 * - połączenie DataGrid z Zend_Form
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
$value = array(
array('id' => 'iMac_1',
	  'image' => 'http://images.apple.com/euro/imac/images/overview_hero1_thumb20091027.png'),
array('id' => 'iMac_2',
 	  'image' => 'http://images.apple.com/euro/imac/images/overview_hero2_thumb20091020.png'),
array('id' => 'iMac_3',
	  'image' => 'http://images.apple.com/euro/imac/images/overview_hero3_thumb20091020.png'),
array('id' => 'iMac_4',
	  'image' => 'http://images.apple.com/euro/imac/images/overview_hero4_thumb20091020.png'),
);

$columns = array(
	'image' => array(
		'cell' => array(
			'type' => 'Image'
		)
	),
	'radio' => array(
		'name' => '&nbsp;',
		'cell' => array(
			'type' => 'Editable_FormRadio',
			'options' => array(
				'primaryKey' => array('id')
			)
		)
	)
);

//Zend_Form_Decorator_ViewHelper

require_once 'Zend/Form.php';
$form = new Zend_Form(array(
	'prefixPath' => array(
		'prefix' => 'KontorX_Form_', 
		'path' => 'KontorX/Form'
	),
	'elements' => array(
		'product' => array(
			'type' => 'Text',
			'options' => array(
				'label' => 'Nazwa produktu',
				'description' => 'Nazwa produktu powinna być czytelna',
				'value' => 'iMac'
			)
		),
		'images' => array(
			'type' => 'DataGrid',
			'options' => array(
				'label' => 'Grafika produktu',
				'description' => '↑ Wybierz główną fotografię produktu',
				'columns' => $columns,
				'value' => $value
			)
		),
		'submit' => array(
			'type' => 'Submit',
			'options' => array(
				'label' => 'Wyślij formularz',
			)
		)
	)
));

require_once 'Zend/View.php';
print $form->render(new Zend_View());