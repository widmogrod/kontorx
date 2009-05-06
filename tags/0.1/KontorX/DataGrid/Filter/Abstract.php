<?php
require_once 'KontorX/DataGrid/Row/Interface.php';
abstract class KontorX_DataGrid_Filter_Abstract implements KontorX_DataGrid_Filter_Interface {

    /**
     * @param array $options
     */
    public function __construct(array $options = null) {
        if (null != $options) {
            $this->setOptions($options);
        }

        $this->_init();
    }

    /**
     * Initialize
     *
     * @return void
     */
    protected function _init() {}

    /**
     * @var Zend_Config
     */
    private $_values = null;

    /**
     * Values
     * @param array $values
     */
    public function setValues(Zend_Config $values) {
        $this->_values = $values;
    }

    /**
     * Return values
     * @return Zend_Config
     */
    public function getValues() {
        return $this->_values;
    }

    /**
     * Set filter value
     * @param mixed $key
     * @param mixed $value
     * @return void
     */
    public function setValue($key, $value = null) {
        if (null === $value) {
            $value = $key;
            $key = $this->getClassName();
        }

        $column = $this->getColumnName();
        $values = $this->_values->filter;
        if (!isset($values->$column)) {
            $values->$column = new Zend_Config(array($key => $value),true);
        }
        $values->$column->$key = $value;
    }

    /**
     * Get filter value
     * @param string $key
     * @return mixed
     */
    public function getValue($key = null) {
        if (null === $key) {
            $key = $this->getClassName();
        }
        $values = $this->_values->filter;
        $column = $this->getColumnName();
        if (isset($values->$column)) {
            return $values->$column->$key;
        }
        return null;
    }

    /**
     * @var string
     */
    private $_name = null;

    /**
     * Ustawia nazwę kolumny
     * @param string $name
     */
    public function setName($name) {
        $this->_name = (string) $name;
    }

    /**
     * Zwraca pełnowymiarową nazwę kolumny
     * @return string
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * @var string
     */
    private $_columnName = null;

    /**
     * Set column displayed name
     * @return void
     */
    public function setColumnName($name) {
        $this->_columnName = $name;
    }

    /**
     * Get column displayed name
     * @return string
     */
    public function getColumnName() {
        return $this->_columnName;
    }

    /**
     * Return class name without prefix
     * @return string
     */
    public function getClassName() {
        return end(explode('_',get_class($this)));
    }

    /**
     * @var array
     */
    private $_protectedMethods = array('Values','Value','ColumnName');

    /**
     * @param array $options
     * @return void
     */
    public function setOptions(array $options) {
    	if (isset($options['columnName'])) {
			$this->setColumnName($options['columnName']);
			unset($options['columnName']);
		}

        foreach ($options as $name => $value) {
            $ucname = ucfirst($name);
            if (!in_array($ucname, $this->_protectedMethods)) {
                $method = 'set'.ucfirst($ucname);
                if (method_exists($this, $method)) {
                    $this->$method($value);
                    unset($options[$name]);
                }
            }
        }

        $this->addAttribs($options);
    }

    /**
     * @var array
     */
    protected $_attribs = array();

    /**
     * @param array $attribs
     * @return void
     */
    public function setAttribs(array $attribs) {
        $this->_attribs = $attribs;
    }

    /**
     * @param array $attribs
     * @return void
     */
    public function addAttribs(array $attribs) {
        $this->_attribs += $attribs;
    }

    /**
     * @return array
     */
    public function getAttribs() {
        return $this->_attribs;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getAttrib($name) {
        return array_key_exists($name, $this->_attribs)
            ? $this->_attribs[$name] : null;
    }

    /**
     * Return attrib key => value
     * @return string
     */
    public function __get($name) {
        return $this->getAttrib($name);
    }

    /**
     * To string
     *
     * @return string
     */
    public function __toString() {
        return $this->render();
    }
}