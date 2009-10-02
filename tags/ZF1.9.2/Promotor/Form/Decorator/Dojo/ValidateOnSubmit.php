<?php
class Promotor_Form_Decorator_Dojo_ValidateOnSubmit extends Zend_Form_Decorator_Abstract {

	protected $_onLoad = 'function(){
		var myForm = dijit.byId("%s");
		dojo.connect(myForm, "onSubmit", function(e){
			if (myForm.isValid()){
				return true;
			} else {
				return myForm.validate();
			}
		});
	}';

	public function render($content) {
		$form = $this->getElement();
		// only for Zend_Form
		if ($form instanceof Zend_Form) {
			$view = $form->getView();
			if (null === ($id = $form->getId())) {
				$id = 'form-' . uniqid();
				$form->setAttrib('id',$id);
			}
	
			$dojo = $view->getHelper('dojo');
	
			$onload = sprintf($this->_onLoad, $id);
			$dojo->addOnLoad($onload);
		}

		return $content;
	}
}