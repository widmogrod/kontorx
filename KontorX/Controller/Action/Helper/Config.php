<?php
/**
 * Helper_Loader
 */
class KontorX_Controller_Action_Helper_Config extends Zend_Controller_Action_Helper_Abstract {
    /**
     * Typy plikow konfiguracyjnych
     */
    const CONFIG_PHP = 'php';
    const CONFIG_INI = 'ini';
    const CONFIG_XML = 'xml';

    const MODULE_CONFIG_DIRNNAME = APP_CONFIGURATION_DIRNAME;
    
    /**
     * Przechowuje wczytane obiekty konfiguracji @see Zend_Config
     * @var array
     */
    protected $_cachedConfig = array();

    /**
     * Wczytuje konfiguracje
     * @param string $fileName
     * @param string $module
     * @param string $type
     * @return Zend_Config
     */
    public function config($filename = null, $module = null, $type = null) {
        $filename  = (null === $filename) ? 'config.ini' : $filename;

        // prepere file type
        $type = (null === $type)
            ? pathinfo($filename, PATHINFO_EXTENSION)
            : $type;

//		var_dump(func_get_args());
     	if (null === $module) {
            $module = $this->getRequest()->getModuleName();
        }

        $configKey = "$filename:$module:$type";

        // zapobiegu ponownemu wczytywaniu konfiguacji
        if (array_key_exists($configKey, $this->_cachedConfig)) {
            return $this->_cachedConfig[$configKey];
        }

        // sciezka do
        $path = $this->_getPathConfig($filename, $module);

        // jakiego typu jest konfiguracja
        switch ($type) {
            case self::CONFIG_PHP:
                require_once 'Zend/Config.php';
                $config = new Zend_Config(include $path);
                break;
            default:
            case self::CONFIG_INI:
                require_once 'Zend/Config/Ini.php';
                $config = new Zend_Config_Ini($path);
                break;
            case self::CONFIG_XML:
                require_once 'Zend/Config/Xml.php';
                $config = new Zend_Config_Xml($path);
                break;
        }

        // zapis konfiguracji
        return $this->_cachedConfig[$configKey] = $config;
    }

    /**
     * @param string $filename
     * @param string $module
     * @param string $type
     * @return Zend_Config
     */
    public function direct($filename = null, $module = null, $type = null) {
    	return $this->config($filename, $module, $type);
    }
    
    /**
     * Sprawdza czy plik konfiguracyjny istnieje
     * @param string $filename
     * @param string $module
     * @return bool
     */
    public function hasConfig($filename, $module = null) {
        $path = $this->_getPathConfig($filename, $module);
        return is_file($path);
    }

    /**
     * Zwraca scieżkę do pliku konfiguracji
     * @param string $filename
     * @param string $module
     * @return string
     */
    protected function _getPathConfig($filename, $module = null) {
        if (null === $module) {
            $module = $this->getRequest()->getModuleName();
        }

        return APP_MODULES_PATHNAME . $module . DIRECTORY_SEPARATOR . self::MODULE_CONFIG_DIRNNAME . DIRECTORY_SEPARATOR. $filename;
    }
}