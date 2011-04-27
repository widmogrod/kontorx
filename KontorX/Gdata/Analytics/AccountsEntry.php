<?php
/**
 * @author gabriel
 * @version $Id$
 * @link http://code.google.com/intl/pl/apis/analytics/docs/gdata/gdataReferenceAccountFeed.html#accountResponse
 */
class KontorX_Gdata_Analytics_AccountsEntry extends Zend_Gdata_Kind_EventEntry
{
    protected $_entryClassName = 'KontorX_Gdata_Analytics_AccountsEntry';

    /**
     * The unique, namespaced profile ID to be used when requesting data from the Google Analytics data feed.
     * @example ga:31254308
     * @var string
     */
    protected $_tableId;
    
    /**
     * The web property ID associated with the profile.
     * @example UA-15671597-1
     * @var numeric
     */
    protected $_webPropertyId;
    
    /**
     * The account ID associated with the profile, used in the tracking code for your web property (e.g. UA-30481-22).
     * @example 15671597 
     * @var numeric
     */
    protected $_accountId;
    
    /**
     * The name of the account associated with the profile.
     * @example mostowy.com.pl
     * @var string
     */
    protected $_accountName;
    
    /**
     * The numeric ID of the profile.
     * @example 31254308
     * @var numeric
     */
    protected $_profileId;
    
    /**
     * The currency type associated with the profile, such as USD (for U.S. dollars)
     * @example USD
     * @var string
     */
    protected $_currency;
    
    /**
     * Setting this value processes your report the data in the time zone that the profile has been configured for. 
     * For example, but setting this value to GMT the day begins at 12am GMT, not 12am PST, which is the default value.
     * @example Europe/Warsaw
     * @var string
     */
    protected $_timezone;
    
    public function __construct($element = null)
    {
        $this->registerAllNamespaces(KontorX_Gdata_Analytics::$namespaces);
        parent::__construct($element);
    }

    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        
        /*
         * TODO: Dodać odpowiednie parametry - by było możliwe generowanie XMLa 
         */
        
//        if ($this->_sendEventNotifications != null) {
//            $element->appendChild($this->_sendEventNotifications->getDOM($element->ownerDocument));
//        }
//        if ($this->_timezone != null) {
//            $element->appendChild($this->_timezone->getDOM($element->ownerDocument));
//        }
//        if ($this->_quickadd != null) {
//            $element->appendChild($this->_quickadd->getDOM($element->ownerDocument));
//        }
        return $element;
    }

    protected $_todo;
    
    protected function takeChildFromDOM(/* @var $child DOMElement */ $child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;

        switch ($absoluteNodeName) 
        {
            case $this->lookupNamespace('analytics') . ':' . 'property';
                $property = new KontorX_Gdata_Analytics_Extension_Property();
                $property->transferFromDOM($child);
                
                $name = $property->getName();
                $this->__set($name, $property);
                
//                var_dump(array($name, (string) $property));
                
//                $name = $property->getName();
//                $method = 'set'.ucfirst($name);
//                $this->$method($property);
                
                break;

            case $this->lookupNamespace('analytics') . ':' . 'tableId';
                $this->_tableId = $child->nodeValue;
                break;
                
            default:
                parent::takeChildFromDOM($child);
                break;
        }
    }

    
    /**
     * The unique, namespaced profile ID to be used when requesting
     * data from the Google Analytics data feed.
     * 
     * @example ga:31254308
     * @return string
     */
    public function getTableId()
    {
        return $this->_tableId;
    }
    
    /**
     * The web property ID associated with the profile.
     * 
     * @example UA-15671597-1
     * @return numeric
     */
    public function getWebPropertyId()
    {
        return $this->_webPropertyId;
    }
    
    /**
     * The account ID associated with the profile, used in the tracking code for your web property (e.g. UA-30481-22).
     * 
     * @example 15671597 
     * @return numeric
     */
    public function getAccountId()
    {
        return $this->_accountId;
    }
    
    /**
     * The name of the account associated with the profile.
     * 
     * @example mostowy.com.pl
     * @return string
     */
    public function getAccountName()
    {
        return $this->_accountName;
    }
    
    /**
     * The numeric ID of the profile.
     * 
     * @example 31254308
     * @return numeric
     */
    public function getProfileId()
    {
        return $this->_profileId;
    }
    
    /**
     * The currency type associated with the profile, such as USD (for U.S. dollars)
     * 
     * @example USD
     * @return string
     */
    public function getCurrency()
    {
        return $this->_currency;
    }
    
    /**
     * Setting this value processes your report the data in the time zone that the profile has been configured for. 
     * For example, but setting this value to GMT the day begins at 12am GMT, not 12am PST, which is the default value.
     * 
     * @example Europe/Warsaw
     * @return string
     */
    public function getTimezone()
    {
        return $this->_timezone;
    }
    
    /*
     * TODO: Dodać settery - by było możliwe generowanie XMLa 
     */

//    /**
//     * @param Zend_Gdata_Calendar_Extension_Timezone $value
//     * @return KontorX_Gdata_Analytics_AccountsEntry Provides a fluent interface
//     */
//    public function setTimezone($value)
//    {
//        $this->_timezone = $value;
//        return $this;
//    }
}