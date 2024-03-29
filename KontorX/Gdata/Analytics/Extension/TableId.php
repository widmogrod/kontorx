<?php
class KontorX_Gdata_Analytics_Extension_TableId extends Zend_Gdata_Extension
{
    protected $_rootNamespace = 'analytics';
    protected $_rootElement = 'tableId';
    protected $_value = null;

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
//        if ($this->_value !== null) {
//            $element->setAttribute('value', ($this->_value ? "true" : "false"));
//        }
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
        switch ($attribute->localName) {
        case 'value':
            var_dump(array('value' => $attribute->nodeValue));
//            if ($attribute->nodeValue == "true") {
//                $this->_value = true;
//            }
//            else if ($attribute->nodeValue == "false") {
//                $this->_value = false;
//            }
//            else {
//                throw new Zend_Gdata_App_InvalidArgumentException("Expected 'true' or 'false' for gCal:selected#value.");
//            }
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
     * @return Zend_Gdata_Extension_SendEventNotifications The element being modified.
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

}