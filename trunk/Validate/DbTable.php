<?php
require_once 'Zend/Validate/Abstract.php';

/**
 * KontorX_Validate_DbTable
 *
 * @category 	File
 * @package 	KontorX_Validate
 * @version 	0.1.2
 * @license	GNU GPL
 * @author 	Gabriel `widmogror` Habryn, widmogrod@gmail.com
 */
class KontorX_Validate_DbTable extends Zend_Validate_Abstract {
//    const NOT_IN_DB = 'notInDb';
    const RECORD_EXSISTS_IN_TABLE = 'recordExsistsInTable';

    const REQUEST = 'REQUEST';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::RECORD_EXSISTS_IN_TABLE => "'%value%' exsists in db table"
    );

    /**
     * @param mixced $table
     * @param string $where
     * @param bool $uniqValue
     */
    public function __construct($table = null, $where = null, $uniqValue = null) {
        if (is_array($table) && null === $where && null === $uniqValue) {
            $this->setOptions($table);
        } else {
            $this->setTable($table);
            $this->setWhere($where);
            $this->setUniqValue($uniqValue);
        }
    }

    /**
     * @param array $options
     * @return KontorX_File_Write
     */
    public function setOptions(array $options) {
        foreach ($options as $name => $value) {
            $method = 'set'.ucfirst($name);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    /**
     * @var Zend_Db_Table_Abstract
     */
    private $_table = null;

    /**
     * @param Zend_Db_Table_Abstract $table
     * @var void
     */
    public function setTable($table) {
        if (is_string($table)) {
            Zend_Loader::loadClass($table);
            $table = new $table();
        } elseif (is_array($table)) {
            // TODO
        }

        if (!$table instanceof Zend_Db_Table_Abstract) {
            $message = "table is not instance of Zend_Db_Table_Abstract";
            require_once 'Zend/Validate/Exception.php';
            throw new Zend_Validate_Exception($message);
        }

        $this->_table = $table;
    }

    /**
     * @return Zend_Db_Table_Abstract
     */
    public function getTable() {
        return $this->_table;
    }

    /**
     * @var string
     */
    protected $_where = null;

    /**
     * @param string $where
     * @return void
     */
    public function setWhere($where) {
        $this->_where = (string) $where;
    }

    /**
     * @return string
     */
    public function getWhere() {
        return $this->_where;
    }

    /**
     * @var array
     */
    protected $_attribs = null;

    /**
     * @param array $where
     * @return void
     */
    public function setAttribs($attribs) {
        if (is_string($attribs)) {
            switch (strtoupper($attribs)) {
                default:
                case self::REQUEST:
                    $attribs = Zend_Controller_Front::getInstance()->getRequest()->getParams();
                    break;
            }
        }
        $this->_attribs = $attribs;
    }

    /**
     * @return array
     */
    public function getAttribs() {
        if (null === $this->_attribs) {
            $this->setAttribs(self::REQUEST);
        }
        return $this->_attribs;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getAttrib($name) {
        return array_key_exists($name, $this->getAttribs())
            ? $this->_attribs[$name] : null;
    }

    /**
     * @var bool
     */
    protected $_uniqValue = true;

    /**
     * @param bool $flag
     * @return void
     */
    public function setUniqValue($flag = true) {
        $this->_uniqValue = (bool) $flag;
    }

    /**
     * @return bool
     */
    public function isUniqValue() {
        return $this->_uniqValue;
    }

    /**
     * @Override
     * @return bool
     */
    public function isValid($value) {
        $this->_setValue($value);

        if (null === ($where = $this->getWhere())) {
            $message = "Where is not set";
            require_once 'Zend/Validate/Exception.php';
            throw new Zend_Validate_Exception($message);
        }

        $where = preg_replace('/:([\wd_^:]+)/e', "\$this->_getQuotedAttrib('$1')", $where);

        $table = $this->getTable();
        $where = $table->select()->where($where, $value);
        $row = $table->fetchRow($where);

        if (null !== $row) {
            if ($this->isUniqValue()) {
                $this->_error(self::RECORD_EXSISTS_IN_TABLE);
                return false;
            }
        }

        return true;
    }

    /**
     * @var KontorX_Filter_MagicQuotes
     */
    private $_filter = null;

    /**
     * @param string $name
     * @return string
     */
    protected function _getQuotedAttrib($name) {
        if (null === ($attr = $this->getAttrib($name))) {
            return null;
        }
        if (null === $this->_filter) {
            require_once 'KontorX/Filter/MagicQuotes.php';
            $this->_filter = new KontorX_Filter_MagicQuotes();
        }
        $attr = $this->_filter->filter($attr);
        return $this->getTable()->getAdapter()->quoteInto("?", $attr);
    }
}