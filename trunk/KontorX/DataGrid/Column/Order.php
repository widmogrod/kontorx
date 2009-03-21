<?php
require_once 'KontorX/DataGrid/Column/Abstract.php';
class KontorX_DataGrid_Column_Order extends KontorX_DataGrid_Column_Abstract {
    protected function _init() {
        require_once 'KontorX/DataGrid/Filter/Order.php';
        $filter = new KontorX_DataGrid_Filter_Order($this->getAttribs());

        $this->addFilter($filter);
    }

    public function render() {
        // switching type of order..
        $orderCurrent = strtolower($this->getValue());
        switch ($orderCurrent) {
            default:
            case 'null':
                $orderNext    = 'asc';
                $orderCurrent = 'null';
                break;
            case 'asc':
                $orderNext = 'desc';
                break;
            case 'desc':
                $orderNext = 'null';
                break;
        }

        // klonowanie wzrtosci i zmiana atrybotow
        // umozliwia ich aktywacje po kliknieciu linku!
        $backup = $this->getValues();
        $this->setValue($orderNext);
        $values = clone $this->getValues();
        $this->setValues($backup);

        $href = $this->getAttrib('href');
        $href = rtrim($href, '?') . '?';
        $href .= http_build_query($values->toArray());

        $format = '<a class="column column_order" href="%s">%s <span class="order order-%s">%s</span></a>';
        return sprintf($format, $href, $this->getName(), $orderCurrent, $orderCurrent);
    }
}