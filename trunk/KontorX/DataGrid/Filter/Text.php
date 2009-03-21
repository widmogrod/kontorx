<?php
require_once 'KontorX/DataGrid/Filter/Abstract.php';
class KontorX_DataGrid_Filter_Text extends KontorX_DataGrid_Filter_Abstract {

    /**
     * @param KontorX_DataGrid_Adapter_Interface $adapter
     * @return void
     */
    public function filter(KontorX_DataGrid_Adapter_Interface $adapter) {
        if (!$adapter instanceof KontorX_DataGrid_Adapter_DbTable
            && !$adapter instanceof KontorX_DataGrid_Adapter_DbSelect) {
            require_once 'KontorX/DataGrid/Exception.php';
            throw new KontorX_DataGrid_Exception("Wrong filter adapter");
        }

        $select = $adapter->getSelect();
        $column = $this->getColumnName();
        $text = $this->getValue();

        if (strlen($text)) {
            $adapter->getSelect()
            ->where("$column LIKE ?", "%$text%");
        }
    }

    /**
     * @return string
     */
    public function render() {
        $text = $this->getValue();
        $column = $this->getColumnName();
        $filter = $this->getClassName();
        $class = $this->getAttrib('class');

        $format = '<input type="text" name="filter[%s][%s]" value="%s" class="%s" />';
        return sprintf($format, $column, $filter, $text, $class);
    }
}