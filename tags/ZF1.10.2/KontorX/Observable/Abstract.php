<?php
/**
 * KontorX_Observable
 */
abstract class KontorX_Observable_Abstract implements Countable, Iterator {
	const NOTICE = 1;
	const DEBUG = 2;
	const SUCCESS = 3;
	const WARNING = 4;
	const ERROR = 5;
	const CRITICAL = 6;

	/**
	 * Przechowuje obserwatorow
	 * @var array
	 */
	protected $_observers = array();

	/**
	 * Przechowuje aktualny status
	 * @var integer
	 */
	protected $_status = null;

	/**
	 * Przechowuje dane, ktore sa przekazywane pomiedzy obserwatorami
	 * @var arary
	 */
	protected $_data = array();

	/**
	 * Zawiera klucze danych ktore sa tylko do odczytu
	 * @var array
	 */
	protected $_dataReadable = array();
	
	/**
	 * Przechowuje wiadomosci wg. statusow i kolejnosci ich dodania
	 * @var array
	 */
	protected $_messages = array();

	/**
	 * Przechowuje wiadomosci w kolejnosci ich dodania
	 * @var array
	 */
	protected $_messagesRaw = array();

	/**
	 * Czy iteracja obserwatorow jest zablokowana
	 *
	 * @var bool
	 */
	protected $_locked = false;
	
	/**
	 * Ilosc dotychczasowych przewiniec kursora
	 *
	 * @var integer
	 */
	protected $_rewindCount = 0;

	/**
	 * Limit mozliwosci przewijania
	 *
	 * @var integer
	 */
	protected $_rewindLimit = 1;

	/**
	 * Wskaznik kursora iteracji
	 *
	 * @var integer
	 */
	protected $_pointer = 0;

	/**
	 * Liczba obserwatorow
	 *
	 * @var integer
	 */
	protected $_count = 0;
	
	/**
	 * Dodaje obserwator
	 *
	 * @param KontorX_Observable_Observer_Interface $observer
	 */
	public function addObserver(KontorX_Observable_Observer_Interface $observer) {
		// $this->_count++ dalem jako klucz tylko dlatego ze ta wartosc
		// jest ekwiwalentna do $this->_pointer z tym ze pointer powinien
		// się zaczynac od zera powiekszany dopiero poprzez next() itd.
		$this->_observers[$this->_count++] = $observer;
	}

	/**
	 * Usówa obserwator
	 *
	 * @param KontorX_Observable_Observer_Interface $observer
	 * @return bool
	 */
	public function removeObserver(KontorX_Observable_Observer_Interface $observer) {
		foreach ($observers as &$o) {
			if ($o === $observer) {
				unset($o);
				--$this->_count;
				return true;
			}
		}
		return false;
	}

	/**
	 * Zwraca liczbe obserwatorów
	 *
	 * Zwraca liczbe obserwatorów ale tylko obserwatorow
	 * domyslnych nie funkcyjnych!
	 * 
	 * @return integer
	 */
	public function countObservers() {
		return $this->count();
	}

	/**
	 * Powiadom obserwatorów o zmianie wywolujac usluge obserwatora `update`
	 *
	 * Argumenty tej metody, zostaja przekazane
	 * jako argumenty obserwatora
	 * 
	 */
	public function notify() {
		$args = func_get_args();
		$this->_notify('update', $args);
	}

	private  function _notify($method, array $params) {
		array_unshift($params, $this);

		// kolekclonuje statusy
		// gdy obserwer zakonczy SUCCESS wtedy gdy zostanie wykonana metoda rewind
		// obserwatory z SUCCESS zostana pominiete
		$obserwersSuccessStatus = array();

		// pobiera obserwatory domyslne
		$observers = $this->_observers;
		do {
			// pobieramy pierwszego obserwatora z stosu
			$observer = $this->current();

			// sprawdzam czy obserwator juz jest wykonany pomyslnie
			// przydatne przy metodzie rewind by nie powielac obserwacji
			if (in_array($observer, $obserwersSuccessStatus, true)) {
				// przesuwam dalej kursor
				$this->next();
				// skip loop
				continue;
			}

			switch (true) {
				// sprawdzam czy obserwator jest typu KontorX_Observable_Observer_Abstract
				// jezeli tak: sprawdzam czy akceptuje status, nie: pomijam
				case $observer instanceof KontorX_Observable_Observer_Abstract
						&& !$observer->isAcceptStatus($this->getStatus()):
						// przesuwam dalej kursor
						$this->next();
						// skip loop
						continue 2;
			}

			// uruchom obserwatora
			call_user_func_array(array($observer,$method), $params);

			// kolekcjonuje obiekty obserwatora te ktore zakonczone sukcesem
			if ($this->getStatus() == self::SUCCESS) {
				$obserwersSuccessStatus[] = $observer;
			}

			// przesuwam dalej kursor
			$this->next();
		} while ($this->valid());
	}
	
	/**
	 * Ustawienie status przebiegu obserwowanego obiektu
	 * 
	 * W praktyce opserwator zmienia status, i gdy jest cos nie tak
	 * obserwator kolejny w kolejce sprawdza go i decyduje jaka akcje podjasc
	 *
	 * @param integer $status
	 * @throws KontorX_Observable_Exception
	 */
	public function setStatus($status) {
		$this->_checkStatus($status);
		$this->_status = $status;
	}

