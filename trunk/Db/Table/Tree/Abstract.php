<?php
require_once 'KontorX/Db/Table/Abstract.php';

/**
 * KontorX_Db_Table_Tree_Abstract
 *
 * @category	KontorX
 * @package		KontorX_Db
 * @subpackage	Table
 * @version		0.1.6
 */
class KontorX_Db_Table_Tree_Abstract extends KontorX_Db_Table_Abstract {
	/**
	 * @Overwrite
	 */
	protected $_rowClass = 'KontorX_Db_Table_Tree_Row';

    /**
	 * @Overwrite
	 */
    protected $_rowsetClass = 'KontorX_Db_Table_Tree_Rowset';
    
    /**
     * Nazwa kolumny poziomu zagnieżdżenia
     *
     * @var string
     */
    protected $_level = null;

	/**
	 * Seprarator poziomu zagnieżdżenia
	 *
	 * @var string
	 */
	protected $_separator = '/';

	public function init() {
		if (null === $this->_level) {
			require_once 'KontorX/Db/Table/Tree/Exception.php';
			throw new KontorX_Db_Table_Tree_Exception('Field `$_level` name for nested records is not definded');
		}
		if (!in_array($this->_level, $this->_getCols())) {
            require_once 'KontorX/Db/Table/Tree/Exception.php';
            throw new KontorX_Db_Table_Tree_Exception("Specified column \"$this->_level\" is not in the row");
        }
        parent::init();
	}

	/**
	 * Zwraca nazwe kolumny przechowujacej informacje o zagniezdzeniu
	 *
	 * @return string
	 */
	public function getLevel() {
		return $this->_level;
	}

	/**
	 * Zwraca separator rozdzielający poziom, zagnieżdżenia
	 *
	 * @return string
	 */
	public function getSeparator() {
		return $this->_separator;
	}

//	/**
//     * @Overwrite
//     * 
//     * @param  mixed $key The value(s) of the primary keys.
//     * @return KontorX_Db_Table_Tree_Rowset_Abstract
//     * @throws Zend_Db_Table_Exception
//     */
//	public function find() {
//		return call_user_func_array(array(parent,'find'),func_get_args());
//	}

	/**
     * @Overwrite
     * 
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @param int                               $count  OPTIONAL An SQL LIMIT count.
     * @param int                               $offset OPTIONAL An SQL LIMIT offset.
     * @return KontorX_Db_Table_Tree_Rowset_Abstract The row results per the Zend_Db_Adapter fetch mode.
     */
	public function fetchAll($where = null, $order = null, $count = null, $offset = null) {
		return parent::fetchAll($where, $order, $count, $offset);
	}

	/**
     * @Overwrite
     * 
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @return KontorX_Db_Table_Tree_Row_Abstract The row results per the
     *     Zend_Db_Adapter fetch mode, or null if no row found.
     */
	public function fetchRow($where = null, $order = null) {
		return parent::fetchRow($where, $order);
	}
}