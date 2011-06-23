<?php
require_once 'Zend/Loader/PluginLoader.php';

/**
 * @author gabriel
 * @package KontorX
 * @version $Id$
 */
class KontorX_Import
{
    public static function factory($filePath, $options = null)
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $extension = strtolower($extension);

        $className = self::getPluginLoader()->load($extension);
        return new $className($filePath, $options);
    }

    /**
     * @var Zend_Loader_PluginLoader
     */
    protected static $_pluginLoader;

    public static function setPluginLoader(Zend_Loader_PluginLoader $pluginLoader)
    {
        self::$_pluginLoader = $pluginLoader;
    }
    
    /**
     * @return Zend_Loader_PluginLoader
     */
    public static function getPluginLoader()
    {
        if (null === self::$_pluginLoader)
        {
            self::$_pluginLoader = new Zend_Loader_PluginLoader();
            self::$_pluginLoader->addPrefixPath('KontorX_Import_Adapter','KontorX/Import/Adapter');
        }
        return self::$_pluginLoader;
    }
}