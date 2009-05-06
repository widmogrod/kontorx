<?php
require_once 'Zend/Db/Table/Row/Abstract.php';

class KontorX_Db_Table_Row extends Zend_Db_Table_Row_Abstract {
	/**
     * @return mixed The primary key value(s), as an associative array if the
     *     key is compound, or a scalar if the key is single-column.
     */
    protected function _doUpdate()
    {
        /**
         * A read-only row cannot be saved.
         */
        if ($this->_readOnly === true) {
            require_once 'Zend/Db/Table/Row/Exception.php';
            throw new Zend_Db_Table_Row_Exception('This row has been marked read-only');
        }

        /**
         * Get expressions for a WHERE clause
         * based on the primary key value(s).
         */
        $where = $this->_getWhereQuery(false);

        /**
         * Run pre-UPDATE logic
         */
        $this->_update();

        /**
         * Compare the data to the modified fields array to discover
         * which columns have been changed.
         */
        $diffData = array_intersect_key($this->_data, $this->_modifiedFields);

        /**
         * Were any of the changed columns part of the primary key?
         */
        $pkDiffData = array_intersect_key($diffData, array_flip((array)$this->_primary));

        /**
         * Execute cascading updates against dependent tables.
         * Do this only if primary key value(s) were changed.
         */
        if (count($pkDiffData) > 0) {
            $depTables = $this->_getTable()->getDependentTables();
            if (!empty($depTables)) {
                $db = $this->_getTable()->getAdapter();
                $pkNew = $this->_getPrimaryKey(true);
                $pkOld = $this->_getPrimaryKey(false);
                foreach ($depTables as $tableClass) {
//                    try {
//                        Zend_Loader::loadClass($tableClass);
//                    } catch (Zend_Exception $e) {
//                        require_once 'Zend/Db/Table/Row/Exception.php';
//                        throw new Zend_Db_Table_Row_Exception($e->getMessage());
//                    }
                    $t = new $tableClass(array('db' => $db));
                    $t->_cascadeUpdate($this->getTableClass(), $pkOld, $pkNew);
                }
            }
        }

        /**
         * Execute the UPDATE (this may throw an exception)
         * Do this only if data values were changed.
         * Use the $diffData variable, so the UPDATE statement
         * includes SET terms only for data values that changed.
         */
        if (count($diffData) > 0) {
            $this->_getTable()->update($diffData, $where);
        }

        /**
         * Run post-UPDATE logic.  Do this before the _refresh()
         * so the _postUpdate() function can tell the difference
         * between changed data and clean (pre-changed) data.
         */
        $this->_postUpdate();

        /**
         * Refresh the data just in case triggers in the RDBMS changed
         * any columns.  Also this resets the _cleanData.
         */
        $this->_refresh();

        /**
         * Return the primary key value(s) as an array
         * if the key is compound or a scalar if the key
         * is a scalar.
         */
        $primaryKey = $this->_getPrimaryKey(true);
        if (count($primaryKey) == 1) {
            return current($primaryKey);
        }

        return $primaryKey;
    }

    /**
     * Deletes existing rows.
     *
     * @return int The number of rows deleted.
     */
    public function delete()
    {
        /**
         * A read-only row cannot be deleted.
         */
        if ($this->_readOnly === true) {
            require_once 'Zend/Db/Table/Row/Exception.php';
            throw new Zend_Db_Table_Row_Exception('This row has been marked read-only');
        }

        $where = $this->_getWhereQuery();

        /**
         * Execute pre-DELETE logic
         */
        $this->_delete();

        /**
         * Execute cascading deletes against dependent tables
         */
        $depTables = $this->_getTable()->getDependentTables();
        if (!empty($depTables)) {
            $db = $this->_getTable()->getAdapter();
            $pk = $this->_getPrimaryKey();
            foreach ($depTables as $tableClass) {
//                try {
//                    Zend_Loader::loadClass($tableClass);
//                } catch (Zend_Exception $e) {
//                    require_once 'Zend/Db/Table/Row/Exception.php';
//                    throw new Zend_Db_Table_Row_Exception($e->getMessage());
//                }
                $t = new $tableClass(array('db' => $db));
                $t->_cascadeDelete($this->getTableClass(), $pk);
            }
        }

        /**
         * Execute the DELETE (this may throw an exception)
         */
        $result = $this->_getTable()->delete($where);

        /**
         * Execute post-DELETE logic
         */
        $this->_postDelete();

        /**
         * Reset all fields to null to indicate that the row is not there
         */
        $this->_data = array_combine(
            array_keys($this->_data),
            array_fill(0, count($this->_data), null)
        );

        return $result;
    }
}
