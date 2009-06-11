<?php
/**
 * Decorator implementing @see http://www.jstree.com
 * @author gabriel
 *
 */
class KontorX_Form_Decorator_JsTree extends Zend_Form_Decorator_Abstract {
	
	protected $_jsOptions = array(
		'data' => array(
			'type' => 'json'
		),
		'callback' => array(
			'onselect' =>
				'function(node,tree) {
					var checkbox = $(node).find(":checkbox:first");
					if (!checkbox.size()) {
						var chbox = $("<input type=\'checkbox\' checked=\'checked\' name=\'{{fullyQualifiedName}}[]\'/>").val($(node).attr("id"));
						$(node).prepend(chbox);
					}
					if(checkbox.attr("checked")) {
						checkbox.attr("checked",false);
					} else {
						checkbox.attr("checked",true);
					}
				}'
		)
	);

	public function render($content) {
		$element = $this->getElement();
		$view = $element->getView();

		$helper = $view->getHelper('jsTree');

		$jsOptions = (array) $this->getOption('jsOptions', $element->getAttrib('jsOptions'));
		$jsOptions = array_merge_recursive($this->_jsOptions, $jsOptions);
		$jsOptions['fullyQualifiedName'] = $element->getFullyQualifiedName();
		
		$helper->jsTree($element->getId(), $jsOptions);
		$helper->render();

		return $content . sprintf('<div id="%s"></div>', $element->getId());
	}
}