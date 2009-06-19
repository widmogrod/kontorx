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
			'styleConfigFilename' => 'styleConfigFilename',
			'allowedStyleConfig' => false
		);

		try {
			$this->_template->setOptions($options);
		} catch (KontorX_Template_Exception $e) {}
			
		$this->assertEqual(
			$this->_template->getStyleDirName(),
			'styles',
			sprintf('Opcja "%s" nie jest taka sama jak ustawiona','styles'));
		
		$this->assertEqual(
			$this->_template->getStyleConfigFilename(),
			$options['styleConfigFilename'],
			sprintf('Opcja "%s" nie jest taka sama jak ustawiona','styleConfigFilename'));

		$this->assertEqual(
			$this->_template->isAllowedStyleConfig(),
			$options['allowedStyleConfig'],
			sprintf('Opcja "%s" nie jest taka sama jak ustawiona',$options['allowedStyleConfig']));
	}

	public function testGetTemplatePath() {
		$options = array(
			'templatePath' => 'source/',
			'allowedStyleConfig' => false
		);

		$this->_template->setOptions($options);

		$this->assertEqual(
			$this->_template->getTemplatePaths(),
			array('source/'),
			'lista ustawionych szablonów jest różna');
	}
	
	public function testGetTemplatePaths() {
		$options = array(
			'templatePaths' => array(
				'source/',
				'source2/'
			),
			'allowedStyleConfig' => false
		);

		$this->_template->setOptions($options);

		$this->assertEqual(
			$this->_template->getTemplatePaths(),
			array('source/','source2/'),
			'zwrócona lista ustawionych szablonów jest różna');
	}
	
	public function testGetTemplatePathsIsArray() {
		$options = array(
			'templatePaths' => array(
				'source/',
				'source2/'
			),
			'allowedStyleConfig' => false
		);

		$this->_template->setOptions($options);

		$this->assertTrue(
			is_array($this->_template->getTemplatePaths()),
			'lista szablonów nie jest tablicą');
	}

	public function testConfigIni() {
		$options = array(
			'templatePaths' => array(
				realpath('source/'),
				realpath('source2/')
			),
			'styleConfigFilename' => 'config.ini',
			'allowedStyleConfig' => false
		);
		
		$this->_template->setOptions($options);
		$result = $this->_template->getStyleConfig();
		
		$this->assertTrue(
			$result instanceof Zend_Config,
			'configuracja nie jest typu "Zend_Config"');
	}
	
	public function testConfigXml() {
		$options = array(
			'templatePaths' => array(
				realpath('source/'),
				realpath('source2/')
			),
			'styleConfigFilename' => 'config.xml',
			'allowedStyleConfig' => false
		);
		
		$this->_template->setOptions($options);
		$this->_template
			->setTemplateName('win')
			->setStyleName('default');
		$result = $this->_template->getStyleConfig();
		
		$this->assertTrue(
			$result instanceof Zend_Config,
			'configuracja nie jest typu "Zend_Config"');
	}
	
	public function testConfigPhp() {
		$options = array(
			'templatePaths' => array(
				realpath('source/'),
				realpath('source2/')
			),
			'styleConfigFilename' => 'config.php',
			'allowedStyleConfig' => false
		);
		
		$this->_template->setOptions($options);
		$this->_template
			->setTemplateName('win')
			->setStyleName('default');
		$result = $this->_template->getStyleConfig();
		
		$this->assertTrue(
			$result instanceof Zend_Config,
			'configuracja nie jest typu "Zend_Config"');
	}
	
	public function testFindTemplates() {
		$options = array(
			'templatePaths' => array(
				realpath('source/'),
				realpath('source2/')
			)
		);

		$this->_template->setOptions($options);
		$templates = $this->_template->findTemplates();
		$this->dump($templates);
	}

	public function testFindStyles() {
		$options = array(
			'templatePaths' => array(
				realpath('source/'),
				realpath('source2/')
			)
		);

		$this->_template->setOptions($options);
		$templates = $this->_template->findStyles('win');
		$this->dump($templates);
	}
}

$r = new KontorX_Template_TemplateTest();
$r->run(new TextReporter());