<?php
class Promotor_Form_Scaffold_Remove extends Zend_Form {
	public function init() {
		$this->setMethod('post');
		
		$subForm = new Zend_Form_SubForm();
		$subForm->setAttribs(array(
			'legend' => 'Potwierdzenie usuwania rekordu'
		));

		$subForm->addElement('checkbox','delete', array(
			'label' => 'Czy usunąć rekord',
			'description' => 'Usunięcie rekordu jest nieodwracalne',
			'required' => true,
			'validators' => array(
				'greaterThan' => array('validator' => new Zend_Validate_GreaterThan(0))
			)));
			
		$this->addSubForm($subForm,'delete');
	}
}