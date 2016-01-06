<?php
/**
 * utilityValidateArray.class.php
 * 
 * utilityValidateArray
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateArray
 * @version $Rev: 706 $
 */


/**
 * utilityValidateArray
 * 
 * Validates that a variable is an array. Does not check contents or
 * size only that the variable can be evaluated as an array.
 * 
 * <code>
 * $oValidator = new utilityValidateArray(
 *     array(
 *         // no options for this validator
 *     )
 * );
 * if ( !$oValidator->isValid($var) ) {
 *     print_r($oValidator->getMessages());
 * }
 * </code>
 *
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateArray
 */
class utilityValidateArray extends utilityValidateAbstract {
	
	const MESSAGE_NOT_AN_ARRAY = 'notAnArray';
	
	/**
	 * @see utilityValidateAbstract::initialise()
	 */
	function initialise() {
		$this->addMessageTemplates(self::MESSAGE_NOT_AN_ARRAY, 'Value is not an array');
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
		if ( !is_array($inValue) ) {
			$valid = false;
			$this->addMessage($this->_formatMessage(self::MESSAGE_NOT_AN_ARRAY, $inValue));
		}
		
		return $valid;
	}
}