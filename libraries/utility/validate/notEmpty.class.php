<?php
/**
 * utilityValidateNotEmpty.class.php
 * 
 * utilityValidateNotEmpty
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateNotEmpty
 * @version $Rev: 706 $
 */


/**
 * utilityValidateNotEmpty
 * 
 * Validates that a variable is not empty, i.e. it has a value that
 * can be considered as "empty": 0, "0", "", null, "null", false etc.
 * are all considered to be "empty".
 * 
 * <code>
 * $oValidator = new utilityValidateNotEmpty(
 *     array(
 *         // no options to set
 *     )
 * );
 * if ( !$oValidator->isValid($thing) ) {
 *     print_r($oValidator->getMessages());
 * }
 * </code>
 *
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateNotEmpty
 */
class utilityValidateNotEmpty extends utilityValidateAbstract {
	
	const MESSAGE_NOT_EMPTY = 'notEmpty';
	
	/**
	 * @see utilityValidateAbstract::initialise()
	 */
	function initialise() {
		$this->addMessageTemplates(self::MESSAGE_NOT_EMPTY, 'Value is required and cannot be empty');
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
			$this->addMessage($this->_formatMessage(self::MESSAGE_NOT_EMPTY, $inValue));
		}
		if ( $valid && is_string($inValue) ) {
			if ( trim(strtolower($inValue)) === 'null' ) {
				$valid = false;
				$this->addMessage($this->_formatMessage(self::MESSAGE_NOT_EMPTY, $inValue));
			}
		}
		
		return $valid;
	}
}