	/**
	 * Zwraca kod status
	 *
	 * @return integer
	 */
	public function getStatus() {
		return $this->_status;
	}
	
	/**
	 * Sprawdza poprawnosc statusu
	 *
	 * @param integer $status
	 * @throws KontorX_Observable_Exception
	 */
	private function _checkStatus($status) {
		switch ($status) {
			case null:
			case self::SUCCESS:
			case self::NOTICE:
			case self::WARNING:
			case self::ERROR:
			case self::CRITICAL: break;

			default:
				$message = "Unknown status code";
				throw new KontorX_Observable_Exception($message);
		}
	}

	/**
	 * Enter description here...
	 *
	 * @param string $message
	 * @param integer $status
	 */
	public function addMessage($message, $status = null) {
		$this->_checkStatus($status);
		$this->_messages[$status][] = (string) $message;
		$this->_messagesRaw[] = array($status => (string) $message);
	}

	/**
	 * Czy są wiadomosci
	 * 
	 * Mozna sprawdzic czy sa wiadomosci o okreslonym statucie,
	 * wystarczy podac parametr 1
	 *
	 * @param integer $status
	 * @throws KontorX_Observable_Exception
	 * @return bool
	 */
	public function hasMessages($status = null) {
		$this->_checkStatus($status);

		return null === $status
			? !empty($this->_messages)
			: (array_key_exists($status, $this->_messages)
				? !empty($this->_messages[$status]) : false);
	}

	/**
	 * Zwraca wiadomości
	 *
	 * Mozna pobrac wiadomosci tylko o okreslonym statucie
	 * 
	 * @param integer $status
	 * @throws KontorX_Observable_Exception
	 * @return array
	 */
	public function getMessages($status = null) {
		$this->_checkStatus($status);

		return null === $status
			? $this->_messages : (array) @$this->_messages[$status];
	}

	/**
	 * Zwraca wiadomości surowe
	 *
	 * Zwraca wiadomości surowe, nieposortowane
	 * wg. statusu wiadomosci
	 * 
	 * @return array
	 */
	public function getMessagesRaw() {
		return $this->_messagesRaw;
	}

	/**
	 * Ustawia dane
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param bool $readOnly
	 */
	public function setData($key, $value, $readOnly = false) {
		$key = (string) $key;

		// sprawdz czy jest tylko do odczytu
		if (in_array($key, $this->_dataReadable, true)) {
			$message = "Data for key `$key` isset only to read";
			throw new KontorX_Observable_Exception($message);
		} else
		// ustaw do odczytu
		if ($readOnly) {
			$this->_dataReadable[] = $key;
		}
		
		// ustaw dane
		$this->_data[$key] = $value;
	}

	/**
	 * Sprawdza czy istnieje wartosc
	 *
	 * @param string $key
	 * @return bool
	 */
	public function hasData($key) {
		return array_key_exists($key, $this->_data);
	}
	
	/**
	 * Zwraca dane
	 * 
	 * Gdy nie podano parametrow, zwraca wszystkie
	 * Gdy podano $key i klucz istniej zwraca wartosc
	 * w przeciwnym wypadku $default
	 *
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function getData($key = null, $default = null) {
		if (null === $key) {
			return $this->_data;
		}

		return array_key_exists($key, $this->_data)
			? $this->_data[$key]
			: $default;
	}
	
	/**
	 * Zwraca liczbe obserwatorów
	 * @return integer
	 */
	public function count() {
		return $this->_count;
	}

	/**
	 * Nastepny obserwator
	 * @return void
	 */
	public function next() {
		++$this->_pointer;
	}

	/**
	 * Czy jest kolejny
	 * @return bool
	 */
	public function valid() {
		if ($this->locked()) {
			return false;
		}
		return $this->_count > $this->_pointer;
	}

	/**
	 * Klucz
	 * @return unknown
	 */
	public function key() {
		return $this->_pointer;
	}

	/**
	 * Aktualny obserwator
	 * @return KontorX_Observable_Observer_Interface
	 */
	public function current() {
		return $this->_observers[$this->_pointer];
	}

	/**
	 * Kursor na poczatek
	 *
	 */
	public function rewind() {
		if ($this->_rewindCount++ < $this->_rewindLimit) {
			$this->_pointer = 0;
		}
	}

	/**
	 * Przewija kursor do konkretnego obserwatora
	 *
	 * @param KontorX_Observable_Observer_Interface $observer
	 */
	public function rewindTo(KontorX_Observable_Observer_Interface $observer) {
		$this->rewind();
		do  {
			$this->next();
		} while ($this->current() === $observer);
	}

	/**
	 * Zablokuj lancuch
	 *
	 */
	public function lock() {
		$this->_locked = true;
	}

	/**
	 * Odblokuj lancuch
	 *
	 */
	public function unlock() {
		$this->_locked = false;
	}

	/**
	 * Czy zablokowac lancuch
	 *
	 * @return bool
	 */
	public function locked() {
		return $this->_locked;
	}
}
?>