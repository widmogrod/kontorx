<?php
class KontorX_Application_Resource_Doctrine extends Zend_Application_Resource_ResourceAbstract
{
	protected $_defaultGenerateModelsOptions = array(
        'pearStyle' => true,
        'generateTableClasses' => true,
        'baseClassPrefix' => 'Base',
        'baseClassesDirectory' => null,
    );
    
	public function init()
	{
		$this->_setupDoctrine();

		spl_autoload_register(array('Doctrine', 'autoload'));
		spl_autoload_register(array('Doctrine', 'modelsAutoload'));
		spl_autoload_register(array('Doctrine', 'extensionsAutoload'));
		
		$this->_setupConnections();

		$options = $this->getOptions();
		if (isset($options['load_models']) 
		&& true == $options['load_models']) 
		{
			$this->loadModels();
		}
	}

	public function loadModels()
	{
		Doctrine_Core::loadModels(MODELS_PATH);
	}
	
	/**
	 * @var Doctrine_Manager
	 */
	protected $_manager;
	
	/**
	 * Pobierz obiekt @see Doctrine_Manager
	 * @return Doctrine_Manager
	 */
	public function getManager()
	{
		if (null === $this->_manager) {
			$this->_manager = Doctrine_Manager::getInstance();
		}
		
		return $this->_manager;
	}
	
	/**
	 * Podstawowe ustawienia Doctrine
	 */
	protected function _setupDoctrine()
	{
		$options = $this->getOptions();
		$setup = (array) @$options['setup'];

		if (!defined('DATA_FIXTURES_PATH') && isset($setup['data_fixtures_path']))
			define('DATA_FIXTURES_PATH', $setup['data_fixtures_path']);
			
		if (!defined('MODELS_PATH') && isset($setup['models_path']))
			define('MODELS_PATH', $setup['models_path']);
			
		if (!defined('MIGRATIONS_PATH') && isset($setup['migrations_path']))
			define('MIGRATIONS_PATH', $setup['migrations_path']);
			
		if (!defined('SQL_PATH') && isset($setup['sql_path']))
			define('SQL_PATH', $setup['sql_path']);
			
		if (!defined('YAML_SCHEMA_PATH') && isset($setup['yaml_schema_path']))
			define('YAML_SCHEMA_PATH', $setup['yaml_schema_path']);
			
		if (isset($setup['doctrine_path'])) {
			require_once rtrim($setup['doctrine_path'],'\\/') . '/Doctrine.php';
		} else {
			require_once 'Doctrine.php';
		}	
	}

	/**
	 * Konfiguracja po¸ˆczeÄ z bazˆ danych.
	 * @throws Zend_Application_Resource_Exception
	 */
	protected function _setupConnections()
	{
		$options = $this->getOptions();

		if (!isset($options['connection']) ||
			!is_array($options['connection']) ||
			empty($options['connection']))
		{
			$message = '"KontorX_Application_Resource_Doctrine" has no connection defined!';
			throw new Zend_Application_Resource_Exception($message);
		}

		$manager = $this->getManager();
		foreach ($options['connection'] as $name => $adapter) 
		{
			if (is_array($adapter)) {
				$adapter = $adapter['dns'];
			}

			$manager->openConnection($adapter, $name);
		}
	}
	
	
	/**
	 * @var Doctrine_Cli
	 */
	protected $_cli;
	
	/**
	 * Tworzenie instancji obiektu @see Doctrine_Cli.
	 * @return Doctrine_Cli
	 */
	public function getCli()
	{
		if (null === $this->_cli)
		{
			$options = $this->getOptions();
			$userGenerateModelsOptions = (array) @$options['generate_models_options'];
			
			$config = array(
			    'data_fixtures_path'  =>  DATA_FIXTURES_PATH,
			    'models_path'         =>  MODELS_PATH,
			    'migrations_path'     =>  MIGRATIONS_PATH,
			    'sql_path'            =>  SQL_PATH,
			    'yaml_schema_path'    =>  YAML_SCHEMA_PATH,
			    'generate_models_options' => array_merge($this->_defaultGenerateModelsOptions, 
														$userGenerateModelsOptions)
			);
			
			$this->_cli = new Doctrine_Cli($config);
		}
		
		return $this->_cli;
	}
	
	/**
	 * Uruchom obs¸ug« linni polecieÄ dla Doctrine.
	 */
	public function runCli()
	{
		$this->getCli()->run($_SERVER['argv']);
	}
}