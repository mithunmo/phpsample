<?php
/**
 * systemDateTimeZone
 * 
 * Stored in dateTimeZone.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemDateTimeZone
 * @version $Rev: 722 $
 */


/**
 * systemDateTimeZone
 *
 * Scorpio System Date Time Zone object; is an extension of the PHP DateTimeZone object providing additional
 * functionality above what the internal object allows
 * 
 * @link http://ca.php.net/manual/en/function.timezone-open.php
 * @link http://laughingmeme.org/2007/02/27/looking-at-php5s-datetime-and-datetimezone/
 *
 * @package scorpio
 * @subpackage system
 * @category systemDateTimeZone
 */
class systemDateTimeZone extends DateTimeZone {

	/**
	 * Returns the current timezone name
	 *
	 * @return string
	 */
	function __toString() {
		return $this->getName();
	}
	
	/**
	 * Returns an instance of systemDateTimeZone object
	 *
	 * @param string $inTimezone
	 * @return systemDateTimeZone
	 * @static
	 */
	static function getInstance($inTimezone = null) {
		if ( $inTimezone === null ) {
			$inTimezone = system::getConfig()->getSystemTimeZone();
		}
		$oObject = new systemDateTimeZone($inTimezone);
		return $oObject;
	}

	/**
	 * Convenience method that creates an object with UTC
	 *
	 * @param string $inTime
	 * @return systemDateTimeZone
	 * @static
	 */
	static function getInstanceUtc() {
		$oTimezone = new systemDateTimeZone('UTC');
		return $oTimezone;
	}
}