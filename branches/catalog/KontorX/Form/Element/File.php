<?php
/** Zend_Form_Element_Xhtml */
require_once 'Zend/Form/Element/Xhtml.php';

/**
 * File form element
 * 
 * @category   KontorX
 * @package    KontorX_Form
 * @subpackage Element
 */
class KontorX_Form_Element_File extends Zend_Form_Element_Xhtml
{
    /**
     * Default form view helper to use for rendering
     * @var string
     */
    public $helper = 'formFile';
}
