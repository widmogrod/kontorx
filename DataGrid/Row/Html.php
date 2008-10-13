<?php
require_once 'KontorX/DataGrid/Row/Abstract.php';
class KontorX_DataGrid_Row_Html extends KontorX_DataGrid_Row_Abstract {

	/**
	 * Return a context as a html/text string
	 *
	 * @return string
	 */
	public function render() {
		$options = $this->getOptions();
		if (isset($options['content'])) {
			$content = $options['content'];
		} else {
			$content = "<!-- KontorX_DataGrid_Row_Html::render() \$options['content'] was not set-->";
		}
		return preg_replace("/{([\wd_\-^}}]+)}/ie", "\$this->getData('\\1')", $content);
	}
}