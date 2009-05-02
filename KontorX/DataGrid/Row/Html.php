<?php
require_once 'KontorX/DataGrid/Row/Abstract.php';
class KontorX_DataGrid_Row_Html extends KontorX_DataGrid_Row_Abstract {

    /**
     * Return a context as a html/text string
     * @return string
     */
    public function render() {
        $content = $this->getAttrib('content');
        if (null !== $content) {
            $content = preg_replace("/{([\wd_\-^}]+)}/ie", "\$this->getData('$1')", $content);
        } else {
            $content = $this->getColumnName();
        }
        return $content;
    }
}