<?php
require_once 'KontorX/DataGrid/Row/Interface.php';
abstract class KontorX_DataGrid_Row_Abstract implements KontorX_DataGrid_Row_Interface {

    /**
     * @param array $options
     */
    public function __construct(array $options = null) {
        if (null != $options) {
            $this->setOptions($options);
        }
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
    private $_protectedMethods = array('ColumnName','Data');

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
    protected $_data = array();

    /**
     * @param array $attribs
     * @return void
     */
    public function setData($data) {
        $this->_data = $data;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getData($name = null) {
        if (null === $name) {
            return $this->_data;
        }
        return array_key_exists($name, $this->_data)
            ? $this->_data[$name] : null;
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
     * @param mixed $default
     * @return string
     */
    public function getAttrib($name, $default = null) {
        return array_key_exists($name, $this->_attribs)
            ? $this->_attribs[$name] : $default;
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
        try {
            return $this->render();
        } catch (Exception $e) {
            trigger_error(get_class($e) . "::" . $e->getMessage(), E_USER_ERROR);
        }

        $result = $this->getData($this->getColumnName());
        return "$result";
    }
}