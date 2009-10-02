<?php
require_once 'KontorX/DataGrid/Cell/Abstract.php';
class KontorX_DataGrid_Cell_Tree extends KontorX_DataGrid_Cell_Abstract {

	public function render() {
		$depth = $multiply = ((int) $this->getData('depth')) + 1;

		if (null !== ($m = (int) $this->getAttrib('multiply'))) {
			$multiply *= $m;
		}

		$class = $this->getAttrib('class');
		$style = $this->getAttrib('style');
		$name = $this->getAttrib('name');

		if (null !== ($repeat = $this->getAttrib('repeat'))) {
			$name = str_repeat($repeat, $depth) . $name;
		}

		$span = '<span class="%s" style="%s">%s</span>';
		$span = sprintf($span, $class, $style, $name);

		$span = str_replace(array('{{depth}}','{{multiply}}'),
							array($depth, $multiply),
							$span);

		return $span;
	}
}