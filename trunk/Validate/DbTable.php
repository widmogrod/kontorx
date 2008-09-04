<?php
require_once 'Zend/Validate/Abstract.php';

/**
 * KontorX_Validate_DbTable
 * 
 * @category 	File
 * @package 	KontorX_Validate
 * @version 	0.1.2
 * @license		GNU GPL
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
 */
class KontorX_Validate_DbTable extends Zend_Validate_Abstract {
	const NOT_IN_DB      = 'notInDb';
	const IS_IN_DB      = 'isInDb';

    protected $_messageTemplates = array(
        self::NOT_IN_DB      => 'Values do not exsists in db table',
        self::IS_IN_DB      => 'Values exsists in db table',
    );

    /**
     * Enter description here...
     *
     * @var Zend_Db_Table_Abstract
     */
    private $_table = null;
    protected $_where = null;
    protected $_ifExsistsInTableIsValid = null;
    
    protected $_defaultMessage = null; 
    
    public function __construct(Zend_Db_Table_Abstract $table, $where, $ifExsistsInTableIsValid = false, $message = null) {
    	$this->_table = $table;

    	if (empty($where)) {
    		$message = "\$where attribute can not be empty";
    		require_once 'Zend/Validate/Exception.php';
    		throw new Zend_Validate_Exception($message);
    	}

    	$this->_where = $where;
    	
    	$this->_ifExsistsInTableIsValid = (bool) $ifExsistsInTableIsValid;

    	if (null !== $message) {
    		$this->_defaultMessage = (string) $message;
    	}
    }

	public function isValid($value) {
		$this->_setValue($value);

        $row = $this->_table->fetchRow($this->_where);

        switch (true) {
        	case $this->_ifExsistsInTableIsValid && null === $row:
        		if (null === $this->_defaultMessage) {
        			$this->_error(self::NOT_IN_DB);
        		} else {
					$this->_errors[]              = self::NOT_IN_DB;
					$this->_messages[$messageKey] = $this->_defaultMessage;
        		}
            	return false;

        	case $this->_ifExsistsInTableIsValid && null !== $row:
            	return true;

            case !$this->_ifExsistsInTableIsValid && null === $row:
            	return true;

        	case !$this->_ifExsistsInTableIsValid && null !== $row:
        		if (null === $this->_defaultMessage) {
        			$this->_error(self::IS_IN_DB);
        		} else {
					$this->_errors[]              = self::IS_IN_DB;
					$this->_messages[$messageKey] = $this->_defaultMessage;
        		}
            	return false;
        }

				
        return true;
	}
}
?>