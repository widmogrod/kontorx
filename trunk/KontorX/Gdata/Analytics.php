<?php
class KontorX_Gdata_Analytics extends Zend_Gdata
{
    const ANALYTICS_ACCOUNTS_URI = 'https://www.google.com/analytics/feeds/accounts/default';
    const ANALYTICS_DATA_URI = 'https://www.google.com/analytics/feeds/data';
    
    const AUTH_SERVICE_NAME = 'ga';
    const NOT_SET = '(not set)';

    protected $_defaultPostUri = self::ANALYTICS_ACCOUNTS_URI;

    /**
     * Namespaces used for Zend_Gdata_Calendar
     *
     * @var array
     */
    public static $namespaces = array(
        array('analytics', 'http://schemas.google.com/analytics/2009', 1, 0)
    );

    /**
     * Create Gdata_Calendar object
     *
     * @param Zend_Http_Client $client (optional) The HTTP client to use when
     *          when communicating with the Google servers.
     * @param string $applicationId The identity of the app in the form of Company-AppName-Version
     */
    public function __construct($client = null, $applicationId = 'MyCompany-MyApp-1.0')
    {
        $this->registerPackage('KontorX_Gdata_Analytics');
        $this->registerPackage('KontorX_Gdata_Analytics_Extension');
        parent::__construct($client, $applicationId);
        $this->_httpClient->setParameterPost('service', self::AUTH_SERVICE_NAME);
    }

    /**
     * Retreive feed object
     *
     * @param mixed $location The location for the feed, as a URL or Query
     * @return KontorX_Gdata_Analytics_AccountsFeed
     */
    public function getAnalyticsAccountsFeed($location = null)
    {
        if ($location == null) {
            $uri = self::ANALYTICS_ACCOUNTS_URI;
        } else if ($location instanceof Zend_Gdata_Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }

        return parent::getFeed($uri, 'KontorX_Gdata_Analytics_AccountsFeed');
    }
    
	/**
     * Retreive feed object
     *
     * @param mixed $location The location for the feed, as a URL or Query
     * @return KontorX_Gdata_Analytics_DataFeed
     */
    public function getAnalyticsDataFeed($location)
    {
        if ($location instanceof Zend_Gdata_Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }

        return parent::getFeed($uri, 'KontorX_Gdata_Analytics_DataFeed');
    }

//    /**
//     * Retreive entry object
//     *
//     * @return Zend_Gdata_Calendar_EventEntry
//     */
//    public function getCalendarEventEntry($location = null)
//    {
//        if ($location == null) {
//            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
//            throw new Zend_Gdata_App_InvalidArgumentException(
//                    'Location must not be null');
//        } else if ($location instanceof Zend_Gdata_Query) {
//            $uri = $location->getQueryUrl();
//        } else {
//            $uri = $location;
//        }
//        return parent::getEntry($uri, 'Zend_Gdata_Calendar_EventEntry');
//    }
//
//
//    /**
//     * Retrieve feed object
//     *
//     * @return Zend_Gdata_Calendar_ListFeed
//     */
//    public function getCalendarListFeed()
//    {
//        $uri = self::CALENDAR_FEED_URI . '/default';
//        return parent::getFeed($uri,'Zend_Gdata_Calendar_ListFeed');
//    }
//
//    /**
//     * Retreive entryobject
//     *
//     * @return Zend_Gdata_Calendar_ListEntry
//     */
//    public function getCalendarListEntry($location = null)
//    {
//        if ($location == null) {
//            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
//            throw new Zend_Gdata_App_InvalidArgumentException(
//                    'Location must not be null');
//        } else if ($location instanceof Zend_Gdata_Query) {
//            $uri = $location->getQueryUrl();
//        } else {
//            $uri = $location;
//        }
//        return parent::getEntry($uri,'Zend_Gdata_Calendar_ListEntry');
//    }
//
//    public function insertEvent($event, $uri=null)
//    {
//        if ($uri == null) {
//            $uri = $this->_defaultPostUri;
//        }
//        $newEvent = $this->insertEntry($event, $uri, 'Zend_Gdata_Calendar_EventEntry');
//        return $newEvent;
//    }
}