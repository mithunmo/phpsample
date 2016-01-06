<?php
/**
 * utilityValidateEmailAddress.class.php
 * 
 * utilityValidateEmailAddress
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateEmailAddress
 * @version $Rev: 706 $
 */


/**
 * utilityValidateEmailAddress
 * 
 * Validates that a variable is a valid email address. This validator uses
 * the PHP filter_var::VALIDATE_EMAIL with an additional check to catch
 * cases such as me@example that the default filter will allow through.
 * 
 * <code>
 * $oValidator = new utilityValidateEmailAddress(
 *     array(
 *         // no options for this validator
 *     )
 * );
 * if ( !$oValidator->isValid($email) ) {
 *     print_r($oValidator->getMessages());
 * }
 * </code>
 *
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateEmailAddress
 */
class utilityValidateEmailAddress extends utilityValidateAbstract {
	
	const MESSAGE_NOT_AN_EMAILADDRESS = 'notAnEmailAddress';
	
	/**
	 * @see utilityValidateAbstract::initialise()
	 */
	function initialise() {
		$this->addMessageTemplates(self::MESSAGE_NOT_AN_EMAILADDRESS, 'Value %value% is not a valid email address');
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
		$oFilter = utilityInputFilter::filterValidateEmail();
		$filtered = $oFilter->doFilter($inValue);
		if ( !$filtered || strpos($inValue, '.') === false ) {
			$valid = false;
			$this->addMessage($this->_formatMessage(self::MESSAGE_NOT_AN_EMAILADDRESS, $inValue));
		}
		unset($oFilter);

		return $valid;
	}
}