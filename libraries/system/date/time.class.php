<?php
/**
 * systemDateTime
 * 
 * Stored in dateTime.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemDateTime
 * @version $Rev: 844 $
 */


/**
 * systemDateTime
 *
 * Scorpio System Date Time object; is an extension of the PHP DateTime object providing additional
 * functionality above what the internal object allows.
 * 
 * systemDateTime supports the Serializable interface allowing the datetime object to be easily
 * stored with both the date and the timezone which prevents errors when trying to restore the
 * datetime.
 * 
 * @link http://php.net/manual/en/function.date-create.php
 * @link http://laughingmeme.org/2007/02/27/looking-at-php5s-datetime-and-datetimezone/
 *
 * @package scorpio
 * @subpackage system
 * @category systemDateTime
 */
class systemDateTime extends DateTime implements Serializable {

	/**
	 * Stores a flag for if the object has been modified
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;



	/**
	 * Returns new DateTime object, if $inTimezone is not specified, defaults to system config timezone
	 * $inTimezone can be the string of the timezone; this will be checked against the valid strings
	 *
	 * @param string $inTime Any valid strtotime string
	 * @param string|DateTimeZone $inTimezone
	 * @return systemDateTime
	 */
	public function __construct($inTime = 'now', $inTimezone = null) {
		if ( $inTimezone !== null && !$inTimezone instanceof DateTimeZone && strlen($inTimezone) > 1 ) {
			$zones = systemDateTimeZone::listIdentifiers();
			foreach ( $zones as $timezone ) {
				if ( $inTimezone == $timezone ) {
					$inTimezone = new systemDateTimeZone($inTimezone);
					break;
				}
			}
		}
		if ( !$inTimezone instanceof DateTimeZone ) {
			$inTimezone = new systemDateTimeZone(system::getConfig()->getSystemTimeZone()->getParamValue());
		}
		
		parent::__construct($inTime, $inTimezone);

		$this->setModified(false);
	}
	
	/**
	 * Returns the current date/time object as a string in Y-m-d H:i:s format
	 *
	 * @return string
	 */
	function __toString() {
		return $this->format(system::getConfig()->getDatabaseDatetimeFormat());
	}
	
	/**
	 * Implementation for Serializable interface
	 * 
	 * Returns a serialized array containing date - the current datetime in standard
	 * CCYY-MM-DD HH:ii:ss format and timezone - the string name of the current timezone.
	 * 
	 * @return string
	 */
	public function serialize() {
		return serialize(
			array(
				'date' => $this->format('Y-m-d H:i:s'),
				'timezone' => $this->getTimezone()->getName()
			)
		);
	}

	/**
	 * Restores a serialized DateTime object
	 * 
	 * @param string $serialized
	 * @return systemDateTime
	 */
	public function unserialize($serialized) {
		$array = unserialize($serialized);
		if ( is_array($array) && count($array) == 2 ) {
			$this->__construct($array['date'], $array['timezone']);
		}
	}
	
	
	
	/**
	 * Returns an instance of systemDateTime object
	 *
	 * @param string $inTime
	 * @param DateTimeZone $inTimezone
	 * @return systemDateTime
	 * @static
	 */
	static function getInstance($inTime = 'now', $inTimezone = null) {
		$oObject = new systemDateTime($inTime, $inTimezone);
		return $oObject;
	}

	/**
	 * Convenience method that creates an object with timezone set to UTC
	 *
	 * @param string $inTime
	 * @return systemDateTime
	 * @static
	 */
	static function getInstanceUtc($inTime = 'now') {
		$oObject = new systemDateTime($inTime, 'UTC');
		return $oObject;
	}
	
	/**
	 * Returns new systemDateTime object from the UNIX timestamp
	 *
	 * @param integer $inTimestamp
	 * @param string|DateTimeZone $inTimezone
	 * @return systemDateTime
	 * @static
	 */
	static function getInstanceFromUnix($inTimestamp, $inTimezone = null) {
		$oObject = new systemDateTime(date('Y-m-d H:i:s', $inTimestamp), $inTimezone);
		return $oObject;
	}

	/**
	 * Returns the DateInterval in seconds
	 *
	 * Note: DateInterval has no concept of dates so a year is treated as 365 days, a
	 * month 30 days etc. This may give strange results.
	 *
	 * @param DateInterval $inDiff
	 * @return integer
	 * @static
	 */
	static function getDiffInSeconds(DateInterval $inDiff) {
		return
			($inDiff->s) +
			($inDiff->i*60) +
			($inDiff->h*60*60) +
			($inDiff->d*24*60*60) +
			($inDiff->m*30*24*60*60) +
			($inDiff->y*365*30*24*60*60);
	}


	
	/**
	 * Alter the timestamp by incrementing or decrementing in a format accepted by strtotime().
	 *
	 * @param string $inModify
	 * @return systemDateTime
	 * @link http://ca.php.net/manual/en/datetime.modify.php
	 */
	function modify($inModify) {
		parent::modify($inModify);
		$this->setModified();

		return $this;
	}
	/**
	 * Set the timezone to $inTimezone
	 *
	 * @param DateTimeZone $inTimezone
	 * @return systemDateTime
	 * @link http://www.php.net/manual/en/function.date-timezone-set.php
	 */
	function setTimezone($inTimezone) {
		if ( $inTimezone !== null && !$inTimezone instanceof DateTimeZone && strlen($inTimezone) > 1 ) {
			$zones = systemDateTimeZone::listIdentifiers();
			foreach ( $zones as $timezone ) {
				if ( $inTimezone == $timezone ) {
					$inTimezone = new systemDateTimeZone($inTimezone);
					break;
				}
			}
		}
		if ( !$inTimezone instanceof DateTimeZone ) {
			$inTimezone = new systemDateTimeZone(system::getConfig()->getSystemTimeZone());
		}
		
		parent::setTimezone($inTimezone);
		$this->setModified();

		return $this;
	}
	
