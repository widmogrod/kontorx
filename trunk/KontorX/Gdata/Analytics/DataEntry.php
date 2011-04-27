<?php
/**
 * @author gabriel
 * @version $Id$
 * @link http://code.google.com/intl/pl/apis/analytics/docs/gdata/gdataReferenceAccountFeed.html#accountResponse
 */
class KontorX_Gdata_Analytics_DataEntry extends Zend_Gdata_Kind_EventEntry
{
    protected $_entryClassName = 'KontorX_Gdata_Analytics_DataEntry';

    /**
     * one element for each metric in the query 
     * @var KontorX_Gdata_Analytics_Extension_Metric
     */
    protected $_metric;
    
    /**
     * one element for each metric in the query 
     * @var KontorX_Gdata_Analytics_Extension_Property
     */
    protected $_dimension;
    
    
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
    
    protected function takeChildFromDOM(/* @var $child DOMElement */ $child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;

        switch ($absoluteNodeName) 
        {
            case $this->lookupNamespace('analytics') . ':' . 'metric';
                $property = new KontorX_Gdata_Analytics_Extension_Metric();
                $property->transferFromDOM($child);
                
                $this->__set('metric', $property);
                break;
                
            case $this->lookupNamespace('analytics') . ':' . 'dimension';
                $property = new KontorX_Gdata_Analytics_Extension_Property();
                $property->transferFromDOM($child);
                
                $name = $property->getName();
                $this->__set('dimension', $property);
                break;
                
            default:
                parent::takeChildFromDOM($child);
                break;
        }
    }

    
    /**
     * Enter description here ...
     * @return KontorX_Gdata_Analytics_Extension_Metric
     */
    public function getMetric()
    {
        return $this->_metric;
    }
    
    /**
     * Enter description here ...
     * @return KontorX_Gdata_Analytics_Extension_Property
     */
    public function getDimension()
    {
        return $this->_dimension;
    }
}