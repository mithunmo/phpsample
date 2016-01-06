<?php
/**
 * systemDateCalendar
 *
 * Stored in calendar.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemDateCalendar
 * @version $Rev: 650 $
 */


/**
 * systemDateCalendar
 *
 * Class for building a calendar with optional events. It creates an internal
 * array of items to be used by the view layer rather than trying to create
 * HTML.
 *
 * This is based on work by David Walsh: http://davidwalsh.name/php-calendar.
 *
 * The calendar data is stored in {@link systemDateCalendar::$_CalendarData}
 * and once built forms a 42 element array (a 7x6 grid), formatted for the
 * week starting on Sunday (PHPs {@link http://www.php.net/date date} function
 * numbers the days of the week starting at 0 for Sunday).
 *
 * Events can be added to a date in the calendar period. Events are stored as
 * {@link systemDateEvent} objects.
 *
 * The calendar can be iterated in the view layer to build a calendar. Empty
 * array data marks an empty day. The day data is an associative array
 * containing: number, day and events where number is the day number, day the
 * long format day name and events an array of events on that day.
 *
 * @package scorpio
 * @subpackage system
 * @category systemDateCalendar
 */
class systemDateCalendar {

	/**
	 * Stores $_Modified
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified;

	/**
	 * Stores $_Year
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_Year;

	/**
	 * Stores $_Month
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_Month;

	/**
	 * Stores the calendar data
	 *
	 * @var array
	 * @access protected
	 */
	protected $_CalendarData;

	/**
	 * Stores an array of calendar events
	 *
	 * @var array
	 * @access protected
	 */
	protected $_Events;



	/**
	 * Creates a new calendar instance
	 *
	 * @param integer $inYear
	 * @param integer $inMonth
	 */
	function __construct($inYear = null, $inMonth = null) {
		$this->reset();
		if ( $inYear === null ) {
			$inYear = date('Y');
		}
		if ( $inMonth === null ) {
			$inMonth = date('m');
		}

		$this->setYear($inYear);
		$this->setMonth($inMonth);
	}

	/**
	 * Resets the object
	 *
	 * @return void
	 */
	function reset() {
		$this->_Year = date('Y');
		$this->_Month = date('m');
		$this->_CalendarData = array();
		$this->_Events = array();
		$this->_Modified = false;
	}



	/**
	 * Returns true if object modified
	 *
	 * @return boolean
	 */
	function isModified() {
		return $this->_Modified;
	}

	/**
	 * Sets $_Modified to $inStatus
	 *
	 * @param boolean $inStatus
	 * @return systemDateCalendar
	 */
	function setModified($inStatus = true) {
		$this->_Modified = $inStatus;
		return $this;
	}



	/**
	 * Returns the value of $_Year
	 *
	 * @return integer
	 */
	function getYear() {
		return $this->_Year;
	}

