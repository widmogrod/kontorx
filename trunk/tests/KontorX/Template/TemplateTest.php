<?php
if (!defined('SETUP_TEST')) {
	require_once '../../setupTest.php';
}

/**
 * @see KontorX_Template 
 */
require_once 'KontorX/Template.php';

class KontorX_Template_TemplateTest extends UnitTestCase {
	/**
	 * @var KontorX_Template
	 */
	protected $_template = null;

	public function setUp() {
		/**
		 * Zeby normalnie testować należy zakomentowac w klasie KontorX_Template
		 * linie z __construct():
		 * //$this->_initPlugin();
		 * //$this->_initHelper();
		 */
		$this->_template = KontorX_Template::getInstance();
	}
	
	public function tearDown() {
		$this->_template = null;
	}

	public function testOptions() {
		$options = array(
			'themeConfigFilename' => 'ThemeConfigName',
			'allowThemeConfig' => false
		);

		try {
			$this->_template->setOptions($options);
		} catch (KontorX_Template_Exception $e) {}
			
		$this->assertEqual(
			$this->_template->getStyleDirName(),
			'styles',
			sprintf('Opcja "%s" nie jest taka sama jak ustawiona','styles'));
		
		$this->assertEqual(
			$this->_template->getThemeConfigFilename(),
			$options['themeConfigFilename'],
			sprintf('Opcja "%s" nie jest taka sama jak ustawiona','themeConfigName'));

		$this->assertEqual(
			$this->_template->isAllowedThemeConfig(),
			$options['allowThemeConfig'],
			sprintf('Opcja "%s" nie jest taka sama jak ustawiona',$options['allowThemeConfig']));
	}

	public function testGetTemplatePath() {
		$options = array(
			'templatePath' => 'source/',
			'allowThemeConfig' => false
		);

		$this->_template->setOptions($options);

		$this->_template->setThemeName('xp');
		$this->_template->setStyleName('orange');
		
		$templatePath = 'source/xp/styles/orange/';
		
		$this->assertEqual(
			$t = $this->_template->getTemplatePath(true),
			$templatePath,
			sprintf('Ścieżka do szablonu "%s" jest inna niż oczekiwana "%s"',$t, $templatePath));
	}

	public function testConfigIni() {
		$options = array(
			'templatePath' => 'source/',
			'themeConfigFilename' => 'config.ini',
			'allowThemeConfig' => false
		);
		
		$this->_template->setOptions($options);
		$result = $this->_template->getThemeConfig();
		
		$this->assertTrue(
			$result instanceof Zend_Config,
			'configuracja nie jest typu "Zend_Config"');
	}
	
	public function testConfigXml() {
		$options = array(
			'templatePath' => 'source/',
			'themeConfigFilename' => 'config.xml',
			'allowThemeConfig' => false
		);
		
		$this->_template->setOptions($options);
		$result = $this->_template->getThemeConfig();
		
		$this->assertTrue(
			$result instanceof Zend_Config,
			'configuracja nie jest typu "Zend_Config"');
	}
	
	public function testConfigPhp() {
		$options = array(
			'templatePath' => 'source/',
			'themeConfigFilename' => 'config.php',
			'allowThemeConfig' => false
		);
		
		$this->_template->setOptions($options);
		$result = $this->_template->getThemeConfig();
		
		$this->assertTrue(
			$result instanceof Zend_Config,
			'configuracja nie jest typu "Zend_Config"');
	}
}

$r = new KontorX_Template_TemplateTest();
$r->run(new TextReporter());