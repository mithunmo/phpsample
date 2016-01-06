<?php
/**
 * utilityValidateDate.class.php
 * 
 * utilityValidateDate
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateDate
 * @version $Rev: 708 $
 */


/**
 * utilityValidateDate
 * 
 * Validates that a variable matches a date pattern and that it is
 * a valid date. The pattern is a regular expression. This validator
 * only handles the date component of a date/time. See also the
 * {@link utilityValidateTime time} and {@link utilityValidateDateTime dateTime}
 * validators. If pattern is not specified it defaults to CCYY-MM-DD.
 * 
 * To validate that a date is an actual date in the Gregorian calendar,
 * set the dateComponentOrder option. This should reflect the order that
 * the regex is in using Y - year, M - month, D - day e.g. Y/M/D. The
 * separator must also be specified.
 * 
 * Please note: if using the dateComponentOrder your must set the PATTERN
 * to reflect your order, otherwise the validator will default to CCYY-MM-DD
 * validation which will not match your order. Similarly you must specify
 * the separator required to split the date.
 * 
 * <code>
 * // validate a date using defaults
 * $oValidator = new utilityValidateDate(
 *     array(
 *         // don't specify anything
 *     )
 * );
 * if ( !$oValidator->isValid($date) ) {
 *     print_r($oValidator->getMessages());
 * }
 * 
 * // validate a date as M/D/Y, remember to set the PATTERN
 * $oValidator = new utilityValidateDate(
 *     array(
 *         utilityValidateDate::PATTERN => '/^\d{2}\/\d{2}\/\d{4}$/',
 *         utilityValidateDate::DATE_COMPONENT_ORDER => 'M/D/Y',
 *         utilityValidateDate::DATE_SEPARATOR => '/',
 *     )
 * );
 * if ( !$oValidator->isValid($date) ) {
 *     print_r($oValidator->getMessages());
 * }
 * </code>
 *
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateDate
 */
class utilityValidateDate extends utilityValidateAbstract {
	
	const PATTERN = 'datePattern';
	
	const DATE_COMPONENT_ORDER = 'dateComponentOrder';
	const DATE_COMPONENT_YEAR = 'Y';
	const DATE_COMPONENT_MONTH = 'M';
	const DATE_COMPONENT_DAY = 'D';
	
	const DATE_SEPARATOR = 'dateSeparator';
	
	const MESSAGE_NOT_VALID_FORMAT = 'notValidDateFormat';
	const MESSAGE_NOT_VALID_FORMAT_FOR_ORDER = 'notValidDateFormatForOrder';
	const MESSAGE_NOT_A_DATE = 'notADate';
	const MESSAGE_COMPONENT_ORDER_NOT_MATCH_DATE = 'comOrderNotMatchDate';
	
	/**
	 * @see utilityValidateAbstract::initialise()
	 */
	function initialise() {
		$this->addMessageTemplates(self::MESSAGE_NOT_VALID_FORMAT, 'Value %value% does not match %datePattern%');
		$this->addMessageTemplates(self::MESSAGE_NOT_VALID_FORMAT_FOR_ORDER, 'Value %value% is not a valid date in %dateComponentOrder% order');
		$this->addMessageTemplates(self::MESSAGE_NOT_A_DATE, 'Value %value% is not a valid date');
		$this->addMessageTemplates(self::MESSAGE_COMPONENT_ORDER_NOT_MATCH_DATE, 'Component order %dateComponentOrder% does not match date');
		
		if ( !$this->getOptions(self::PATTERN) ) {
			$this->setOptions(array(self::PATTERN => '/^\d{4}-\d{2}-\d{2}$/'));
		}
		
		$this->addMessageVariable(self::PATTERN, $this->getOptions(self::PATTERN));
		$this->addMessageVariable(self::DATE_COMPONENT_ORDER, $this->getOptions(self::DATE_COMPONENT_ORDER));
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
		$year = $month = $day = false;

		if ( $inValue instanceof DateTime ) {
			if ( $this->getOptions(self::DATE_COMPONENT_ORDER) ) {
				$format = strtr(
					strtolower($this->getOptions(self::DATE_COMPONENT_ORDER)),
					strtolower(self::DATE_COMPONENT_YEAR),
					self::DATE_COMPONENT_YEAR
				);

				$inValue = $inValue->format($format);
			} else {
				$inValue = $inValue->format('Y-m-d');
			}
		}
		
		if ( !preg_match($this->getOptions(self::PATTERN), $inValue) ) {
			$valid = false;
			$this->addMessage($this->_formatMessage(self::MESSAGE_NOT_VALID_FORMAT, $inValue));
		}
		if ( $valid && $this->getOptions(self::PATTERN) == '/^\d{4}-\d{2}-\d{2}$/' ) {
			list($year, $month, $day) = sscanf($inValue, '%d-%d-%d');
		}
		
		/*
		 * If an order has been set, use it to get the year, month and day
		 */
		if ( $this->getOptions(self::DATE_COMPONENT_ORDER) ) {
			if ( !$this->getOptions(self::DATE_SEPARATOR) ) {
				throw new utilityValidateOptionException(__CLASS__, self::DATE_SEPARATOR);
			}
			
			$order = explode($this->getOptions(self::DATE_SEPARATOR), $this->getOptions(self::DATE_COMPONENT_ORDER));
			$components = explode($this->getOptions(self::DATE_SEPARATOR), $inValue);
			
			if ( count($order) == count($components) ) {
				for ( $i=0; $i<count($order); $i++ ) {
					$type = $order[$i];
					switch ( $type ) {
						case self::DATE_COMPONENT_YEAR:  $year = $components[$i];  break;
						case self::DATE_COMPONENT_MONTH: $month = $components[$i]; break;
						case self::DATE_COMPONENT_DAY:   $day = $components[$i];   break;
					}
				}
			} else {
				$valid = false;
				$this->addMessage($this->_formatMessage(self::MESSAGE_COMPONENT_ORDER_NOT_MATCH_DATE, $inValue));
			}
		}
		
		/*
		 * Validate that date is a valid date
		 */
		if ( $valid && $year && $month && $day ) {
			if ( !checkdate(intval($month), intval($day), intval($year)) ) {
				$valid = false;
				if ( $this->getOptions(self::DATE_COMPONENT_ORDER) ) {
					$this->addMessage($this->_formatMessage(self::MESSAGE_NOT_VALID_FORMAT_FOR_ORDER, $inValue));
				} else {
					$this->addMessage($this->_formatMessage(self::MESSAGE_NOT_A_DATE, $inValue));
				}
			}
		}
		
		return $valid;
	}
}