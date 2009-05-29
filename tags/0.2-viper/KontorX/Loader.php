<?php
/**
 * @author gabriel
 * @todo Przekazanie w 'getInstance' konfiguracji i nazw obiektów, które
 * będą wczytywane z czasem ich uzycia.
 * Instancje tych obiektów będą trzymane .. ale będzie tez możliwość utworzenia nowego,
 * dzięki czemu wszystkie klasy, będą a'la leazy load...
 */
class KontorX_Loader {
	/**
	 * @var KontorX_Loader
	 */
	private static $_instance = null;
	
	/**
	 * @param Zend_Config $config
	 * @return KontorX_Loader
	 */
	public static function getInstance(Zend_Config $config) {
		if (null === self::$_instance) {
			self::$_instance = new self($config);
		}
		return self::$_instance;
	}
	
	/**
	 * @param Zend_Config $config
	 * @return void
	 */
	private function __construct(Zend_Config $config) {
		array(
			'KontorX_Semantic' => array(
				'class' => 'KontorX_Semantic',
				// niebędzie trzeba ich definiować
				'params' => array()
			)
		);
	}

	public function get($name);
}