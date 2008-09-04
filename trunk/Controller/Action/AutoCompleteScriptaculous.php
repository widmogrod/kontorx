<?php
require_once 'Zend/Controller/Action/Helper/AutoComplete/Abstract.php';

/**
 * Create and send Scriptaculous-compatible autocompletion lists
 *
 * @uses       Zend_Controller_Action_Helper_AutoComplete_Abstract
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Zend_Controller_Action_Helper
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class KontorX_Controller_Action_Helper_AutoCompleteJquery extends Zend_Controller_Action_Helper_AutoComplete_Abstract {
    /**
     * Validate data for autocompletion
     * 
     * @param  mixed $data 
     * @return bool
     */
    public function validateData($data)
    {
        if (!is_array($data) && !is_scalar($data)) {
            return false;
        }

        return true;
    }

    /**
     * Prepare data for autocompletion
     * 
     * @param  mixed   $data 
     * @param  boolean $keepLayouts 
     * @throws Zend_Controller_Action_Exception
     * @return string
     */
    public function prepareAutoCompletion($data, $keepLayouts = false)
    {
        if (!$this->validateData($data)) {
            /**
             * @see Zend_Controller_Action_Exception
             */
            require_once 'Zend/Controller/Action/Exception.php';
            throw new Zend_Controller_Action_Exception('Invalid data passed for autocompletion');
        }

        $data = (array) $data;
        $data = '<ul><li>' . implode('</li><li>', $data) . '</li></ul>';

        if (!$keepLayouts) {
            $this->disableLayouts();
        }

        return $data;
    }
}
