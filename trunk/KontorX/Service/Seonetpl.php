<?php
/**
 * Klasa pozwala na zarzàdzanie swoim kontem na seo.net.pl.
 * @author gabrielhabryn
 */
class KontorX_Service_Seonetpl
{
	const SEONET_URI = 'http://seo.net.pl/';
	const SESSION_COOKIE_NAME = 'mano_session';

	protected $_dataKeys = array('anchor','url','priority','search_engine_id');
	
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
    		$client = new Zend_Http_Client();
    		$this->setLocalHttpClient($client);
    	}

    	$this->_localHttpClient->resetParameters(true);

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
			
			# sprawdê czy logowanie przebieg∏o prawid∏owo
			require_once 'Zend/Dom/Query.php';
			$query = new Zend_Dom_Query($body);
			$result = $query->query('div.error_message');
			
			if (count($result)) // zawiera komunikat b∏ed 
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
     * Pobieranie zestawów linków 
     * @return array
     */
    public function getLinkSets()
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
     * Pobieranie tablicy linków dla danego zestawu
     * @param integer $linkSetId
     * @return array
     */
    public function exportDataForLinkSet($linkSetId)
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
    
	/**
     * Zapisywanie tablicy linków dla danego zestawu
     * @param array $data
     * @param integer $linkSetId
     * @throws KontorX_Service_Seonetpl_Exception
     * @return array
     */
    public function importDataForLinkSet(array $data, $linkSetId)
    {
    	$this->authorise();

    	$linkSetId = (int) $linkSetId;
    	
    	if (!count($data) || empty($data)) 
    	{
    		$message = 'Data is empty';
    		require_once 'KontorX/Service/Seonetpl/Exception.php';
			throw new KontorX_Service_Seonetpl_Exception($message);
    	}
    	
    	$lines = '';
    	foreach ($data as $key => $line) 
    	{
    		if (!is_array($line)) {
    			$message = 'data line for key "'.$key.'" is not array';
    			require_once 'KontorX/Service/Seonetpl/Exception.php';
				throw new KontorX_Service_Seonetpl_Exception($message);
    		}

    		$line = array_intersect_key($line, array_flip($this->_dataKeys));

    		if (count($line) != 4) {
    			$message = 'data line for key "'.$key.'" has invalid keys';
    			require_once 'KontorX/Service/Seonetpl/Exception.php';
				throw new KontorX_Service_Seonetpl_Exception($message);
    		}
    		
    		$row = array_combine($this->_dataKeys, array(
    			$line[$this->_dataKeys[0]],
    			$line[$this->_dataKeys[1]],
    			(int) $line[$this->_dataKeys[2]],
    			1 // tylko ten jest obs∏ugiwany na chwil´ obecnà
    		));

    		$lines .= implode(';', $row);
    		$lines .= "\n";
    	}

    	$action = self::SEONET_URI . sprintf('?module=swl&action=import&set_id=%d', $linkSetId);

    	$client = $this->getLocalHttpClient();
    	$client->setUri($action);
    	$client->setCookie(self::SESSION_COOKIE_NAME, $this->getSessionCookie());
    	$client->setMethod(Zend_Http_Client::POST);
		$client->setParameterPost('import_data', $lines);

    	$request = $client->request();
    	$body = $request->getBody();

    	require_once 'Zend/Dom/Query.php';
    	$query = new Zend_Dom_Query($body);
    	
    	$result = $query->query('div.success_message');
    	if (count($result))  // zawiera komunikat sukcesu
    	{
    		return true;
    	}
    	
    	$result = $query->query('div.error_message');
    	if (count($result))  // zawiera komunikat b∏´du
    	{
    		return false;
    	}

		$message = 'Result unknown';
		require_once 'KontorX/Service/Seonetpl/Exception.php';
		throw new KontorX_Service_Seonetpl_Exception($message);
    }
    
	public function getSetLinks($linkSetId)
    {
    	$this->authorise();
    	
    	$linkSetId = (int) $linkSetId;

    	$action = self::SEONET_URI . sprintf('?module=swl&action=get_set_links&set_id=%d&orderby=url', $linkSetId);

    	$client = $this->getLocalHttpClient();
    	$client->setUri($action);
    	$client->setCookie(self::SESSION_COOKIE_NAME, $this->getSessionCookie());
    	$client->setMethod(Zend_Http_Client::GET);

    	$request = $client->request();
    	$body = $request->getBody();

    	require_once 'Zend/Dom/Query.php';
    	$query = new Zend_Dom_Query($body);

    	
    	$elements = $query->queryXpath('//table[contains(normalize-space(@class), \'listing\')]//tr[contains(normalize-space(@id), \'link_\')]');
    	
    	$result = array();
    	foreach ($elements as /* @var $element DOMElement */ $element)
    	{
    		$childNodes = $element->childNodes;
//    		foreach ($childNodes as $k => $node)
//    		{
//    			var_dump($k);
//    			var_dump($node->nodeName);
//    		}

    		$result[] = array(
    			'id' => str_replace('link_', '', $element->getAttribute('id')),
    			'name' => $childNodes->item(2)->childNodes->item(1)->nodeValue,
    			'link' => $childNodes->item(2)->childNodes->item(4)->nodeValue,
    			'max' => $this->_getSelectedChildNode($childNodes->item(16)->firstChild->childNodes, 'nodeValue'),
	    		'perday' => $this->_getSelectedChildNode($childNodes->item(14)->firstChild->childNodes, 'nodeValue'),
	    		'priority' => $this->_getSelectedChildNode($childNodes->item(8)->firstChild->childNodes, 'nodeValue'),
    		);
    	}

    	return $result;
    	
    	// http://seo.net.pl/?module=swl&action=get_set_links&set_id=91794&orderby=url
    	
    	/*
			action	get_set_links
			module	swl
			orderby	url
			set_id	91794
		*/
    	// GET
    	// //*[@id="link_5441603"]
    	// $elements = $query->queryXpath();
    }

    protected function _getSelectedChildNode($childNodes, $methodOrProperty = null, $default = null)
    {
    	if (null === $childNodes) {
    		return $default;
    	}
    	
    	if (!$childNodes instanceof $childNodes) {
    		return $default;
    	}

    	foreach ($childNodes as /* @var $element DOMElement */ $element)
    	{
    		if ($element->hasAttribute('selected')) 
    		{
    			if (null !== $methodOrProperty) 
    			{
    				if (isset($element->$methodOrProperty)) {
    					return $element->$methodOrProperty;
    				}
    				elseif (method_exists($element, $methodOrProperty)) 
    				{
    					return $element->$methodOrProperty();
    				}
    			}

    			return $element;
    		}
    	}
    	
    	return $default;
    }
    
	public function deleteLink($linkId, $linkSetId)
    {
    	$this->authorise();
    	
    	$linkId = (int) $linkId;
    	$linkSetId = (int) $linkSetId;

    	$action = self::SEONET_URI . sprintf('?module=swl&action=delete_link&link_id=%d&set_id=%d', $linkId, $linkSetId);

    	$client = $this->getLocalHttpClient();
    	$client->setUri($action);
    	$client->setCookie(self::SESSION_COOKIE_NAME, $this->getSessionCookie());
    	$client->setMethod(Zend_Http_Client::GET);

    	$request = $client->request();
    	$body = $request->getBody();

    	require_once 'Zend/Dom/Query.php';
    	$query = new Zend_Dom_Query($body);

    	$elements = $query->query('div.success_message');
    	if (count($elements)) {
    		return true;
    	}
    	
    	$elements = $query->query('div.error_message');
    	if (count($elements)) {
    		return false;
    	}
    	
    	$message = 'Result unknown';
		require_once 'KontorX/Service/Seonetpl/Exception.php';
		throw new KontorX_Service_Seonetpl_Exception($message);

    	// http://seo.net.pl/?module=swl&action=delete_link&link_id=6696277&set_id=91794
    	/*
			action	delete_link
			link_id	6696277
			module	swl
			set_id	91794
		*/
    	// GET
    	// <div class="success_message">
    	
    }
    
	public function setLinksMax($max, $linkId, $linkSetId)
    {
    	$this->authorise();
    	
    	$max = (int) $max;
    	$linkId = (int) $linkId;
    	$linkSetId = (int) $linkSetId;

    	$action = self::SEONET_URI . sprintf('?module=swl&action=set_links_max&link_id=%d&max=%d&set_id=%d&orderby=undefined', $linkId, $max, $linkSetId);

    	$client = $this->getLocalHttpClient();
    	$client->setUri($action);
    	$client->setCookie(self::SESSION_COOKIE_NAME, $this->getSessionCookie());
    	$client->setMethod(Zend_Http_Client::GET);

    	$request = $client->request();
    	
    	// TODO: dodaç sprawdzanie czy result zawiera zmienionà wartoÊç max 
    	
    	return true;

    	// http://seo.net.pl/?module=swl&action=set_links_max&link_id=5441603&max=400&set_id=91794&orderby=undefined
		/*
			action	set_links_max
			link_id	5441603
			max	400
			module	swl
			orderby	undefined
			set_id	91794
		*/
    	// GET
    }
    
	public function setLinksPerday($perday, $linkId, $linkSetId)
    {
    	$this->authorise();
    	
    	$perday = (int) $perday;
    	$linkId = (int) $linkId;
    	$linkSetId = (int) $linkSetId;

    	$action = self::SEONET_URI . sprintf('?module=swl&action=set_links_perday&link_id=%d&perday=%d&set_id=%d&orderby=undefined', $linkId, $perday, $linkSetId);

    	$client = $this->getLocalHttpClient();
    	$client->setUri($action);
    	$client->setCookie(self::SESSION_COOKIE_NAME, $this->getSessionCookie());
    	$client->setMethod(Zend_Http_Client::GET);

    	$request = $client->request();
    	
    	// TODO: dodaç sprawdzanie czy result zawiera zmienionà wartoÊç $perday

    	return true;
    	
    	// http://seo.net.pl/?module=swl&action=set_links_perday&link_id=5441603&perday=11&set_id=91794&orderby=undefined
		/*
			action	set_links_perday
			link_id	5441603
			module	swl
			orderby	undefined
			perday	11
			set_id	91794
		*/
    	// GET
    }
    
	public function setLinksPriority($priority, $linkId, $linkSetId)
    {
    	$this->authorise();
    	
    	$priority = (int) $priority;
    	$linkId = (int) $linkId;
    	$linkSetId = (int) $linkSetId;

    	$action = self::SEONET_URI . sprintf('?module=swl&action=set_link_priority&link_id=%d&priority=%d&set_id=%d&orderby=undefined', $linkId, $priority, $linkSetId);

    	$client = $this->getLocalHttpClient();
    	$client->setUri($action);
    	$client->setCookie(self::SESSION_COOKIE_NAME, $this->getSessionCookie());
    	$client->setMethod(Zend_Http_Client::GET);

    	$request = $client->request();
    	
    	// TODO: dodaç sprawdzanie czy result zawiera zmienionà wartoÊç $priority

    	return true;
    	
    	// http://seo.net.pl/?module=swl&action=set_link_priority&link_id=5441595&priority=6&set_id=91794&orderby=undefined
		/*
			action	set_link_priority
			link_id	5441595
			module	swl
			orderby	undefined
			priority	6
			set_id	91794
		*/
    	// GET
    }
}