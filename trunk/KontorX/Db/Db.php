<?php
class KontorX_Db extends Zend_Db {
	public static function buildDSN(array $config) {
		// don't pass the username, password, and driver_options in the DSN
        unset($dsn['username']);
        unset($dsn['password']);
        unset($dsn['options']);
        unset($dsn['driver_options']);

        // use all remaining parts in the DSN
        foreach ($dsn as $key => $val) {
            $dsn[$key] = "$key=$val";
        }
        return $this->_pdoType . ':' . implode(';', $dsn);
		'mysql://user:password@host/database';
	}
}
?>