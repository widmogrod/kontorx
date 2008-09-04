<?php
class KontorX_Loader_PluginLoader extends Zend_Loader_PluginLoader {
    public function __construct(Array $prefixToPaths = array(), $staticRegistryName = null) {
   		 if (is_string($staticRegistryName) && !empty($staticRegistryName)) {
            $this->_useStaticRegistry = $staticRegistryName;
            self::$_staticPrefixToPaths[$staticRegistryName] = array();
            self::$_staticLoadedPlugins[$staticRegistryName] = array();
        }

        foreach ($prefixToPaths as $prefix => $path) {
            $this->addPrefixPath($prefix, $path);
            $this->addPrefixPath(
            	str_replace('Zend','KontorX',$prefix),
            	str_replace('Zend','KontorX',$path)
            );
        }
    }
}
