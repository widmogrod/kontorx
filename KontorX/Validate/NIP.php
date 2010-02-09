<?php

require_once 'Zend/Validate/Abstract.php';

require_once 'identificationNumber/identificationNumberValidator.class.php';
require_once 'identificationNumber/nip/nipValidator.class.php';

/**
 * KontorX_Validate_NIP
 * 
 * @category 	KontorX
 * @package 	KontorX_Validate
 * @version 	0.1.1
 * @license		GNU GPL
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
 */
class KontorX_Validate_NIP extends Zend_Validate_Abstract {
	const TO_SHORT_OR_LONG  = 'nipToShotOrLon';
	const IS_INCORRECT      = 'nipIsIncorrect';
	const IS_ON_BLACKLIST   = 'nipIsOnBlacklist';

    // TODO: TO  english
    protected $_messageTemplates = array(
        self::TO_SHORT_OR_LONG 	=> "Podany numer NIP ma nieprawdłową ilosć znaków",
		self::IS_INCORRECT		=> "Podany numer NIP jest nieprawidłowy",
		self::IS_ON_BLACKLIST	=> "Podany numer NIP znajduje się na liście niepoprawnych numerów",
    );

	public function isValid($value) {
		$this->_setValue($value);

		$nip = new nipValidator($value);
		if ($nip->isValid()) {
			return true;
		}

		if (!$nip->hasErrors()) {
			$this->_error(self::IS_INCORRECT);
			return false;
		}

		foreach ($nip->getErrors() as $error) {
			switch ($error['code']) {
				case 0: $this->_error(self::TO_SHORT_OR_LONG); break;
				case 1: $this->_error(self::IS_INCORRECT); break;
				case 2: $this->_error(self::IS_ON_BLACKLIST); break;
			}
		}
		return false;
	}
}
?>