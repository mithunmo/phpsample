<?php
/**
 * utilityValidateBoolean.class.php
 * 
 * utilityValidateBoolean
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateBoolean
 * @version $Rev: 706 $
 */


/**
 * utilityValidateBoolean
 * 
 * Validates that a variable is a boolean, optionally can be set to
 * validate only strict boolean values (false|true). Default is to
 * compare against a list of representations of boolean values.
 * 
 * <code>
 * // just use defaults so the string: "no" is considered boolean (false)
 * $oValidator = new utilityValidateBoolean(
 *     array(
 *         // use defaults
 * );
 * if ( $oValidator->isValid('No') ) {
 *     // is boolean
 * }
 * 
 * // validate that var is a strict boolean
 * $oValidator = new utilityValidateBoolean(
 *     array(
 *         utilityValidateBoolean::STRICT_CHECK => true,
 *     )
 * );
 * if ( !$oValidator->isValid($bool) ) {
 *     print_r($oValidator->getMessages());
 * }
 * </code>
 *
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateBoolean
 */
class utilityValidateBoolean extends utilityValidateAbstract {
	
	const TRUE_VALUES = 'trueValues';
	const FALSE_VALUES = 'falseValues';
	
	const MESSAGE_NOT_A_BOOLEAN = 'notABoolean';
	const MESSAGE_NOT_REC_BOOLEAN = 'notARecBoolean';
	
	/**
	 * @see utilityValidateAbstract::initialise()
	 */
	function initialise() {
		$this->addMessageTemplates(self::MESSAGE_NOT_A_BOOLEAN, 'Value is not a logical boolean');
		$this->addMessageTemplates(self::MESSAGE_NOT_REC_BOOLEAN, 'Value "%value%" is not a recognised boolean');
		
		if ( !$this->getOptions(self::TRUE_VALUES) ) {
			$this->setOptions(array(self::TRUE_VALUES => array('y', 'yes', 'true', 't', 'on', '1')));
		}
		if ( !$this->getOptions(self::FALSE_VALUES) ) {
			$this->setOptions(array(self::FALSE_VALUES => array('n', 'no', 'false', 'f', 'off', '0')));
		}
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
		if ( $this->getOptions(self::STRICT_CHECK) ) {
			if ( $inValue !== true && $inValue !== false ) { 
				$valid = false;
				$this->addMessage($this->_formatMessage(self::MESSAGE_NOT_A_BOOLEAN, $inValue));
			}
		} else {
			$inValue = (string) $inValue;
			if (
				!in_array($inValue, $this->getOptions(self::TRUE_VALUES))
				&&
				!in_array($inValue, $this->getOptions(self::FALSE_VALUES))
			) {
				$valid = false;
				$this->addMessage($this->_formatMessage(self::MESSAGE_NOT_REC_BOOLEAN, $inValue));
			}
		}
		
		return $valid;
	}
}