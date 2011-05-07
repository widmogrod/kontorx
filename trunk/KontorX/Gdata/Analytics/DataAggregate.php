<?php
/**
 * @author gabriel
 * @version $Id$
 * @link http://code.google.com/intl/pl/apis/analytics/docs/gdata/gdataReferenceAccountFeed.html#accountResponse
 */
class KontorX_Gdata_Analytics_DataAggregate extends Zend_Gdata_Kind_EventEntry
{
    protected $_entryClassName = 'KontorX_Gdata_Analytics_DataAggregate';

    /**
     * one element for each metric in the query 
     * @var array od @see KontorX_Gdata_Analytics_Extension_Metric
     */
    protected $_metrics = array();
    
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
            case $this->lookupNamespace('analytics') . ':' . 'metric':
                $metric = new KontorX_Gdata_Analytics_Extension_Metric();
                $metric->transferFromDOM($child);
                $this->setMetric($metric);
                break;
                
            default:
                parent::takeChildFromDOM($child);
                break;
        }
    }

    
    /**
     * @param KontorX_Gdata_Analytics_Extension_Metric $metric
     */
    public function setMetric(KontorX_Gdata_Analytics_Extension_Metric $metric)
    {
        $this->_metrics[$metric->getName()] = $metric;
    }
    
    /**
     * @param string $name
     * @param mixed $default
     * @return Ambigous <KontorX_Gdata_Analytics_Extension_Metric, mixed>
     */
    public function getMetric($name, $default = null)
    {
        return isset($this->_metrics[$name]) ? $this->_metrics[$name] : $default;
    }
    
    /**
     * Enter description here ...
     * @return array of @see KontorX_Gdata_Analytics_Extension_Metric
     */
    public function getMetrics()
    {
        return $this->_metrics;
    }
    
    public function toArray()
    {
        $result = array();
        
        foreach ($this->_metrics as $name => /* @var $metric KontorX_Gdata_Analytics_Extension_Metric */ $metric)
        {
            $result[$name] = $metric->getValue();
        }
        
        return $result;
    }
}