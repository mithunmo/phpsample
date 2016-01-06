<?php
/**
 * utilityValidateString.class.php
 * 
 * utilityValidateString
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateString
 * @version $Rev: 801 $
 */


/**
 * utilityValidateString
 * 
 * Validates that a variable is a string and optionally falls within a range.
 * This validator is often coupled with the {@link utilityValidateRegex} to
 * enforce a particular type of string.
 * 
 * <code>
 * // validate a string is 5 or 6 characters
 * $oValidator = new utilityValidateString(
 *     array(
 *         utilityValidateString::MIN => 5,
 *         utilityValidateString::MAX => 6,
 *     )
 * );
 * if ( !$oValidator->isValid($string) ) {
 *     print_r($oValidator->getMessages());
 * }
 * </code>
 *
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateString
 */
class utilityValidateString extends utilityValidateAbstract {
	
	const MESSAGE_NOT_A_STRING = 'notAString';
	const MESSAGE_MIN = 'min';
	const MESSAGE_MAX = 'max';
	
	/**
	 * @see utilityValidateAbstract::initialise()
	 */
	function initialise() {
		$this->addMessageTemplates(self::MESSAGE_NOT_A_STRING, 'Value "%value%" is not a string');
		$this->addMessageTemplates(self::MESSAGE_MIN, '"%value%" is too short, min length is %min% chars');
		$this->addMessageTemplates(self::MESSAGE_MAX, '"%value%" is too long, max length is %max% chars');
		
		$this->addMessageVariable(self::MESSAGE_MIN, $this->getOptions(self::MIN));
		$this->addMessageVariable(self::MESSAGE_MAX, $this->getOptions(self::MAX));
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
		if ( !is_string($inValue) ) {
			$valid = false;
			$this->addMessage($this->_formatMessage(self::MESSAGE_NOT_A_STRING, $inValue));
		}
		
		$length = strlen($inValue);
		if ( $valid && ($this->getOptions(self::MIN) || $this->getOptions(self::MIN) === 0) ) {
			if ( $length < $this->getOptions(self::MIN) ) {
				$valid = false;
				$this->addMessage($this->_formatMessage(self::MESSAGE_MIN, $inValue));
			}
		}
		if ( $valid && ($this->getOptions(self::MAX) || $this->getOptions(self::MAX) === 0) ) {
			if ( $length > $this->getOptions(self::MAX) ) {
				$valid = false;
				$this->addMessage($this->_formatMessage(self::MESSAGE_MAX, $inValue));
			}
		}
		return $valid;
	}
}