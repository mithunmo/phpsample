<?php
/**
 * utilityValidateInArray.class.php
 * 
 * utilityValidateInArray
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateInArray
 * @version $Rev: 706 $
 */


/**
 * utilityValidateInArray
 * 
 * Validates that a variable is in a pre-defined array of values. The value can
 * be any valid array value. The validation array should be a simple array - not
 * a nested array (but you can validate nested arrays e.g. does a nested array
 * match a defined nested array). For strict checks (i.e. object properties or
 * arrays) set the option STRICT_CHECK to true.
 * 
 * <code>
 * // validate that a variable is one of the values
 * $oValidator = new utilityValidateInArray(
 *     array(
 *         utilityValidateInArray::VALID_VALUES => array(1, 2, 3, 4, 5, 6),
 *     )
 * );
 * if ( !$oValidator->isValid(45) ) {
 *     print_r($oValidator->getMessages());
 * }
 * </code>
 *
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateInArray
 */
class utilityValidateInArray extends utilityValidateAbstract {
	
	const VALID_VALUES = 'values';
	const MESSAGE_NOT_IN_ARRAY = 'notInArray';
	
	/**
	 * @see utilityValidateAbstract::initialise()
	 */
	function initialise() {
		$this->addMessageTemplates(self::MESSAGE_NOT_IN_ARRAY, 'Value %value% is not a valid selection');
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
		if ( !in_array($inValue, $this->getOptions(self::VALID_VALUES), $this->getOptions(self::STRICT_CHECK)) ) {
			$valid = false;
			$this->addMessage($this->_formatMessage(self::MESSAGE_NOT_IN_ARRAY, $inValue));
		}
		
		return $valid;
	}
}