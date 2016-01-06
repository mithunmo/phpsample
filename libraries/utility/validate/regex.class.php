<?php
/**
 * utilityValidateRegex.class.php
 * 
 * utilityValidateRegex
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateRegex
 * @version $Rev: 801 $
 */


/**
 * utilityValidateRegex
 * 
 * Validates that a variable matches a regular expression pattern. preg_match
 * is used, therefore any valid PERL Regular Expression can be used. The regex
 * must match at least once. Note that checks are not made for multiple matches.
 * 
 * <code>
 * // basic validation of a North American style phone number
 * $oValidator = new utilityValidateRegex(
 *     array(
 *         utilityValidateRegex::PATTERN => '/^1-[0-9]{3}-[0-9]{3}-[0-9]{4}$/',
 *     )
 * );
 * if ( !$oValidator->isValid($var) ) {
 *     print_r($oValidator->getMessages());
 * }
 * </code>
 *
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateRegex
 */
class utilityValidateRegex extends utilityValidateAbstract {
	
	const PATTERN = 'pattern';
	const MESSAGE_NOT_MATCH = 'notMatch';
	
	/**
	 * @see utilityValidateAbstract::initialise()
	 */
	function initialise() {
		$this->addMessageTemplates(self::MESSAGE_NOT_MATCH, 'Value %value% does not match pattern %pattern%');
		
		if ( !$this->getOptions(self::PATTERN) ) {
			throw new utilityValidateOptionException(__CLASS__, self::PATTERN);
		}
		$this->addMessageVariable(self::PATTERN, $this->getOptions(self::PATTERN));
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
		$res = preg_match($this->getOptions(self::PATTERN), $inValue);
		if ( $res === false || $res === 0 ) {
			$valid = false;
			$this->addMessage($this->_formatMessage(self::MESSAGE_NOT_MATCH, $inValue));
		}
		
		return $valid;
	}
}