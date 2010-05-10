<?php
/**
 * Integracja z płatnościami internetowymi platnosci.pl {@link https://www.platnosci.pl}
 * 
 * @version $Id$
 * @author Gabriel Habryn, widmogrod@gmail.com
 * @license MIT License
 */
class KontorX_Payments_Platnosci
{
	const BASE_URL = 'https://www.platnosci.pl/paygw';
	
	/**
	 * Kody błędów
	 * @var array
	 */
	protected $_errorCodes = array(
		100 => 'brak lub błędna wartość parametru pos id',
		101 => 'brak parametru session id',
		102 => 'brak parametru ts',
		103 => 'brak lub błędna wartość parametru sig',
		104 => 'brak parametru desc',
		105 => 'brak parametru client ip',
		106 => 'brak parametru first name',
		107 => 'brak parametru last name',
		108 => 'brak parametru street',
		109 => 'brak parametru city',
		110 => 'brak parametru post code',
		111 => 'brak parametru amount',
		112 => 'błędny numer konta bankowego',
		113 => 'brak parametru email',
		114 => 'brak numeru telefonu',
		200  => 'inny chwilowy błąd',
		201  => 'inny chwilowy błąd bazy danych',
		202  => 'Pos o podanym identyfikatorze jest zablokowany',
		203  => 'niedozwolona wartość pay type dla danego pos id',
		204  => 'podana metoda płatności (wartość pay type) jest chwilowo zablokowana dla danego pos id, np. przerwa konserwacyjna bramki płatniczej',
		205  => 'kwota transakcji mniejsza od wartości minimalnej',
		206  => 'kwota transakcji większa od wartości maksymalnej',
		207  => 'przekroczona wartość wszystkich transakcji dla jednego klienta w ostatnim prze-dziale czasowym',
		208  => 'Pos działa w wariancie ExpressPayment lecz nie nastąpiła aktywacja tego wariantu współpracy (czekamy na zgodę działu obsługi klienta)',
		209  => 'błędny numer pos id lub pos auth key',
		500  => 'transakcja nie istnieje',
		501  => 'brak autoryzacji dla danej transakcji',
		502  => 'transakcja rozpoczęta wcześniej',
		503  => 'autoryzacja do transakcji była już przeprowadzana',
		504  => 'transakcja anulowana wcześniej',
		505  => 'transakcja przekazana do odbioru wcześniej',
		506  => 'transakcja już odebrana',
		507  => 'błąd podczas zwrotu środków do klienta',
		599  => 'błędny stan transakcji, np. nie można uznać transakcji kilka razy lub inny, prosimy o kontakt',
		999  => 'inny błąd krytyczny - prosimy o kontakt'	
	);

	/**
	 * Status 2 - „anulowana” pojawi się automatycznie 
	 * po określonej liczbie dni (p. 2.4) od utworzenia lub
	 * rozpoczęcia transakcji (Status 1 lub 4) jeśli do tego czasu 
	 * nie zostanie ona rozliczona (nie wpłyną środki do systemu Płatności.pl)
	 * @var int
	 */
//	const STATUS_NOWA = 1;
	
	/**
	 * Status 2 - „anulowana” pojawi się automatycznie 
	 * po określonej liczbie dni (p. 2.4) od utworzenia lub
	 * rozpoczęcia transakcji (Status 1 lub 4) jeśli do tego czasu 
	 * nie zostanie ona rozliczona (nie wpłyną środki do systemu Płatności.pl)
	 * @var int
	 */
//	const STATUS_ANULOWANA = 2;
	
	/**
	 * Statusy transakcji
	 * @var array
	 */
	protected $_statuses = array(
		1 => 'nowa',
		2 => 'anulowana',
		3 => 'odrzucona',
		4 => 'rozpoczęta',
		5 => 'oczekuje na odbiór',
		7 => 'płatność odrzucona, otrzymano środki od klienta po wcześniejszym anulowaniu transakcji, lub nie było możliwości zwrotu środków w sposób automatyczny, sytuacje takie będą monitorowane i wyjaśniane przez zespół Płatności',
		99 => 'płatność odebrana - zakończona',
		888 => 'błędny status - prosimy o kontakt'
	);

