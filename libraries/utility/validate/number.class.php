<?php
/**
 * utilityValidateNumber.class.php
 * 
 * utilityValidateNumber
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateNumber
 * @version $Rev: 706 $
 */


/**
 * utilityValidateNumber
 * 
 * Validates that a variable is a number and optionally falls within a range.
 *
 * <code>
 * // validates that number is greater than 3
 * $oValidator = new utilityValidateNumber(
 *     array(
 *         utilityValidateNumber::MIN => 3
 *     )
 * );
 * if ( !$oValidator->isValid($num) ) {
 *     print_r($oValidator->getMessages());
 * }
 * </code>
 *
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateNumber
 */
class utilityValidateNumber extends utilityValidateAbstract {
	
	const MESSAGE_NOT_A_NUMBER = 'notANumber';
	const MESSAGE_MIN = 'min';
	const MESSAGE_MAX = 'max';
	
	/**
	 * @see utilityValidateAbstract::initialise()
	 */
	function initialise() {
		$this->addMessageTemplates(self::MESSAGE_NOT_A_NUMBER, '%value% is not a number');
		$this->addMessageTemplates(self::MESSAGE_MIN, '%value% is too small, min value %min%');
		$this->addMessageTemplates(self::MESSAGE_MAX, '%value% is too large, max value %max%');
		
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
		if ( !is_numeric($inValue) ) {
			$valid = false;
			$this->addMessage($this->_formatMessage(self::MESSAGE_NOT_A_NUMBER, $inValue));
		}
		
		$inValue = strval(intval($inValue));
		if ( $valid && ($this->getOptions(self::MIN) || $this->getOptions(self::MIN) === 0) ) {
			if ( $inValue < $this->getOptions(self::MIN) ) {
				$valid = false;
				$this->addMessage($this->_formatMessage(self::MESSAGE_MIN, $inValue));
			}
		}
		if ( $valid && ($this->getOptions(self::MAX) || $this->getOptions(self::MAX) === 0) ) {
			if ( $inValue > $this->getOptions(self::MAX) ) {
				$valid = false;
				$this->addMessage($this->_formatMessage(self::MESSAGE_MAX, $inValue));
			}
		}
		return $valid;
	}
}