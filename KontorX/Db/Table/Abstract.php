<?php
require_once 'Zend/Db/Table/Abstract.php';
abstract class KontorX_Db_Table_Abstract extends Zend_Db_Table_Abstract {

	protected $_rowClass = 'KontorX_Db_Table_Row';
	
    /**
     * @var Zend_Cache_Core
     */
    private static $_defaultResultCache = null;

    /**
     * Ustawienie @see Zend_Cache_Core keszujacego wynik zapytania
     * @return void
     */
    public static function setDefaultResultCache($resultCache) {
        self::$_defaultResultCache = self::_setupMetadataCache($resultCache);
    }

    /**
     * Zwraca objekt @see Zend_Cache_Core lub null
     * @return Zend_Cache_Core|null
     */
    public static function getDefaultResultCache() {
        return self::$_defaultResultCache;
    }

    /**
     * @var Zend_Cache_Core|null
     */
    private $_resultCache = null;

    /**
     * Zwraca objekt @see Zend_Cache_Core lub null
     * @return Zend_Cache_Core|null
     */
    public function getResultCache() {
        if (null === $this->_resultCache) {
            $this->_resultCache = self::$_defaultResultCache;
        }
        return $this->_resultCache;
    }

    /**
     * Tablica zdefiniowanych przez użytkownika method, które będą keszowane
     * @var array
     */
    protected $_cachedMethods = array();

    /**
     * Tablica zdefiniowanych method, które będą keszowane
     * @var array
     */
    private $_defaultCachedMethods = array('fetchAll','fetchRow');

    /**
     * Wywoluje metode keszujac rezultat jej wyniku
     * @return mixed
     * @throws Zend_Db_Table_Exception
     */
    public function cache() {
        // pobieranie parametrow
        $params = func_get_args();
        $method = array_shift($params);

        // metoda nie istnieje w zdefiniowanej podstawowej tablicy
        if (!in_array($method, $this->_defaultCachedMethods)) {
            // metoda nie istnieje w tablicy zdefiniowanej przez użytkownika
            if (!in_array($method, $this->_cachedMethods)) {
                $message = "Method '$method' is not enabled as cached method";
                require_once 'Zend/Db/Table/Exception.php';
                throw new Zend_Db_Table_Exception($message);
            }
        }

        $resultCache = $this->getResultCache();

        if (!$resultCache instanceof Zend_Cache_Core) {
            $message = "Cache object is not instanceof Zend_Cache_Core or is not set";
            require_once 'Zend/Db/Table/Exception.php';
            throw new Zend_Db_Table_Exception($message);
        }

        // identyfikator cache
        $cacheId = $this->_getResultCacheId($method, $params);

        // keszowanie
        if (false === ($result = $resultCache->load($cacheId))) {
            $result = call_user_func_array(array($this, $method), $params);
            $resultCache->save($result, $cacheId);
        }

        return $result;
    }

    /**
     * Zwraca cache id.
     * @return string
     */
    private function _getResultCacheId($method, $params = null) {
        // baza id
        $result = array(get_class($this), $method);

        // budowanie id ze wzgledu na parametry
        if (is_array($params)) {
            foreach ($params as $key => $value) {
                if (is_object($value)) {
                    $class = get_class($value);
                    $result[] = $key . $class . serialize($value);
                } else
                if (is_array($value)) {
                    $result[] = $key . serialize($value);
                } else {
                    $result[] = $key . $value;
                }
            }
        } else {
           $result[] = $params;
        }

        return sha1(implode($result));
    }

    /**
     * Magick method
     * 
     * @return mixed
     * @throws Zend_Db_Table_Exception
     */
    public function __call($name, array $params = array()) {
        // tablica przechowuje dopasowania wyrazenia reguralnego
        $matches = array();

        // sprawdzenie czy wywolana metoda jest zakońconych "Cache"
        if (preg_match('/^(?P<method>\w+)Cache$/i', $name, $matches)) {
            if (!isset($matches['method'])) {
                $message = "Method '$name' do not exsists";
                require_once 'Zend/Db/Table/Exception.php';
                throw new Zend_Db_Table_Exception($message);
            }

            $method = $matches['method'];

            // dodanie metody jako pierwszego atrybutu
            array_unshift($params, $method);
            // wywolanie metody "cache"
            return call_user_func_array(array($this, 'cache'), $params);
        }
    }

    /**
     * @todo Poniższe atrybuty i metody przenieść do KontorX_Db_Table!
     */

    /**
     * Nazwa kolumny przechowującej rodzaj widoczności rekordu
     * @var string
     */
    protected $_columnForSpecialCredentials = 'visible';

    /**
     * Przygotowuje zapytanie @see Zend_Db_Select
     *
     * Przygotowuje zapytanie określające, które rekordy
     * mogą zostać wyłowione z BD dla użytkownika.
     * Czy ma uprawnienia do danych rekordów czy nie ..
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Zend_Db_Select $select
     * @return Zend_Db_Select
     */
    public function selectForRowOwner(Zend_Controller_Request_Abstract $request, Zend_Db_Select $select = null) {
        $select = (null === $select)
            ? $this->select()
            : $select;

        $controller = $request->getControllerName();
        $module	 	= $request->getModuleName();

        /**
         * Jeżlie jest brak uprawnień do moderowania, pokaż pod względem ID
         */
        require_once 'user/models/User.php';
        if (!User::hasCredential(User::PRIVILAGE_MODERATE, $controller, $module)) {
            $userId = User::getAuth(User::AUTH_USERNAME_ID);
            $select->where('user_id = ?', $userId);
        }
        return $select;
    }

    /**
     * Nazwa kolumny przechowującej timestamp
     *
     * @var string
     */
    protected $_columnForTimeRange = 't_create';

    /**
     * Ustawia dla @see Zend_Db_Select przedzial czasowy
     *
     * @param Zend_Db_Select $select
     * @param integer $year
     * @param integer $month
     * @param integer $day
     */
    public function selectSetupForTimeRange(Zend_Db_Select $select, $year = null, $month = null, $day = null) {
        // year
        if (is_numeric($year)) {
            $select->where("YEAR($this->_columnForTimeRange) = ?", abs($year));
        }
        // month
        if (is_numeric($month) && $month >= 1 && $month <= 12) {
            $select->where("MONTH($this->_columnForTimeRange) = ?", abs($month));
        }
        // day
        if (is_numeric($day) && $day >= 1 && $day <= 31) {
            $select->where("MONTH($this->_columnForTimeRange) = ?", abs($day));
        }
    }

    /**
     * Przygotowuje zapytanie @see Zend_Db_Select
     *
     * Przygotowuje zapytanie określające, które rekordy
     * mogą zostać wyłowione z BD dla użytkownika
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Zend_Db_Select $select
     * @return Zend_Db_Select
     */
    public function selectForSpecialCredentials(Zend_Controller_Request_Abstract $request, Zend_Db_Select $select = null) {
        $select = (null === $select)
        ? $this->select()
        : $select;

        $controller = $request->getControllerName();
        $module	 	= $request->getModuleName();

        require_once 'user/models/User.php';
        User::selectForSpecialCredentials($select, $this->_columnForSpecialCredentials, $controller, $module);
        return $select;
    }
}