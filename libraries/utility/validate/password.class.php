<?php
/**
 * utilityValidatePassword.class.php
 * 
 * utilityValidatePassword
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityValidatePassword
 * @version $Rev: 706 $
 */


/**
 * utilityValidatePassword
 * 
 * Validates that a password conforms to a pattern (regex) and is a minimum
 * length. Validation is performed by the string and regex validators. This
 * validator can therefore accept any options for those two validators.
 *
 * <code>
 * // validate password is 8 characters and contains a number
 * $oValidator = new utilityValidatePassword(
 *     array(
 *         utilityValidatePassword::MIN => 8,
 *         utilityValidateRegex::PATTERN => '/[0-9]{1,}/',
 *     )
 * );
 * if ( !$oValidator->isValid($password) ) {
 *     print_r($oValidator->getMessages());
 * }
 * </code>
 * 
 * @package scorpio
 * @subpackage utility
 * @category utilityValidatePassword
 */
class utilityValidatePassword extends utilityValidateAbstract {
	
	const MESSAGE_NOT_A_VALID_PASSWORD = 'notValidPassword';
	
	/**
	 * @see utilityValidateAbstract::initialise()
	 */
	function initialise() {
		$this->addMessageTemplates(self::MESSAGE_NOT_A_VALID_PASSWORD, 'The password entered does not meet the criteria');
	}
	
	/**
	 * @see utilityValidateInterface::isValid()
	 *
	 * @param mixed $inValue
	 * @return boolean
	 */
	function isValid($inValue) {
		$this->setValue($inValue);
		
		$valid = true;
		$oString = new utilityValidateString($this->getOptions(), $this->getTranslationManager());
		if ( !$oString->isValid($inValue) ) {
			$valid = false;
			foreach ( $oString->getMessages() as $message ) {
				$this->addMessage($message);
			}
		}
		unset($oString);
		
		if ( $valid && $this->getOptions(utilityValidateRegex::PATTERN) ) {
			$oRegex = new utilityValidateRegex($this->getOptions(), $this->getTranslationManager());
			if ( !$oRegex->isValid($inValue) ) {
				$valid = false;
				foreach ( $oRegex->getMessages() as $message ) {
					$this->addMessage($message);
				}
			}
			unset($oRegex);
		}
		
		if ( !$valid ) {
			$this->addMessage($this->_formatMessage(self::MESSAGE_NOT_A_VALID_PASSWORD, $inValue));
		}
		
		return $valid;
	}
}