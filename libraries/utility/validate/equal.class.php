<?php
/**
 * utilityValidateEqual.class.php
 * 
 * utilityValidateEqual
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateEqual
 * @version $Rev: 706 $
 */


/**
 * utilityValidateEqual
 * 
 * Validates that a variable is equal to a preset value for example the
 * entered Captcha code is the same as the stored captcha code.
 * 
 * Note: this validator checks for a value not identity. To check if an
 * object or array is exactly equal (identity) use {@link utilityValidateIdentical}.
 * 
 * Note: this validator cannot be used with arrays, objects or resources.
 * 
 * <code>
 * // validates that the int 12 equals the string "12"
 * $oValidator = new utilityValidateEqual(
 *     array(
 *         utilityValidateEqual::TEST_VALUE => '12',
 *     )
 * );
 * if ( $oValidator->isValid(12) ) {
 *     // is valid, because 12 and '12' are equal
 * }
 * </code>
 * 
 * You may get un-expected behaviour if you use this validator to compare
 * integers and strings. PHP will auto-convert an integer to a string
 * representation (or vice-versa) so (as in the example) 12 does equal "12".
 * Consider {@link utilityValidateIdentical} if you require both type and
 * value to match, or use {@link utilityValidateRegex}.
 *
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateEqual
 */
class utilityValidateEqual extends utilityValidateAbstract {
	
	const TEST_VALUE = 'testValue';
	const MESSAGE_NOT_EQUAL = 'notEqual';
	const MESSAGE_VALUE_NOT_TESTABLE = 'valueNotTestable';
	
	/**
	 * @see utilityValidateAbstract::initialise()
	 */
	function initialise() {
		$this->addMessageTemplates(self::MESSAGE_NOT_EQUAL, 'Value %value% does not equal %testValue%');
		$this->addMessageTemplates(self::MESSAGE_VALUE_NOT_TESTABLE, 'Supplied value cannot be tested for equality');
		
		$this->addMessageVariable(self::TEST_VALUE, $this->getOptions(self::TEST_VALUE));
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
		if ( empty($inValue) || is_array($inValue) || is_object($inValue) || is_resource($inValue) ) {
			$valid = false;
			$this->addMessage($this->_formatMessage(self::MESSAGE_VALUE_NOT_TESTABLE, $inValue));
		}
		if ( $valid ) {
			if ( $this->getOptions(self::TEST_VALUE) != $inValue ) {
				$valid = false;
				$this->addMessage($this->_formatMessage(self::MESSAGE_NOT_EQUAL, $inValue));
			}
		}
		return $valid;
	}
}