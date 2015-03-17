# Introduction #

MySQL tree implementation for Zend Framework.

## SQL default schema ##

TO PASTLE

## Implementation ##

```
<?php
class Site_Model_DbTable_Site extends KontorX_Db_Table_Tree_Abstract {
        protected $_name = 'site';
        protected $_level = 'level'; // column name for/to holding nested struture path
}
```

## Db operations ##

### Create new row ###

```
/* @var $dbTable KontorX_Db_Table_Tree_Abstract */
$dbTable = new Site_Model_DbTable_Site();
/* @var $row KontorX_Db_Table_Tree_Row_Abstract */
$row = $table->createRow();
$row->setFromArray($data);
$row->save();
```

### Create children ###

```
/* @var $dbTable KontorX_Db_Table_Tree_Abstract */
$dbTable = new Site_Model_DbTable_Site();
/* @var $row KontorX_Db_Table_Tree_Row_Abstract */
$row = $table->createRow();
$row->setFromArray($data);
$row->setParentRow($rowParent);
$row->save();
```

### Move node ###

```
        /**
         * @param integer $pkNode
         * @param integer $pkParent
         * @return void
         */
        public function move($pkNode, $pkParent = null) {
        $row = $this->findById($pkNode, false);
        if (!$row instanceof KontorX_Db_Table_Tree_Row_Interface) {
            $this->_setStatus(self::FAILURE);
                        $this->_addMessage('Record do not exsists or is not instance of "KontorX_Db_Table_Tree_Row_Interface"');
            return;
        }
        
        $row->setRoot(true);
        if (is_numeric($pkParent)) {
                $parentRow = $this->findById($pkParent, false);
                $row->setParentRow($parentRow);
                $row->setRoot(false);
        }

        try {
            $row->save();
            $this->_setStatus(self::SUCCESS);
        } catch (Zend_Db_Table_Exception $e) {
            $this->_setStatus(self::FAILURE);
                        $this->_addMessage($e->getMessage());
        }
        }
```

### Create Zend\_Navigation ###

```
/* @var $dbTable KontorX_Db_Table_Tree_Abstract */
$dbTable = new Site_Model_DbTable_Site();
/* @var $dbTable KontorX_Db_Table_Tree_Rowset_Abstract */
$rowset = $dbTable->fetchAll();

$recursive = new RecursiveIteratorIterator($rowset, RecursiveIteratorIterator::SELF_FIRST);
$navigation = new KontorX_Navigation_Recursive($rowset);
$navigation->accept(new Promotor_Navigation_Recursive_Visitor_Site());
return $navigation->create();
```