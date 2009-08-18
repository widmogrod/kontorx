<?php
class Promotor_Form_Dojo_DisplayGroup extends Zend_Dojo_Form_DisplayGroup {
	public function init() {
		$this->addDecorator('FormElements')
			->addDecorator('HtmlTag', array('tag' => 'dl'))
			->addDecorator('Fieldset')
			->addDecorator('DtDdWrapper')
            ->addDecorator('ContentPane');
	}
}