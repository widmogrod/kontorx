<?php
require_once 'KontorX/Db/Table/Abstract.php';

/**
 * KontorX_Db_Table_Tree_Abstract
 */
class KontorX_Db_Table_Tree_Abstract extends KontorX_Db_Table_Abstract {

    protected $_rowClass = 'KontorX_Db_Table_Tree_Row';

    protected $_rowsetClass = 'KontorX_Db_Table_Tree_Rowset';
    
    /**
     * Nazwa kolumny poziomu zagnieżdżenia
     * @var string
     */
    protected $_level = null;

    /**
     * Seprarator poziomu zagnieżdżenia
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

    public function select() {
    	/**
    	 * Poprawne przejście iteratora @see KontorX_Db_Table_Tree_Rowset_Abstract,
    	 * wymaga posortowania kolumny 'leve' od wartości NULL czyli rekordów głównych 'root'
    	 */
    	return parent::select()->order($this->_level . ' ASC');
    }
    
    /**
     * Zwraca nazwe kolumny przechowujacej informacje o zagniezdzeniu
     * @return string
     */
    public function getLevel() {
            return $this->_level;
    }

    /**
     * Zwraca separator rozdzielający poziom, zagnieżdżenia
     * @return string
     */
    public function getSeparator() {
            return $this->_separator;
    }
}