	/**
	 * Set value of $_Year
	 *
	 * @param integer $inYear
	 * @return integer
	 */
	function setYear($inYear) {
		if ( $inYear !== $this->_Year ) {
			$this->_Year = $inYear;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the value of $_Month
	 *
	 * @return integer
	 */
	function getMonth() {
		return $this->_Month;
	}

	/**
	 * Returns the month name in long format
	 *
	 * @return string
	 */
	function getLongMonth() {
		return date('F', strtotime("{$this->getYear()}-{$this->getMonth()}-01"));
	}

	/**
	 * Set value of $_Month
	 *
	 * @param integer $inMonth
	 * @return systemDateCalendar
	 */
	function setMonth($inMonth) {
		if ( $inMonth !== $this->_Month ) {
			$this->_Month = $inMonth;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns an array containing the year and month of the previous month
	 *
	 * @return array(year, month, short, long)
	 */
	function getPreviousMonth() {
		$now = strtotime("{$this->getYear()}-{$this->getMonth()}-01");
		return array(
			'year' => date('Y', strtotime('-1 month', $now)),
			'month' => date('m', strtotime('-1 month', $now)),
			'short' => date('M', strtotime('-1 month', $now)),
			'long' => date('F', strtotime('-1 month', $now)),
		);
	}

	/**
	 * Returns an array containing the year and month number of the next month
	 *
	 * @return array(year, month, short, long)
	 */
	function getNextMonth() {
		$now = strtotime("{$this->getYear()}-{$this->getMonth()}-01");
		return array(
			'year' => date('Y', strtotime('+1 month', $now)),
			'month' => date('m', strtotime('+1 month', $now)),
			'short' => date('M', strtotime('+1 month', $now)),
			'long' => date('F', strtotime('+1 month', $now)),
		);
	}


	
	/**
	 * Returns the calendar data, building it if not yet
	 * 
	 * @return array
	 */
	function getCalendar() {
		if ( count($this->_CalendarData) == 0 ) {
			$this->buildCalendar();
		}
		return $this->_CalendarData;
	}

	/**
	 * Builds the calendar data
	 *
	 * @return systemDateCalendar
	 */
	function buildCalendar() {
		$year = (integer) $this->getYear();
		$month = (integer) $this->getMonth();

		/* days and weeks vars now ... */
		$running_day = date('w', mktime(0, 0, 0, $month, 1, $year));
		$days_in_month = date('t', mktime(0, 0, 0, $month, 1, $year));
		$days_in_this_week = 1;
		$day_counter = 0;
		$dates_array = array();

		/* print "blank" days until the first of the current week */
		for ($x = 0; $x < $running_day; $x++) {
			$this->_CalendarData[] = array();
			$days_in_this_week++;
		}

		/* keep going with days.... */
		for ($list_day = 1; $list_day <= $days_in_month; $list_day++) {
			/* add in the day number */
			$this->_CalendarData[] = array(
				'number' => $list_day,
				'day' => date('l', strtotime("$year-$month-$list_day")),
				'events' => $this->getEvents($list_day)
			);

			if ( $running_day == 6 ) {
				$running_day = -1;
				$days_in_this_week = 0;
			}
			$days_in_this_week++; $running_day++; $day_counter++;
		}

		/* finish the rest of the days in the week */
		if ( $days_in_this_week < 8 ) {
			for ($x = 1; $x <= (8 - $days_in_this_week); $x++) {
				$this->_CalendarData[] = array();
			}
		}
		if ( count($this->_CalendarData) < 42 ) {
			for ( $i=count($this->_CalendarData); $i<42; $i++ ) {
				$this->_CalendarData[] = array();
			}
		}

		return $this;
	}

	

	/**
	 * Adds an event to the specified day to be used within this calendar
	 *
	 * @param integer $inDay
	 * @param systemDateEvent $inEvent
	 * @return systemDateCalendar
	 */
	function addEvent($inDay, systemDateEvent $inEvent) {
		if ( !isset($this->_Events[$inDay]) ) {
			$this->_Events[$inDay] = array();
		}
		if ( !isset($this->_Events[$inDay][$inEvent->getEventID()]) ) {
			$this->_Events[$inDay][$inEvent->getEventID()] = $inEvent;
		}
		return $this;
	}

	/**
	 * Remove event from calendar
	 *
	 * @param integer $inDay
	 * @param systemDateEvent $inEvent
	 * @return systemDateCalendar
	 */
	function removeEvent($inDay, systemDateEvent $inEvent) {
		if ( isset($this->_Events[$inDay][$inEvent->getEventID()]) ) {
			$this->_Events[$inDay][$inEvent->getEventID()] = null;
			unset($this->_Events[$inDay][$inEvent->getEventID()]);
		}
		return $this;
	}

	/**
	 * Returns events for the specified day, or all events
	 *
	 * @param integer $inDay
	 * @return array
	 */
	function getEvents($inDay = null) {
		if ( $inDay === null ) {
			return $this->_Events;
		}
		if ( isset($this->_Events[$inDay]) ) {
			return $this->_Events[$inDay];
		} else {
			return array();
		}
	}

	/**
	 * Returns the total number of events in the calendar
	 *
	 * @return integer
	 */
	function getEventCount() {
		$cnt = 0;
		if ( count($this->_Events) > 0 ) {
			foreach ( $this->_Events as $day => $events ) {
				$cnt += count($events);
			}
		}
		return $cnt;
	}

	/**
	 * Returns true if there are any events in the calendar
	 *
	 * @return boolean
	 */
	function doesCalendarHaveEvents() {
		return (count($this->_Events) > 0);
	}

	/**
	 * Returns true if there are events set of the specified day
	 *
	 * @param integer $inDay
	 * @return boolean
	 */
	function doesDayHaveEvents($inDay) {
		if ( isset($this->_Events[$inDay]) && count($this->_Events[$inDay]) > 0 ) {
			return true;
		} else {
			return false;
		}
	}
}