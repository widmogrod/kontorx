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
		'ui' => array(
			'theme_path' => 'js/jsTree/source/themes/',
			'theme_name' => 'checkbox'
		),
		'callback' => array(
		'onchange' =>
'function (NODE, TREE_OBJ) {
	var storeContainer = $("#{{id}}-store");
	var storeElement = $(\'<input type="hidden" name="{{fullyQualifiedName}}[]">\');

	var hasStoreId = function (id) {
		return storeContainer.find(\'[value="\'+id+\'"]\').size() > 0 ? true : false;
	};
	var addStoreId = function (id) {
		if (!hasStoreId(id)) {
			storeContainer.append(storeElement.val(id));
		}
	};
	var removeStoreId = function (id) {
		if (hasStoreId(id)) {
			storeContainer.find(":hidden[value=\'"+id+"\']").remove();
		}
	};
	
	if(TREE_OBJ.settings.ui.theme_name == "checkbox") {
		var $this = $(NODE).is("li") ? $(NODE) : $(NODE).parent();
		var $id = $this.attr("id");
		
		if($this.children("a.unchecked").size() == 0) {
			removeStoreId($id);
			TREE_OBJ.container.find("a").addClass("unchecked");
		}
		$this.children("a").removeClass("clicked");
		if($this.children("a").hasClass("checked")) {
			$this.find("li").andSelf().children("a").removeClass("checked").removeClass("undetermined").addClass("unchecked");
			var state = 0;
			
			$this.find("li").andSelf().each(function(){
				var $id = $(this).attr("id");
				removeStoreId($id);
			});
		}
		else {
			$this.find("li").andSelf().children("a").removeClass("unchecked").removeClass("undetermined").addClass("checked");
			var state = 1;

			// add
			$this.find("li").andSelf().each(function(){
				var $id = $(this).attr("id");
				addStoreId($id);
			});
		}
		$this.parents("li").each(function (i,k) {
			if(state == 1) {
				if($(this).find("a.unchecked, a.undetermined").size() - 1 > 0) {
					$(this).parents("li").andSelf().children("a").removeClass("unchecked").removeClass("checked").addClass("undetermined");
					return false;
				} else {
					$(this).children("a").removeClass("unchecked").removeClass("undetermined").addClass("checked");
				}
			}
			else {
				if($(this).find("a.checked, a.undetermined").size() - 1 > 0) {
					$(this).parents("li").andSelf().children("a").removeClass("unchecked").removeClass("checked").addClass("undetermined");
					return false;
				} else {
					$(this).children("a").removeClass("checked").removeClass("undetermined").addClass("unchecked");
				}
			}
		});
	}
}'
			
		)
	);

	public function render($content) {
		$element = $this->getElement();
		$view = $element->getView();

		$helper = $view->getHelper('jsTree');

		$jsOptions = (array) $this->getOption('jsOptions', $element->getAttrib('jsOptions'));
		if (null !== ($value = $element->getValue())) {
			$jsOptions['selected'] = $value;
		}

		$jsOptions = array_merge_recursive($this->_jsOptions, $jsOptions);
		$jsOptions['fullyQualifiedName'] = $element->getFullyQualifiedName();

		$id = $element->getId();
		$helper->jsTree($id, $jsOptions);
		$helper->render();

		return sprintf('%s<div id="%s-store"></div><div id="%s"></div>', $content, $id, $id);
	}
}