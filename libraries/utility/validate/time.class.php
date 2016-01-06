<?php
/**
 * utilityValidateTime.class.php
 * 
 * utilityValidateTime
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateTime
 * @version $Rev: 706 $
 */


/**
 * utilityValidateTime
 * 
 * Validates that a variable is a valid time in either 12 or 24 hour format.
 * If the time format is not specified, it defaults to 24 hours. Time is
 * always treated as HH:MM:SS with a customisable separator (default is :),
 * however seconds are optional.
 * 
 * The validator will convert DateTime objects to a string using the options
 * specified during validator instantiation. Under these circumstances, the
 * date/time should always be valid.
 * 
 * <code>
 * $oValidator = new utilityValidateTime(
 *     array(
 *         utilityValidateTime::TIME_FORMAT => '12',
 *         utilityValidateTime::TIME_SEPARATOR => ':',
 *     )
 * );
 * if ( !$oValidator->isValid($time) ) {
 *     print_r($oValidator->getMessages());
 * }
 * </code>
 *
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateTime
 */
class utilityValidateTime extends utilityValidateAbstract {
	
	const TIME_FORMAT = 'timeFormat';
	const TIME_FORMAT_12HOUR = '12';
	const TIME_FORMAT_24HOUR = '24';
	const TIME_SEPARATOR = 'timeSeparator';
	
	const MESSAGE_NOT_VALID_FORMAT = 'notValidTimeFormat';
	const MESSAGE_NOT_A_TIME = 'notATime';
	const MESSAGE_COMPONENT_HOUR_NOT_VALID = 'comHourNotValid';
	const MESSAGE_COMPONENT_MINUTE_NOT_VALID = 'comMinuteNotValid';
	const MESSAGE_COMPONENT_SECOND_NOT_VALID = 'comSecondNotValid';
	
	/**
	 * @see utilityValidateAbstract::initialise()
	 */
	function initialise() {
		$this->addMessageTemplates(self::MESSAGE_NOT_VALID_FORMAT, 'Specified time format %timeFormat% is not valid');
		$this->addMessageTemplates(self::MESSAGE_NOT_A_TIME, 'Value %value% is not valid for %timeFormat% hour format');
		$this->addMessageTemplates(self::MESSAGE_COMPONENT_HOUR_NOT_VALID, '%value% is not valid for the %timeFormat% hour format component');
		$this->addMessageTemplates(self::MESSAGE_COMPONENT_MINUTE_NOT_VALID, '%value% is not valid for the minute component');
		$this->addMessageTemplates(self::MESSAGE_COMPONENT_SECOND_NOT_VALID, '%value% is not valid for the second component');
		
		if ( !$this->getOptions(self::TIME_FORMAT) ) {
			$this->setOptions(array(self::TIME_FORMAT => self::TIME_FORMAT_24HOUR));
		}
		if ( !$this->getOptions(self::TIME_SEPARATOR) ) {
			$this->setOptions(array(self::TIME_SEPARATOR => ':'));
		}
		
		$this->addMessageVariable(self::TIME_FORMAT, $this->getOptions(self::TIME_FORMAT));
	}

	/**
	 * @see utilityValidateInterface::isValid()
	 *
	 * @param mixed $inValue
	 * @return boolean
	 */
	function isValid($inValue) {
		$this->setValue($inValue);

		if ( $inValue instanceof DateTime ) {
			$sep = $this->getOptions(self::TIME_SEPARATOR);
			if ( $this->getOptions(self::TIME_FORMAT_24HOUR) ) {
				$inValue = $inValue->format("H{$sep}i{$sep}s");
			} else {
				$inValue = $inValue->format("h{$sep}i{$sep}s");
			}
		}

		$valid = true;
		if ( !in_array($this->getOptions(self::TIME_FORMAT), array(self::TIME_FORMAT_12HOUR, self::TIME_FORMAT_24HOUR)) ) {
			$valid = false;
			$this->addMessage($this->_formatMessage(self::MESSAGE_NOT_VALID_FORMAT, $inValue));
		}
		if ( $valid ) {
			$hour = $minute = $second = false;
			$components = explode($this->getOptions(self::TIME_SEPARATOR), $inValue);
			switch ( count($components) ) {
				case 3: list($hour, $minute, $second)	= $components; break;
				case 2: list($hour, $minute)			= $components; break;
				default:
					$valid = false;
					$this->addMessage($this->_formatMessage(self::MESSAGE_NOT_A_TIME, $inValue));
			}
			
			if ( $second !== false && ($second < 0 || $second > 59) ) {
				$valid = false;
				$this->addMessage($this->_formatMessage(self::MESSAGE_COMPONENT_SECOND_NOT_VALID, $second));
			}
			if ( $minute !== false && ($minute < 0 || $minute > 59) ) {
				$valid = false;
				$this->addMessage($this->_formatMessage(self::MESSAGE_COMPONENT_MINUTE_NOT_VALID, $minute));
			}
			if ( $hour !== false ) {
				$hourInvalid = true;
				$format = $this->getOptions(self::TIME_FORMAT);
				if ( $format == self::TIME_FORMAT_12HOUR && ($hour < 0 || $hour > 12) ) {
					$hourInvalid = false;
				}
				if ( $format == self::TIME_FORMAT_24HOUR && ($hour < 0 || $hour > 23) ) {
					$hourInvalid = false;
				}
				if ( !$hourInvalid ) {
					$valid = false;
					$this->addMessage($this->_formatMessage(self::MESSAGE_COMPONENT_HOUR_NOT_VALID, $hour));
				}
			}
		}
		
		return $valid;
	}
}