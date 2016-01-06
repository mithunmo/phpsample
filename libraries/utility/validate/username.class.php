<?php
/**
 * utilityValidateUsername.class.php
 * 
 * utilityValidateUsername
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateUsername
 * @version $Rev: 706 $
 */


/**
 * utilityValidateUsername
 * 
 * Validates that a username conforms to a certain format. Validation is
 * performed using the string and regex validators and can therefore
 * accept any options that they can.
 * 
 * <code>
 * // simple standalone example
 * $oValidator = new utilityValidateUsername(
 *     array(
 *         utilityValidateUsername::MIN => 5,
 *         utilityValidateUsername::MAX => 30,
 *         utilityValidateRegex::PATTERN => '/\W+/i'
 *     )
 * );
 * if ( !$oValidator->isValid($user) ) {
 *     print_r($oValidator->getMessages());
 * }
 * </code>
 *
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateUsername
 */
class utilityValidateUsername extends utilityValidateAbstract {
	
	const MESSAGE_NOT_A_VALID_USERNAME = 'notValidUsername';
	
	/**
	 * @see utilityValidateAbstract::initialise()
	 */
	function initialise() {
		$this->addMessageTemplates(self::MESSAGE_NOT_A_VALID_USERNAME, 'The username entered does not meet the criteria');
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
			$this->addMessage($this->_formatMessage(self::MESSAGE_NOT_A_VALID_USERNAME, $inValue));
		}
		
		return $valid;
	}
}