	/**
	 * Typy płatności
	 * @var array
	 */
	protected $_paymentTypes = array(
		'm'  => 'mTransfer - mBank',
		'n'  => 'MultiTransfer - MultiBank',
		'w'  => 'BZWBK - Przelew24',
		'o'  => 'Pekao24Przelew - Bank Pekao',
		'i'  => 'Płacę z Inteligo',
		'd'  => 'Płać z Nordea',
		'p'  => 'Płać z iPKO',
		'h'  => 'Płać z BPH',
		'g'  => 'Płać z ING',
		'l'  => 'LUKAS e-przelew',
		'wp' => 'Przelew z Polbank',
		'wm' => 'Przelew z Millennium',
		'wk' => 'Przelew z Kredyt Bank',
		'wg' => 'Przelew z BGŻ',
		'wd' => 'Przelew z Deutsche Bank',
		'wr' => 'Przelew z Raiffeisen Bank',
		'wc' => 'Przelew z Citibank',
		'c'  => 'karta kredytowa',
		'b'  => 'Przelew bankowy',
		't'  => 'płatność testowa - zostanie wyświetlony formularz, w którym można bezpośrednio zmienić status transakcji'
	);
	
	public function __construct($options = null)
	{
		if (is_array($options)){
			$this->setOptions($options);
		} elseif ($options instanceof Zend_Config) {
			$this->setOptions($options->toArray());
		}
	}
	
	/**
	 * @param array $options
	 * @return void
	 */
	public function setOptions(array $options)
	{
		$f = new Zend_Filter_Word_UnderscoreToCamelCase();
		foreach ($options as $key => $value)
		{
			$method = 'set' . $f->filter($key);
			if (method_exists($this, $method))
			{
				$this->$method($value);
			}
		}
	}
	
	/**
	 * Pobranie treści błedu na podstawie jego kodu
	 * @return string|null 
	 */
	public function getErrorByCode($code)
	{
		if (isset($this->_errorCodes[$code]))
		{
			return $this->_errorCodes[$code];
		}

		return null;
	}
	
	/**
	 * Zwrucenie informacji o formach płatności
	 * @return array 
	 */
	public function getPaymentsTypes()
	{
		return $this->_paymentTypes;
	}
	
	/**
	 * @var SimpleXMLElement
	 */
	protected $_avalidblePaymentTypes;
	
	/**
	 * Zwrucenie informacji o dostępnych formach płatności
	 * @return SimpleXMLElement
	 */
	public function getPaymentsAvalibleTypes()
	{
		if (null === $this->_avalidblePaymentTypes)
		{
			$url = $this->getUrlDlaProcedury(self::ACTION_PAYTYPE);
	
			$http = new Zend_Http_Client($url);
			/* @var $response Zend_Http_Response */
			$response = $http->request(Zend_Http_Client::GET);
	
			$xml = $response->getBody();

			set_error_handler(array($this, '_loadSimpleXMLErrorHandler'));
			/* @var $payTypes SimpleXMLElement */
	        $payTypes = simplexml_load_string($xml);
	        restore_error_handler();
	
	        if (false === $this->_loadSimpleXMLError)
	        {
	        	$this->_avalidblePaymentTypes = $payTypes->children();
	        } else {
	        	// wyerowanie na brak błedu
	        	$this->_loadSimpleXMLError = false;
	        }
		}

		return $this->_avalidblePaymentTypes;
	}

	/**
	 * @var bool
	 */
	protected $_loadSimpleXMLError = false;
	
	/**
	 * @return void
	 */
	protected function _loadSimpleXMLErrorHandler()
	{
		$this->_loadSimpleXMLError = true;
	}
	
	/**
	 * ISO-8859-2
	 * @var string
	 */
	const KODOWANIE_ISO = 'ISO';
	
	/**
	 * UTF-8
	 * @var string
	 */
	const KODOWANIE_UTF = 'UTF';
	
	/**
	 * Windows-1250
	 * @var string
	 */
	const KODOWANIE_WIN = 'WIN';
	
	/**
	 * @var string
	 */
	protected $_kodowanie = self::KODOWANIE_UTF;
	
