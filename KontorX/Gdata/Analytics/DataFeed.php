<?php
/**
 * @author gabriel
 * @version $Id$
 * @link http://code.google.com/intl/pl/apis/analytics/docs/gdata/gdataReferenceAccountFeed.html#accountResponse
 */
class KontorX_Gdata_Analytics_DataFeed extends Zend_Gdata_Feed
{
    const ENTRIES_KEY = 'entries';
    const AGGREGATES_KEY = 'aggregates';
    
    /**
     * The classname for individual feed elements.
     * @var string
     */
    protected $_entryClassName = 'KontorX_Gdata_Analytics_DataEntry';

    /**
     * The classname for the feed.
     * @var string
     */
    protected $_feedClassName = 'KontorX_Gdata_Analytics_DataFeed';
    
    /**
     * @var KontorX_Gdata_Analytics_DataAggregate
     */
    protected $_aggregate;

    public function __construct($element = null)
    {
        $this->registerAllNamespaces(KontorX_Gdata_Analytics::$namespaces);
        parent::__construct($element);
    }

    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        
        /*
         * TODO Zapisywanie do DOMa 
         */
        
//        if ($this->_timezone != null) {
//            $element->appendChild($this->_timezone->getDOM($element->ownerDocument));
//        }

        return $element;
    }

    protected function takeChildFromDOM(/* @var $child DOMElement */ $child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;

        switch ($absoluteNodeName) 
        {
            case $this->lookupNamespace('analytics') . ':' . 'aggregates':
                $newAggregate = new KontorX_Gdata_Analytics_DataAggregate($child);
                $newAggregate->setHttpClient($this->getHttpClient());
                $newAggregate->setMajorProtocolVersion($this->getMajorProtocolVersion());
                $newAggregate->setMinorProtocolVersion($this->getMinorProtocolVersion());
                $this->setAggregate($newAggregate);
                
                break;
            /*
             * TODO Segmenty 
             */

//            case $this->lookupNamespace('analytics') . ':' . 'segment';
//                break;
//
//            case $this->lookupNamespace('analytics') . ':' . 'definition';
//            
//                $property = new KontorX_Gdata_Analytics_Extension_Property();
//                $property->transferFromDOM($child);
//                
//                $name = $property->getName();
//
//                $this->__set($name, $property);
//                break;

            default:
                parent::takeChildFromDOM($child);
                break;
        }
    }
    
	/*
     * TODO Stworzenie odpowiednich setterów i getterów
     */

//    public function getTimezone()
//    {
//        return $this->_timezone;
//    }
//
//    public function setTimezone($value)
//    {
//        $this->_timezone = $value;
//        return $this;
//    }

	/**
     * @param KontorX_Gdata_Analytics_DataAggregate $aggregate
     */
    public function setAggregate(KontorX_Gdata_Analytics_DataAggregate $aggregate)
    {
        $this->_aggregate = $aggregate;
    }
    
    /**
     * @return KontorX_Gdata_Analytics_DataAggregate
     */
    public function getAggregate()
    {
        return $this->_aggregate;
    }

    public function toArray()
    {
        $result = array(
            self::AGGREGATES_KEY => $this->getAggregate()->toArray(),
            self::ENTRIES_KEY => array()
        );

        $this->rewind();
        while ($this->valid()) 
        {
            $result[self::ENTRIES_KEY][] = $this->current()->toArray();
            $this->next();
        }
        
        return $result;
    }
}