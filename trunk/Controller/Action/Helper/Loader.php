<?php
require_once 'Zend/Controller/Action/Helper/Abstract.php';

/**
 * Helper_Loader
 * 
 * @category 	KontorX
 * @package 	KontorX_Controller_Action_Helper
 * @version 	0.1.2
 * @license		GNU GPL
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
 */
class KontorX_Controller_Action_Helper_Loader extends Zend_Controller_Action_Helper_Abstract {
	/**
	 * Typy plikow konfiguracyjnych
	 */
	const CONFIG_PHP = 'php';
	const CONFIG_INI = 'ini';
	const CONFIG_XML = 'xml';

	/**
	 * Przechowuje wczytane obiekty konfiguracji @see Zend_Config
	 *
	 * @var array
	 */
	protected $_storeConfig = array();

	/**
	 * Wczytuje konfiguracje
	 *
	 * @param string $fileName
	 * @param string $module
	 * @param string $type
	 * @return Zend_Config
	 */
	public function config($fileName = null, $module = null, $type = null) {
		$fileName  = (null === $fileName) ? 'config.ini' : $fileName;
		$configKey = "$fileName:$type";

		// zapobiegu ponownemu wczytywaniu konfiguacji
		if (array_key_exists($configKey, $this->_storeConfig)) {
			return $this->_storeConfig[$configKey];
		}

		// sciezka do 
		$path = $this->_getPathConfig($fileName, $module);

		// jakiego typu jest konfiguracja
		switch ($type) {
        	case self::CONFIG_PHP: $config = new Zend_Config(include $path); break;
        	default:
        	case self::CONFIG_INI: $config = new Zend_Config_Ini($path); break;
        	case self::CONFIG_XML: $config = new Zend_Config_Xml($path); break;
        }

        // zapis konfiguracji
        return $this->_storeConfig[$configKey] = $config;
	}

	/**
	 * Sprawdza czy plik konfiguracyjny istnieje
	 *
	 * @param string $fileName
	 * @param string $module
	 * @return bool
	 */
	public function hasConfig($fileName, $module = null) {
		$path = $this->_getPathConfig($fileName, $module);
		return is_file($path);
	}
	
	/**
	 * Zwraca scieżkę do pliku konfiguracji
	 *
	 * @param string $fileName
	 * @param string $module
	 * @return string
	 */
	protected function _getPathConfig($fileName, $module = null) {
		$dispatcher = $this->getFrontController()->getDispatcher();
        $request    = $this->getRequest();

        if (null === $module) {
            $module = $request->getModuleName();
        }

        return APP_MODULES_PATHNAME . "$module/$fileName";
	}

	/**
	 * Przechowuje wczytane obiekty modelu
	 *
	 * @var array
	 */
	protected $_storeModel = array();
	
	public function model($model, $module = null) {
		$dispatcher = $this->getFrontController()->getDispatcher();
        $request    = $this->getRequest();

        if (null === $module) {
            $module = $request->getModuleName();
            if ($module != $dispatcher->getDefaultModule()) {
                $this->addModelIncludePath($module);
            }
        }

        // TODO
        
        Zend_Loader::loadClass($model);
        $model = new $model();
	}

	public function loadModelAsSingleton($model, $module = null) {
		$uniqKey = $model.$module;
		if (array_key_exists($uniqKey, $this->_modelsSingleton)) {
			return $this->_modelsSingleton[$uniqKey];
		}

		$model = $this->loadModel($model, $module);
		$this->_modelsSingleton[$uniqKey] = $model;
		return $model;
	}

	public function addModelIncludePath($module) {
		$this->_addIncludePath('models', $module);
		return $this;
	}

	public function addIncludePath($dirname, $module) {
		$this->_addIncludePath($dirname, $module);
		return $this;
	}

	protected function _addIncludePath($dirname, $module) {
		$path = APP_MODULES_PATHNAME . basename($module) . '/' . $dirname;

		if (!is_dir($path)) {
//			$error = "include path `$path` dosen't exsists";
//			throw new Zend_Controller_Action_Exception($error);
			return false;
		}
		
		if (strstr(get_include_path(), $path) === false) {
			set_include_path(
				get_include_path() . PATH_SEPARATOR .
				$path
			);
		}
	}
}
?>