	/**
	 * W zależności od tego jakiej strony kodowej 
	 * używa aplikacja Sklepu należy wybrać odpowiednie
	 * kodowanie przy odwołaniu do procedur Platnosci.pl
	 * 
	 * @param string $kodowanie
	 * @return KontorX_Payments_Platnosci
	 * @throws KontorX_Payments_Exception
	 */
	public function setKodowanie($kodowanie)
	{
		switch ($kodowanie)
		{
			case self::KODOWANIE_ISO:
			case self::KODOWANIE_UTF:
			case self::KODOWANIE_WIN:
				$this->_kodowanie = $kodowanie;
				break;

			default:
				throw new KontorX_Payments_Exception('niewłaściwe kodowanie');
		}

		return $this;
	}

	/**
	 * Format danych
	 * @var string
	 */
	const FORMAT_DANYCH_XML = 'xml';
	const FORMAT_DANYCH_TXT = 'txt';
	
	/**
	 * "xml" lub "txt"
	 * @var string
	 */
	protected $_formatDanych = self::FORMAT_DANYCH_XML;
	
	/**
	 * Dla procedur: Payment/get, Payment/confirm, Payment/cancel, 
	 * możemy jeszcze podać formatw jakim mają być przesłane dane
	 * 
	 * @param string $formatDanych
	 * @return KontorX_Payments_Platnosci
	 * @throws KontorX_Payments_Exception
	 */
	public function setFormatDanych($formatDanych)
	{
		switch ($kodowanie)
		{
			case self::FORMAT_DANYCH_XML:
			case self::FORMAT_DANYCH_TXT:
				$this->_formatDanych = $formatDanych;
				break;

			default:
				throw new KontorX_Payments_Exception('niewłaściwy format danych');
		}

		return $this;
	}
	
	const ACTION_GET 		= 'Payment/get';
	const ACTION_CONFIRM 	= 'Payment/confirm';
	const ACTION_CANCEL 	= 'Payment/cancel';
	const ACTION_NEW 		= 'NewPayment';
	const ACTION_PAYTYPE 	= 'PayType';
	
	/**
	 * UrlPlatnosci.pl/Kodowanie/NazwaProcedury/Format
	 * @param string $procedura
	 * @return string
	 */
	public function getUrlDlaProcedury($procedura)
	{
		switch($procedura)
		{
			case self::ACTION_GET:
			case self::ACTION_CANCEL:
			case self::ACTION_CONFIRM:
			case self::ACTION_CANCEL:
				$data = array(
					self::BASE_URL,
					$this->_kodowanie,
					$procedura,
					$this->_formatDanych
				);
				break;

			case self::ACTION_NEW:
				$data = array(
					self::BASE_URL,
					$this->_kodowanie,
					$procedura,
				);
				break;

			case self::ACTION_PAYTYPE:
				$parts = str_split($this->getKey1(), 2);
				$data = array(
					self::BASE_URL,
					$this->_kodowanie,
					'xml',
					$this->getPosId(),
					$parts[0],
					'paytype.xml'
				);
				break;

			default:
				throw new KontorX_Payments_Exception('niewłaściwy typ proceduty "'.$procedura.'"');
		}

		return join('/', $data);
	}

	/**
	 * @var string
	 */
	protected $_urlPozytywny;
	
	/**
	 * UrlPozytywny - adres url aplikacji Sklepu 
	 * pod jaki Klient będzie przekierowany po prawidłowym
	 * rozpoczęciu transakcji
	 * 
	 * @return KontorX_Payments_Platnosci
	 */
	public function setUrlPozytywny($url)
	{
		$this->_urlPozytywny = (string) $url;
		return $this;
	}
	
	/**
	 * @var string
	 */
	protected $_urlNegatywny;
	
	/**
	 * UrlNegatywny - adres url aplikacji Sklepu 
	 * pod jaki Klient będzie przekierowany po błędnym
	 * rozpoczęciu transakcji
	 * 
	 * @return KontorX_Payments_Platnosci
	 */
	public function setUrlNegatywny($url)
	{
		$this->_urlNegatywny = (string) $url;
		return $this;
	}
	
	/**
	 * @var string
	 */
	protected $_urlOnline;
	
