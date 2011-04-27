<?php
class KontorX_Gdata_Analytics_Extension_Property extends Zend_Gdata_Extension
{
    protected $_rootNamespace = 'analytics';
    protected $_rootElement = 'property';
    protected $_value = null;
    protected $_name = null;

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
            $element->setAttribute($this->_name, $this->_value);
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

            default:
                parent::takeAttributeFromDOM($attribute);
        }
    }

    /**
     * Get the value for this element's Value attribute.
     *
     * @return string The requested attribute.
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Set the value for this element's Value attribute.
     *
     * @param string $value The desired value for this attribute.
     * @return KontorX_Gdata_Analytics_Extension_Property The element being modified.
     */
    public function setValue($value)
    {
        $this->_value = $value;
        return $this;
    }

    /**
     * Magic toString method allows using this directly via echo
     * Works best in PHP >= 4.2.0
     */
    public function __toString()
    {
        return $this->getValue();
    }

    public function setName($name)
    {
        $name = str_replace('ga:', '', $name);
        $this->_name = $name;
    }
    
    public function getName()
    {
        return $this->_name;
    }
}