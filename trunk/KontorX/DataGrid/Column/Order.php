<?php
require_once 'KontorX/DataGrid/Column/ViewHelper.php';
class KontorX_DataGrid_Column_Order extends KontorX_DataGrid_Column_ViewHelper {

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
                $orderNext = 'group';
                break;
            case 'group':
                $orderNext = 'null';
                break;
        }

        /**
         * klonowanie wzrtosci i zmiana atrybotow
         * umozliwia ich aktywacje po kliknieciu linku!
         */
        $backup = $this->getValue();
        $this->setValue($orderNext);
        $values = clone $this->getValues();
        $this->setValue($backup);

        if (null === ($href = $this->getAttrib('href'))) {
        	$router = $this->getAttrib('router');
        	$params = (array) $this->getAttrib('params');
        	$view = $this->getView();
			$href = $view->url($params, $router);
        }

        $href = rtrim($href, '?') . '?';
        $href .= http_build_query($values->toArray());

        $format  = '<div class="kx_column">';
//        $format .= '<a class="kx_group" href="%s">*</a>';
        $format .= '<a class="kx_column_order" href="%s">%s <span class="kx_order kx_order-%s">%s</span></a>';
        $format .= '</div>';

        return sprintf($format, $href, $this->getName(), $orderCurrent, $orderCurrent);
    }
}