	/**
	 * UrlOnline - adres url aplikacji Sklepu
	 * pod jaki będą wysyłane za pomocą metody POST informacje
	 * o zmianie stanu płatności – raporty
	 * 
	 * @return KontorX_Payments_Platnosci
	 */
	public function setUrlOnline($url)
	{
		$this->_urlOnline = (string) $url;
		return $this;
	}

	/**
	 * @param string $actionType
	 * @return KontorX_Payments_Platnosci_Response_Xml
	 * @throws KontorX_Payments_Exception
	 */
	public function request($actionType)
	{
		$url = $this->getUrlDlaProcedury($actionType);

		$time = time();
		$sig = md5($this->getPosId() + $this->getSessionId() + $time + $this->getKey1());
		
		$http = new Zend_Http_Client($url);
		$http->setParameterPost('post_id', $this->getPosId());
		$http->setParameterPost('session_id', $this->getSessionId());
		$http->setParameterPost('ts', $time);
		$http->setParameterPost('sig', $sig);

		$response = $http->request(Zend_Http_Client::POST);

		switch($this->_formatDanych)
		{
			case self::FORMAT_DANYCH_XML:
				return new KontorX_Payments_Platnosci_Response_Xml($response->getBody());
			default:
				throw new KontorX_Payments_Platnosci_Response_Exception('format danych "'.$this->_formatDanych.'" nie jest obsługiwany');
		}
	}

	/**
	 * @var integer
	 */
	protected $_posId;
	
	/**
	 * Wartość nadana przez Platnosci.pl
	 * 
	 * @param integer $posId
	 * @return KontorX_Payments_Platnosci
	 */
	public function setPosId($posId)
	{
		$this->_posId = (int) $posId;
		return $this;
	}
	
	/**
	 * @return integer
	 * @throws KontorX_Payments_Exception
	 */
	public function getPosId()
	{
		if (null === $this->_posId)
		{
			throw new KontorX_Payments_Exception('nie podano "pos_id"');
		}

		return $this->_posId;
	}
	
	/**
	 * @var STR {7,7}
	 */
	protected $_posAuthKey;

	/**
	 * Wartość nadana przez Platnosci.pl
	 * 
	 * @param string $posAuthKey
	 * @return KontorX_Payments_Platnosci
	 * @throws KontorX_Payments_Exception
	 */
	public function setPosAuthKey($posAuthKey)
	{
		if (strlen($posAuthKey) != 7)
		{
			throw new KontorX_Payments_Exception('niewłaściwa wartość "pos_auth_key"');
		}

		$this->_posAuthKey = (string) $posAuthKey;
		return $this;
	}

	/**
	 * @return string
	 * @throws KontorX_Payments_Exception
	 */
	public function getPostAuthKey()
	{
		if (null === $this->_posAuthKey)
		{
			throw new KontorX_Payments_Exception('nie podano "pos_auth_key"');
		}

		return $this->_posAuthKey;
	}
	
	/**
	 * @var integer
	 */
	protected $_key1;
	
	/**
	 * Wartość nadana przez Platnosci.pl
	 * 
	 * @param integer $posId
	 * @return KontorX_Payments_Platnosci
	 */
	public function setKey1($key)
	{
		$this->_key1 = (string) $key;
		return $this;
	}
	
	/**
	 * @return integer
	 * @throws KontorX_Payments_Exception
	 */
	public function getKey1()
	{
		if (null === $this->_key1)
		{
			throw new KontorX_Payments_Exception('nie podano "key1"');
		}

		return $this->_key1;
	}
	
	/**
	 * @var integer
	 */
	protected $_key2;
	
	/**
	 * Wartość nadana przez Platnosci.pl
	 * 
	 * @param integer $posId
	 * @return KontorX_Payments_Platnosci
	 */
	public function setKey2($key)
	{
		$this->_key2 = (string) $key;
		return $this;
	}
	
	/**
	 * @return integer
	 * @throws KontorX_Payments_Exception
	 */
	public function getKey2()
	{
		if (null === $this->_key2)
		{
			throw new KontorX_Payments_Exception('nie podano "key2"');
		}

		return $this->_key2;
	}
	
	
	/**
	 * @var STR {0, 1024}
	 */
	protected $_sessionId;