	/**
	 * Set the date to that specified
	 *
	 * @param integer $inYear
	 * @param integer $inMonth
	 * @param integer $inDay
	 * @return systemDateTime
	 * @link http://www.php.net/manual/en/function.date-date-set.php
	 */
	function setDate($inYear = null, $inMonth = null, $inDay = null) {
		parent::setDate($inYear, $inMonth, $inDay);
		$this->setModified();

		return $this;
	}
	
	/**
	 * Set the time to that specified
	 *
	 * @param integer $inHours
	 * @param integer $inMinutes
	 * @param integer $inSeconds
	 * @return systemDateTime
	 * @link http://www.php.net/manual/en/function.date-time-set.php
	 */
	function setTime($inHours = null, $inMinutes = null, $inSeconds = null) {
		parent::setTime($inHours, $inMinutes, $inSeconds);
		$this->setModified();

		return $this;
	}

	/**
	 * Sets the date and time based on an Unix timestamp
	 *
	 * @param integer $inTimestamp
	 * @return systemDateTime
	 * @link http://ca.php.net/manual/en/datetime.settimestamp.php
	 */
	function setTimestamp($inTimestamp) {
		parent::setTimestamp($inTimestamp);
		$this->setModified();

		return $this;
	}
	
	/**
	 * Returns the current date/time formatted according to $inFormat, returns false on bad format
	 * 
	 * @param string $inFormat
	 * @return string
	 * @link http://www.php.net/manual/en/function.date.php
	 */
	function format($inFormat) {
		return parent::format($inFormat);
	}
	
	/**
	 * Clones the current object, returning it for use in computations
	 * 
	 * @return systemDateTime
	 */
	function cloneDateTime() {
		return clone $this;
	}
	
	

	/**
	 * Returns true if the object has been modified
	 *
	 * @return boolean
	 */
	function isModified() {
		return $this->_Modified;
	}

	/**
	 * Sets the objects modified status flag
	 *
	 * @param boolean $inStatus (Default true)
	 * @return systemDateTime
	 */
	function setModified($inStatus = true) {
		$this->_Modified = $inStatus;
		return $this;
	}

	/**
	 * Returns the date in CCYY-MM-DD format
	 *
	 * @return string
	 */
	function getDate() {
		return $this->format('Y-m-d');
	}
	
	/**
	 * Returns just the year, CCYY
	 *
	 * @return string
	 */
	function getYear() {
		return $this->format('Y');
	}
	
	/**
	 * Returns the month with leading zero
	 *
	 * @return string
	 */
	function getMonth() {
		return $this->format('m');
	}
	
	/**
	 * Returns the month as a string, if $inLong is true, returns long format
	 *
	 * @param boolean $inLong
	 * @return string
	 */
	function getMonthAsString($inLong = false) {
		return ($inLong) ? $this->format('F') : $this->format('M');
	}
	
	/**
	 * Returns the day with leading zero
	 *
	 * @return string
	 */
	function getDay() {
		return $this->format('d');
	}
	
	/**
	 * Returns the day as a string, if $inLong is true, returns long format
	 *
	 * @param boolean $inLong
	 * @return string
	 */
	function getDayAsString($inLong = false) {
		return ($inLong) ? $this->format('l') : $this->format('D');
	}
	
	/**
	 * Returns the time in 24-hour format
	 *
	 * @return string
	 */
	function getTime() {
		return $this->format('H:i:s');
	}
	
	/**
	 * Returns the hour in 24-hour format
	 *
	 * @return string
	 */
	function getHour() {
		return $this->format('H');
	}
	
	/**
	 * Returns the minute with leading zero
	 *
	 * @return string
	 */
	function getMinute() {
		return $this->format('i');
	}
	
	/**
	 * Returns the second with leading zero
	 *
	 * @return string
	 */
	function getSecond() {
		return $this->format('s');
	}
	
	/**
	 * Returns the week number
	 *
	 * @return integer
	 */
	function getWeekNumber() {
		return $this->format('W');
	}
	
	/**
	 * Returns the day of the year starting from 0 to 365
	 *
	 * @return integer
	 */
	function getDayOfYear() {
		return $this->format('z');
	}
	
	
	
	/**
	 * Returns current date/time in Unix format
	 *
	 * @return integer
	 */
	function toUnix() {
		return $this->format('U');
	}

	/**
	 * Returns the date as a string (calls into {@link systemDateTime::__toString()}
	 * 
	 * @return string
	 */
	function toString() {
		return $this->__toString();
	}
	
	/**
	 * Creates a clone of the current object, and modifies it by $inCalcString.
	 * $inCalcString is any valid strtotime string
	 *
	 * @param string $inCalcString
	 * @return systemDateTime
	 */
	function calc($inCalcString) {
		$end = $this->cloneDateTime();
		$end->modify($inCalcString);
		return $end;
	}
}