<?php
/**
 * Klasa pozwala na zarzˆdzanie swoim kontem na seo.net.pl.
 * @author gabrielhabryn
 */
class KontorX_Service_Seonetpl
{
	const SEONET_URI = 'http://seo.net.pl/';
	const SESSION_COOKIE_NAME = 'mano_session';

	public function __construct($username = null, $password = null)
	{
		if (null !== $username) {
			$this->setUsername($username);
		}
		if (null !== $password) {
			$this->setPassword($password);
		}
	}
	
	protected $_localHttpClient;
	
	/**
     * Set local HTTP client as distinct from the static HTTP client
     * as inherited from Zend_Rest_Client.
     *
     * @param Zend_Http_Client $client
     * @return self
     */
    public function setLocalHttpClient(Zend_Http_Client $client)
    {
        $this->_localHttpClient = $client;
        $this->_localHttpClient->setHeaders('Accept-Charset', 'ISO-8859-2, utf-8');
        return $this;
    }
    
    /**
     * Get the local HTTP client as distinct from the static HTTP client
     * inherited from Zend_Rest_Client
     *
     * @return Zend_Http_Client
     */
    public function getLocalHttpClient()
    {
    	if (null === $this->_localHttpClient)
    	{
    		require_once 'Zend/Http/Client.php';
    		$this->_localHttpClient = new Zend_Http_Client();
    	}

        return $this->_localHttpClient;
    }
	
    /**
     * @var string
     */
    protected $_sessionCookie;
    
    /**
     * @throws KontorX_Service_Seonetpl_Exception
     * @return string
     */
    public function getSessionCookie()
    {
    	$this->authorise();
		return $this->_sessionCookie;
    }

    /**
     * @var boolean
     */
    protected $_authorised = false;
    
    /**
     * @return KontorX_Service_Seonetpl
     */
    public function authorise()
    {
    	if (!$this->_authorised)
    	{
    		$action = self::SEONET_URI . '?action=login';
			
			$client = $this->getLocalHttpClient();
			$client->setUri($action);
			$client->setParameterPost('user_login', $this->getUsername());
			$client->setParameterPost('user_password', $this->getPassword());
			$client->setMethod(Zend_Http_Client::POST);
	
			# przechowywanie ciasteczek
			require_once 'Zend/Http/CookieJar.php';
			$cookieJar = new Zend_Http_CookieJar();
			$client->setCookieJar($cookieJar);
	
			$request = $client->request();

			$body = $request->getBody();
			
			# sprawd czy logowanie przebieg¸o prawid¸owo
			require_once 'Zend/Dom/Query.php';
			$query = new Zend_Dom_Query($body);
			$result = $query->query('div.error_message');
			
			if (count($result)) // zawiera komunikat b¸ed 
			{
				return $this;
			}

			$cookie = $cookieJar->getCookie(self::SEONET_URI, self::SESSION_COOKIE_NAME, Zend_Http_CookieJar::COOKIE_OBJECT);
			if (!$cookie instanceof Zend_Http_Cookie) {
				$message = sprintf('Session cookie "%s" do not exists',self::SESSION_COOKIE_NAME);
				require_once 'KontorX/Service/Seonetpl/Exception.php';
				throw new KontorX_Service_Seonetpl_Exception($message);
			}

			$this->_sessionCookie = $cookie->getValue();
			
			$this->_authorised = true;
    	}
			
    	return $this;
    }
    
	/**
	 * @return boolean
	 */
	public function isAuthorised()
	{
		return $this->_authorised;
	}
	
	/**
	 * @var string
	 */
	protected $_username;
	
	/**
     * @return string
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     * @param  string $username
     * @return KontorX_Service_Seonetpl
     */
    public function setUsername($username)
    {
        $this->_username = $username;
        $this->_sessionCookie = null;
        $this->_authorised = false;
        return $this;
    }
    
	/**
	 * @var string
	 */
	protected $_password;
	
	/**
     * @return string
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * Set password
     *
     * @param  string $password
     * @return KontorX_Service_Seonetpl
     */
    public function setPassword($password)
    {
        $this->_password = $password;
        $this->_sessionCookie = null;
        $this->_authorised = false;
        return $this;
    }
    
    /**
     * Pobieranie zestaw—w link—w 
     * @return array
     */
    public function getLinkSet()
    {
    	$this->authorise();
    	
    	$action = self::SEONET_URI . '?module=swl&action=links';

    	$client = $this->getLocalHttpClient();
    	$client->setUri($action);
    	$client->setCookie(self::SESSION_COOKIE_NAME, $this->getSessionCookie());
    	$client->setMethod(Zend_Http_Client::GET);
    	
    	$request = $client->request();
    	$body = $request->getBody();
    	
    	require_once 'Zend/Dom/Query.php';
    	$query = new Zend_Dom_Query($body);

    	$elements = $query->queryXpath('//table[contains(normalize-space(@class), \'listing\')]//tr[contains(normalize-space(@id), \'set_\')]');

    	$result = array();
    	foreach ($elements as /* @var $element DOMElement */ $element)
    	{
    		$result[] = array(
    			'id' => str_replace('set_', '', $element->getAttribute('id')),
    			'name' => $element->firstChild->nodeValue
    		);
    	}

    	return $result;
    }
    
    /**
     * Pobieranie tablicy link—w dla danego zestawu
     * @param integer $linkSetId
     * @return array
     */
    public function getDataForLinkSet($linkSetId)
    {
    	$this->authorise();

    	$linkSetId = (int) $linkSetId;

    	$action = self::SEONET_URI . sprintf('?module=swl&action=export&set_id=%d', $linkSetId);

    	$client = $this->getLocalHttpClient();
    	$client->setUri($action);
    	$client->setCookie(self::SESSION_COOKIE_NAME, $this->getSessionCookie());
    	$client->setMethod(Zend_Http_Client::GET);
    	
    	$request = $client->request();
    	$body = $request->getBody();
    	
    	require_once 'Zend/Dom/Query.php';
    	$query = new Zend_Dom_Query($body);

    	
    	$elements = $query->queryXpath('//textarea');
    	/* @var $element DOMElement */
		$element = $elements->current();
		
		$result = array();
		
		$lines = explode("\n", $element->nodeValue);

		foreach ($lines as $line)
		{
			// pomijanie lini komentarza
			if (substr($line, 0, 1) == '#') {
				continue;
			}
			
			$data = explode(';', $line);
			if (count($data) != 4) {
				continue;
			}

			// walidacja
			$data[2] = (int)$data[2];
			$data[3] = (int)$data[3];

			$result[] = array_combine(array('anchor','url','priority','search_engine_id'), $data);
		}

    	return $result;
    }
}