	/**
	 * Identyfikator płatności - unikalny dla klienta
	 * 
	 * @param string sessionId
	 * @return KontorX_Payments_Platnosci
	 * @throws KontorX_Payments_Exception
	 */
	public function setSessionId($sessionId)
	{
		if (strlen($sessionId) < 1)
		{
			throw new KontorX_Payments_Exception('niewłaściwa wartość "session_id"');
		}

		$this->_sessionId = (string) $sessionId;
		return $this;
	}

	/**
	 * @return string
	 * @throws KontorX_Payments_Exception
	 */
	public function getSessionId()
	{
		if (null === $this->_sessionId)
		{
			/**
			 * Jeżeli {@see Zend_Session} nie jest uzywany wtedy
			 * exception, w przeciwnym razie sprubuj uruchomić sesję :)
			 */
			if (!class_exists('Zend_Session', false))
			{
				throw new KontorX_Payments_Exception('nie podano "session_id"');
			} else {
				// jeżeli sesja nie została uruchomiona.. uruchom ją
				if (!Zend_Session::isStarted()) {
					Zend_Session::start();
				}
			}
			
			$this->_sessionId = Zend_Session::getId();
		}

		return $this->_sessionId;
	}
	
	/**
	 * @var numeric
	 */
	protected $_amount;

	/**
	 * Kwota w groszach
	 * 
	 * @param numeric $amount
	 * @return KontorX_Payments_Platnosci
	 * @throws KontorX_Payments_Exception
	 */
	public function setAmount($amount)
	{
		if (!is_numeric($amount))
		{
			throw new KontorX_Payments_Exception('podana wartość "amount" nie jest liczbą');
		}

		$this->_amount = $amount;
		return $this;
	}

	/**
	 * @return numeric
	 * @throws KontorX_Payments_Exception
	 */
	public function getAmount()
	{
		if (null === $this->_amount)
		{
			throw new KontorX_Payments_Exception('nie podano "amount"');
		}

		return $this->_amount;
	}
	
	/**
	 * @var string {1,50}
	 */
	protected $_desc;

	/**
	 * Krótki opis - pokazywany klientowi, 
	 * trafia na wyciągi i inne miejsca
	 * 
	 * @param string $desc
	 * @return KontorX_Payments_Platnosci
	 * @throws KontorX_Payments_Exception
	 */
	public function setDesc($desc)
	{
		$length = strlen($desc);
		if ($length < 1 || $length > 50)
		{
			throw new KontorX_Payments_Exception('wartość "desc" jest niepoprawna');
		}

		$this->_desc = (string) $desc;
		return $this;
	}

	/**
	 * @return string
	 * @throws KontorX_Payments_Exception
	 */
	public function getDesc()
	{
		if (null === $this->_desc)
		{
			throw new KontorX_Payments_Exception('nie podano "desc"');
		}

		return $this->_desc;
	}
	
	/**
	 * @var string {1,100}
	 */
	protected $_firstName;

	/**
	 * Imię
	 * 
	 * @param string $firstName
	 * @return KontorX_Payments_Platnosci
	 * @throws KontorX_Payments_Exception
	 */
	public function setFirstName($firstName)
	{
		$length = strlen($firstName);
		if ($length < 1 || $length > 100)
		{
			throw new KontorX_Payments_Exception('wartość "first_name" jest niepoprawna');
		}

		$this->_firstName = (string) $firstName;
		return $this;
	}

	/**
	 * @return string
	 * @throws KontorX_Payments_Exception
	 */
	public function getFirstName()
	{
		if (null === $this->_firstName)
		{
			throw new KontorX_Payments_Exception('nie podano "first_name"');
		}

		return $this->_firstName;
	}
	
	/**
	 * @var string {1,100}
	 */
	protected $_lastName;

	/**
	 * Nazwisko
	 * 
	 * @param string $lastName
	 * @return KontorX_Payments_Platnosci
	 * @throws KontorX_Payments_Exception
	 */
	public function setLastName($lastName)
	{
		$length = strlen($lastName);
		if ($length < 1 || $length > 100)
		{
			throw new KontorX_Payments_Exception('wartość "last_name" jest niepoprawna');
		}

		$this->_lastName = (string) $lastName;
		return $this;
	}

