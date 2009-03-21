<?php
/**
 * Validate_Compare
 * 
 * @category 	File
 * @package 	KontorX_Validate
 * @version 	0.1.1
 * @license		GNU GPL
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
 */
class KontorX_Validate_Compare extends Zend_Validate_Abstract {
	const NOT_SAME      = 'notSameValue';
	const IS_SAME      = 'isSameValue';

    protected $_comparedValue = null;
    protected $_comparedField = null;
    protected $_sameCorrect = false;

    protected $_messageVariables = array(
        'comparedValue' => '_comparedValue'
    );

    protected $_messageTemplates = array(
        self::NOT_SAME      => 'Values do not match',
        self::IS_SAME      => 'Values are the same',
    );

    public function __construct($comparedValue = null, $sameCorrect = false) {
    	$this->_comparedValue = (string) $comparedValue;
    	$this->_sameCorrect = (bool) $sameCorrect;
    }

    public function setComparedValue($comparedValue) {
    	$this->_comparedValue = (string) $comparedValue;
    }

    public function getComparedValue() {
    	return $this->_comparedValue;
    }

    public function setCompareField($name) {
    	$this->_comparedField = (string) $name;
    }
    
    public function getCompareField () {
    	return $this->_comparedValue;
    }

	public function isValid($value) {
		$this->_setValue($value);
        $comparedValue = $this->getComparedValue();
        $comparedField = $this->getCompareField();

        // test
        if (null !== $comparedField
        		|| !empty($_POST)) {

        	$comparedFieldValue = array_key_exists($comparedField, $_POST)
        		? $_POST[$comparedField] : null;
        }

        if ($value === $comparedValue || $value === $comparedFieldValue) {
        	 if (!$this->_sameCorrect) {
				$this->_error(self::NOT_SAME);
            	return false;
        	 }
        } else {
        	 if ($this->_sameCorrect) {
        	 	$this->_error(self::IS_SAME);
            	return false;
        	 }
        }

        return true;
	}
}
?>