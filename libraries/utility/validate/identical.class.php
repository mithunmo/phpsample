<?php
/**
 * utilityValidateIdentical.class.php
 * 
 * utilityValidateIdentical
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateIdentical
 * @version $Rev: 706 $
 */


/**
 * utilityValidateIdentical
 * 
 * Validates that a variable is identical (===) to a preset value. This
 * validator can be used with arrays and objects.
 * 
 * Note: to check simply strings or numbers consider {@link utilityValidateEqual}
 * or {@link utilityValidateRegex}.
 * 
 * <code>
 * // validate that variable matches the string "12"
 * $oValidator = new utilityValidateIdentical(
 *     array(
 *         utilityValidateIdentical::TEST_VALUE => '12',
 *     )
 * );
 * if ( !$oValidator->isValid(12) ) {
 *     print_r($oValidator->getMessages());
 * }
 * </code>
 *
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateIdentical
 */
class utilityValidateIdentical extends utilityValidateAbstract {
	
	const TEST_VALUE = 'testValue';
	const MESSAGE_NOT_IDENTICAL = 'notIdentical';
	const MESSAGE_VALUE_NOT_TESTABLE = 'valueNotTestable';
	
	/**
	 * @see utilityValidateAbstract::initialise()
	 */
	function initialise() {
		$this->addMessageTemplates(self::MESSAGE_NOT_IDENTICAL, 'Value is not identical to test value');
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
		if ( empty($inValue) ) {
			$valid = false;
			$this->addMessage($this->_formatMessage(self::MESSAGE_VALUE_NOT_TESTABLE, $inValue));
		}
		if ( $valid ) {
			if ( $this->getOptions(self::TEST_VALUE) !== $inValue ) {
				$valid = false;
				$this->addMessage($this->_formatMessage(self::MESSAGE_NOT_IDENTICAL, $inValue));
			}
		}
		return $valid;
	}
}