	/**
	 * @return string
	 * @throws KontorX_Payments_Exception
	 */
	public function getLastName()
	{
		if (null === $this->_lastName)
		{
			throw new KontorX_Payments_Exception('nie podano "last_name"');
		}

		return $this->_lastName;
	}
	
	/**
	 * @var string
	 */
	protected $_email;

	/**
	 * Adres email
	 * 
	 * @param string $email
	 * @return KontorX_Payments_Platnosci
	 * @throws KontorX_Payments_Exception
	 */
	public function setEmail($email)
	{
		require_once 'Zend/Validate/EmailAddress.php';
		$v = new Zend_Validate_EmailAddress();

		if (!$v->isValid($email))
		{
			throw new KontorX_Payments_Exception('wartość "email" jest niepoprawna');
		}

		$this->_email = (string) $email;
		return $this;
	}

	/**
	 * @return string
	 * @throws KontorX_Payments_Exception
	 */
	public function getEmail()
	{
		if (null === $this->_email)
		{
			throw new KontorX_Payments_Exception('nie podano "email"');
		}

		return $this->_email;
	}
	
	/**
	 * @var string
	 */
	protected $_clientIp;

	/**
	 * Adres clientIp klienta w formacie
	 * D{1,3}.D{1,3}.D{1,3}.D{1,3}
	 * 
	 * @param string $clientIp
	 * @return KontorX_Payments_Platnosci
	 * @throws KontorX_Payments_Exception
	 */
	public function setClientIP($clientIp)
	{
		require_once 'Zend/Validate/Ip.php';
		$v = new Zend_Validate_Ip();

		if (!$v->isValid($clientIp))
		{
			throw new KontorX_Payments_Exception('wartość "client_ip" jest niepoprawna');
		}

		$this->_clientIp = (string) $clientIp;
		return $this;
	}

	/**
	 * @return string
	 * @throws KontorX_Payments_Exception
	 */
	public function getClientIP()
	{
		if (null === $this->_clientIp)
		{
			$ip = getenv('HTTP_X_FORWARDED_FOR');
			
			$ip = !$ip ? getenv('REMOTE_ADDR') : $ip;

			require_once 'Zend/Validate/Ip.php';
			$v = new Zend_Validate_Ip();
			if (!$v->isValid($ip))
			{
				throw new KontorX_Payments_Exception('nie podano IP klienta"');
			}

			$this->_clientIp = $ip;
		}

		return $this->_clientIp;
	}
	
	
	/**
	 * Podpisy MD5 (sig)
	 * 
	 * Każde przesłanie polecania oraz każda odpowiedź
	 * generowana przez Platnosci.pl zawiera podpis MD5, 
	 * dzięki temu można zweryfikować poprawność danych.
	 * 
	 * @return string
	 */
	public function getPodpisyMD5()
	{
		/**
		 * pos id               wartość nadana przez Platnosci.pl
		 * session id           identyfikator płatności - unikalny dla klienta
		 * wartosc1 ...wartoscn lista dodatkowych wartości, zostanie podana przy opisie poszczególnych
		 * 						metod
		 * ts                   dowolny losowy ciąg znaków, proponowany aktualny czas w sekundach
		 * key                  ciąg znaków znany przez Platnosci.pl oraz Sklep
		 */
		
		// sig = md5(pos id + session id + wartosc1 + wartosc2 + ... + wartoscn + ts + key)

		$key  = $this->getPosId();
		$key += $this->getSessionId();

		return md5($key);
	}

	/**
	 * Podpisywanie parametrów przekazywanych do nowej płatności
	 * 
	 * Opcjonalnie aplikacja Sklepu może dodać do formularza nowej płatności (NewPayment) 
	 * sumę kontrolną wszystkich przekazywanych parametrów.
	 */
	public function getSig($type = null)
	{
		switch ($type)
		{
			case self::ACTION_NEW;
				/*
					md5(pos id + pay type + session id + pos auth key + amount + desc + desc2
					+trsDesc + order id + f irst name + last name + payback login
					+street + street hn + street an + city + post code + country
					+email + phone + language + client ip + ts + key1)
				*/
			
				// TODO: Brakuje_pay_type
			
				return md5($this->getPosId() + $this->getP);
		}
	}
	
}