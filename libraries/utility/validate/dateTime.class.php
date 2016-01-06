<?php
/**
 * utilityValidateDateTime.class.php
 * 
 * utilityValidateDateTime
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateDateTime
 * @version $Rev: 706 $
 */


/**
 * utilityValidateDateTime
 * 
 * Validates that a variable is composed of a valid date and time.
 * This validator uses both {@link utilityValidateDate date} and
 * {@link utilityValidateTime time} validators. All options
 * supported by those validators can be used with dateTime.
 * 
 * <code>
 * // validate a 24 hour time, but default date
 * $oValidator = new utilityValidateDateTime(
 *     array(
 *         utilityValidateTime::TIME_FORMAT => '24',
 *         utilityValidateTime::TIME_SEPARATOR => ':',
 *     )
 * );
 * if ( !$oValidator->isValid($datetime) ) {
 *     print_r($oValidator->getMessages());
 * }
 * 
 * // validate time first, using defaults
 * $oValidator = new utilityValidateDateTime(
 *     array(
 *         utilityValidateDateTime::DATETIME_COMPONENT_ORDER
 *             => utilityValidateDateTime::DATETIME_COMPONENT_ORDER_TIME_FIRST
 *     )
 * );
 * if ( !$oValidator->isValid($datetime) ) {
 *     print_r($oValidator->getMessages());
 * }
 * </code>
 *
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateDateTime
 */
class utilityValidateDateTime extends utilityValidateAbstract {
	
	const DATETIME_SEPARATOR = 'dateTimeSeparator';
	const DATETIME_COMPONENT_ORDER = 'dateTimeOrder';
	
	const DATETIME_COMPONENT_ORDER_DATE_FIRST = 1;
	const DATETIME_COMPONENT_ORDER_TIME_FIRST = 2;
	
	const MESSAGE_NOT_A_DATETIME = 'notADateTime';
	const MESSAGE_MISSING_DATE = 'missingDate';
	const MESSAGE_MISSING_TIME = 'missingTime';
	
	/**
	 * @see utilityValidateAbstract::initialise()
	 */
	function initialise() {
		$this->addMessageTemplates(self::MESSAGE_NOT_A_DATETIME, 'Value %value% is not valid');
		$this->addMessageTemplates(self::MESSAGE_MISSING_DATE, 'Value %value% is missing a date component');
		$this->addMessageTemplates(self::MESSAGE_MISSING_TIME, 'Value %value% is missing a time component');
		
		if ( !$this->getOptions(self::DATETIME_SEPARATOR) ) {
			$this->setOptions(array(self::DATETIME_SEPARATOR => ' '));
		}
		if ( !$this->getOptions(self::DATETIME_COMPONENT_ORDER) ) {
			$this->setOptions(array(self::DATETIME_COMPONENT_ORDER => self::DATETIME_COMPONENT_ORDER_DATE_FIRST));
		}
		
		$this->addMessageVariable(self::DATETIME_SEPARATOR, $this->getOptions(self::DATETIME_SEPARATOR));
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
		$date = $time = false;
		
		$components = explode($this->getOptions(self::DATETIME_SEPARATOR), $inValue);
		if ( count($components) == 2 ) {
			if ( $this->getOptions(self::DATETIME_COMPONENT_ORDER) == self::DATETIME_COMPONENT_ORDER_TIME_FIRST ) {
				list($time, $date) = $components;
			} else {
				list($date, $time) = $components;
			}
		}
		
		if ( empty($date) ) {
			$valid = false;
			$this->addMessage($this->_formatMessage(self::MESSAGE_MISSING_DATE, $inValue));
		}
		if ( empty($time) ) {
			$valid = false;
			$this->addMessage($this->_formatMessage(self::MESSAGE_MISSING_TIME, $inValue));
		}
		
		if ( $valid ) {
			$oDate = new utilityValidateDate($this->getOptions(), $this->getTranslationManager());
			if ( !$oDate->isValid($date) ) {
				$valid = false;
				foreach ( $oDate->getMessages() as $message ) {
					$this->addMessage($message);
				}
			}
			unset($oDate);
			
			$oTime = new utilityValidateTime($this->getOptions(), $this->getTranslationManager());
			if ( !$oTime->isValid($time) ) {
				$valid = false;
				foreach ( $oTime->getMessages() as $message ) {
					$this->addMessage($message);
				}
			}
			unset($oTime);
		}
		
		if ( !$valid ) {
			$this->addMessage($this->_formatMessage(self::MESSAGE_NOT_A_DATETIME, $inValue));
		}
		
		return $valid;
	}
}