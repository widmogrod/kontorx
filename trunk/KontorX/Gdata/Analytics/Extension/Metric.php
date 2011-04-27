<?php
class KontorX_Gdata_Analytics_Extension_Metric extends Zend_Gdata_Extension
{
    protected $_rootNamespace = 'analytics';
    protected $_rootElement = 'metric';
    
    protected $_value = null;
    protected $_name = null;
    protected $_confidenceInterval = null;
    protected $_type = null;

    /**
     * Constructs a new Zend_Gdata_Extension_SendEventNotifications object.
     * @param bool $value (optional) SendEventNotifications value as URI.
     */
    public function __construct($value = null)
    {
        $this->registerAllNamespaces(KontorX_Gdata_Analytics::$namespaces);
        parent::__construct();
        $this->_value = $value;
    }

    /**
     * Retrieves a DOMElement which corresponds to this element and all
     * child properties.  This is used to build an entry back into a DOM
     * and eventually XML text for sending to the server upon updates, or
     * for application storage/persistence.
     *
     * @param DOMDocument $doc The DOMDocument used to construct DOMElements
     * @return DOMElement The DOMElement representing this element and all
     * child properties.
     */
    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        if (null !== $this->_value && null !== $this->_name) 
        {
            $element->setAttribute('name', $this->_name);
            $element->setAttribute('value', $this->_value);
            $element->setAttribute('confidenceInterval', $this->_confidenceInterval);
            $element->setAttribute('type', $this->_type);
        }
        return $element;
    }

    /**
     * Given a DOMNode representing an attribute, tries to map the data into
     * instance members.  If no mapping is defined, the name and value are
     * stored in an array.
     *
     * @param DOMNode $attribute The DOMNode attribute needed to be handled
     */
    protected function takeAttributeFromDOM($attribute)
    {
        switch ($attribute->localName) 
        {
            case 'name':
                $this->setName($attribute->nodeValue);
                break;
            case 'value':
                $this->setValue($attribute->nodeValue);
                break;
            case 'confidenceInterval':
                $this->setConfidenceInterval($attribute->nodeValue);
                break;
            case 'type':
                $this->setType($attribute->nodeValue);
                break;

            default:
                parent::takeAttributeFromDOM($attribute);
        }
    }
    
    public function setValue($value)
    {
        $this->_value = $value;
        return $this;
    }

    /**
     * the aggregate value for the query for that metric (e.g. 24 for 24 pageviews)
     *
     * @return string
     */
    public function getValue()
    {
        return $this->_value;
    }

    public function setName($name)
    {
        $name = str_replace('ga:', '', $name);
        $this->_name = $name;
    }
    
    /**
     * the name of the metric
     * 
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
    
    public function setConfidenceInterval($confidenceInterval)
    {
        $this->_confidenceInterval = $confidenceInterval;
    }
    
    /**
     * the confidence interval, or range of values likely to include the correct value. 
     * See Sampling & Confidence Intervals below for details.
     *  
     * @return float
     */
    public function getConfidenceInterval()
    {
        return $this->_confidenceInterval;
    }
    
    public function setType($type)
    {
        $this->_type = $type;
    }
    
    /**
     * the type of the value returned. Can be: currency, float, percent, 
     * time, us_currency, an unknown type, or not set
     * 
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }
    
	/**
     * Magic toString method allows using this directly via echo
     * Works best in PHP >= 4.2.0
     */
    public function __toString()
    {
        return $this->getValue();
    }
}