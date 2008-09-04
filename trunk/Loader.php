<?php
/**
 * KontorX_Loader
 * 
 * @category 	KontorX
 * @package 	KontorX_Loader
 * @version 	0.1.0
 * @license		GNU GPL
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
 */
class KontorX_Loader extends Zend_Loader {
	public static function autoload($class) {
		try {
			if ('ezcBase' == $class && !class_exists('ezcBase', false)) {
				require_once 'ezcomponents/Base/src/base.php';
			} else
			if ('ezc' == substr($class, 0, 3)){
				ezcBase::autoload($class);
			} else {
				self::loadClass($class);
			}
            return $class;
        } catch (Exception $e) {
            return false;
        }
    }
}
?>