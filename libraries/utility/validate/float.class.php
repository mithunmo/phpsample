<?php
/**
 * utilityValidateFloat.class.php
 * 
 * utilityValidateFloat
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateFloat
 * @version $Rev: 706 $
 */


/**
 * utilityValidateFloat
 * 
 * Validates that a variable is a float and optionally falls within a range.
 *
 * <code>
 * // validate a float is between 1.5 and 1.55
 * $oValidator = new utilityValidateFloat(
 *     array(
 *         utilityValidateFloat::MIN => 1.5,
 *         utilityValidateFloat::MAX => 1.55,
 *     )
 * );
 * if ( !$oValidator->isValid($time) ) {
 *     print_r($oValidator->getMessages());
 * }
 * </code>
 *
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateFloat
 */
class utilityValidateFloat extends utilityValidateAbstract {
	
	const MESSAGE_NOT_A_FLOAT = 'notAFloat';
	const MESSAGE_MIN = 'min';
	const MESSAGE_MAX = 'max';
	
	/**
	 * @see utilityValidateAbstract::initialise()
	 */
	function initialise() {
		$this->addMessageTemplates(self::MESSAGE_NOT_A_FLOAT, '%value% is not a float');
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
		if ( !is_float($inValue) ) {
			$valid = false;
			$this->addMessage($this->_formatMessage(self::MESSAGE_NOT_A_FLOAT, $inValue));
		}
		
		$inValue = strval(floatval($inValue));
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