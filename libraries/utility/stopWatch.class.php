<?php
/**
 * utilityStopWatch Class
 * 
 * Stored in stopWatch.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityStopWatch
 * @version $Rev: 650 $
 */


/**
 * utilityStopWatch Class
 * 
 * A class for recording the time an event took place during a script
 * execution. Useful for profiling or benchmarking long running scripts
 * or to simply time the execution time between two points.
 * 
 * Supports "split" times, where a split can be marked with a marker name
 * for later identification. Split times are always recorded as the length
 * of time elapsed since the LAST split. If no previous split has been
 * recorded, the start time is used.
 * 
 * By default all microtimes are rounded to 5 decimal places. To change
 * this, set the precision before starting the timer.
 * 
 * The stop watch currently has 3 status points:
 * STOPPED - the stop watch is not timing
 * RUNNING - the stop watch has been started
 * SPLIT   - the stop watch is running and split times are being recorded
 * 
 * The status is set to split whenever a split is recorded. This will
 * affect the return state of {@link utilityStopWatch::elapsed()} where
 * the return will be the time elapsed since the last split time.
 * 
 * utilityStopWatch implements the __toString method allowing the timer to
 * be printed by using it as a string. If the current instance of
 * utilityStopWatch is set to running, __toString will only report "running"
 * and not a breakdown. If you require a breakdown stop() the clock first.
 * 
 * Example:
 * <code>
 * $oTimer = new utilityStopWatch();
 * $oTimer->setPrecision(3);
 * $oTimer->start();
 * // do something
 * $oTimer->stop();
 * echo $oTimer, "\n"; // output string of results
 * </code>
 * 
 * Example using split:
 * <code>
 * $oTimer = new utilityStopWatch();
 * $oTimer->start();
 * $oTimer->split('setup-start');
 * // do something to set up the script
 * $oTimer->split('setup-done');
 * while ( $someThing ) {
 *    // do something complicated and record when finished
 *    $oTimer->split('iteration');
 * }
 * $oTimer->stop();
 * // echo results
 * echo $oTimer->getResultsAsString(), "\n"; // output string of results
 * </code>
 * 
 * @package scorpio
 * @subpackage utility
 * @category utilityStopWatch
 */
class utilityStopWatch {
	
	/**
	 * Stores $_Status
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_Status;
	
	const STAT_STOPPED = 1;
	const STAT_RUNNING = 2;
	const STAT_SPLIT = 4;
	
	/**
	 * Stores $_StartTime
	 *
	 * @var float
	 * @access protected
	 */
	protected $_StartTime;
	
	/**
	 * Stores $_EndTime
	 *
	 * @var float
	 * @access protected
	 */
	protected $_EndTime;
	
	/**
	 * Stores $_SplitTimes
	 *
	 * @var array
	 * @access protected
	 */
	protected $_SplitTimes;
	
	/**
	 * Stores $_LastSplitTime
	 *
	 * @var float
	 * @access protected
	 */
	protected $_LastSplitTime;
	
	/**
	 * Stores $_Precision
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_Precision;
	
	
	
	/**
	 * Creates a new stop watch instance
	 *
	 * @return utilityStopWatch
	 */
	function __construct() {
		$this->reset();
	}
	
	/**
	 * If stop watch is used as a string, output results
	 *
	 * @return string
	 */
	function __toString() {
		return $this->getResultsAsString();
	}
	
	/**
	 * Resets the object to defaults
	 *
	 * @return void
	 */
	function reset() {
		$this->_StartTime = $this->_EndTime = $this->_getTime();
		$this->_SplitTimes = array();
		$this->_Status = self::STAT_STOPPED;
		$this->_LastSplitTime = 0;
		$this->_Precision = 5;
	}
	
	/**
	 * Starts the stop watch
	 *
	 * @return utilityStopWatch
	 */
	function start() {
		$this->_setStartTime($this->_getTime());
		$this->_setStatus(self::STAT_RUNNING);
		return $this;
	}
	
	/**
	 * Creates a "split" point, with optional reference $inMarker
	 *
	 * @param string $inMarker
	 * @return utilityStopWatch
	 */
	function split($inMarker = null) {
		if ( !$inMarker ) {
			$inMarker = 'split';
		}
		$this->_markSplitTime($inMarker);
		if ( $this->getStatus() !== self::STAT_SPLIT ) {
			$this->_setStatus(self::STAT_SPLIT);
		}
		return $this;
	}

	/**
	 * Stops the stop watch
	 *
	 * @return utilityStopWatch
	 */
	function stop() {
		$this->_setEndTime($this->_getTime());
		$this->_setStatus(self::STAT_STOPPED);
		return $this;
	}
	
	/**
	 * Returns the total time elapsed depending on current status
	 * 
	 * STOPPED:  Returns total time elapsed
	 * SPLIT:    Returns the time elapsed since last split
	 * RUNNING:  Returns the current running time since stop watch started
	 *
	 * @return float
	 */
	function elapsed() {
		switch ( $this->getStatus() ) {
			case self::STAT_STOPPED:
				return round($this->getEndTime() - $this->getStartTime(), $this->getPrecision());
			break;
			
			case self::STAT_SPLIT:
				return round($this->getLastSplitTime() - $this->getStartTime(), $this->getPrecision());
			break;
			
			default:
				return round($this->_getTime() - $this->getStartTime(), $this->getPrecision());
			break;
		}
	}
	
	/**
	 * Returns the stop watch results as a formatted string
	 *
	 * @param string $inLineBreak Character to break lines with (default UNIX new line)
	 * @return string
	 */
	function getResultsAsString($inLineBreak = "\n") {
		if ( $this->getStatus() == self::STAT_STOPPED ) {
			$return = array();
			$return[] = '[Start] Stopwatch started: '.$this->getStartTime();
			if ( count($this->_SplitTimes) > 0 ) {
				foreach ( $this->_SplitTimes as $marker => $time ) {
					$return[] = '[Split]['.$marker.'] @ '.$time.' seconds';
				}
			}
			$return[] = '[Stop] Stopwatch ended: '.$this->getEndTime();
			$return[] = 'Total execution time: '.$this->elapsed().' seconds';
			return implode($inLineBreak, $return);
		} else {
			return 'StopWatch is running';
		}
	}
	
	/**
	 * Internal function to return formatted microtime with a certain precision
	 *
	 * @return float
	 */
	protected function _getTime() {
		return round(microtime(true), $this->getPrecision());
	}
	
	
	
	/**
	 * Returns $_Status
	 *
	 * @return integer
	 * @access public
	 */
	function getStatus() {
		return $this->_Status;
	}
	
	/**
	 * Set $_Status to $inStatus
	 *
	 * @param integer $inStatus
	 * @return utilityStopWatch
	 * @access protected
	 * @internal  
	 */
	protected function _setStatus($inStatus) {
		if ( $this->_Status !== $inStatus ) {
			$this->_Status = $inStatus;
		}
		return $this;
	}
	
	/**
	 * Returns $_StartTime
	 *
	 * @return float
	 * @access public
	 */
	function getStartTime() {
		return $this->_StartTime;
	}
	
	/**
	 * Set $_StartTime to $inStartTime
	 *
	 * @param float $inStartTime
	 * @return utilityStopWatch
	 * @access protected
	 * @internal 
	 */
	protected function _setStartTime($inStartTime) {
		if ( $this->_StartTime !== $inStartTime ) {
			$this->_StartTime = $inStartTime;
		}
		return $this;
	}

	/**
	 * Returns $_EndTime
	 *
	 * @return float
	 * @access public
	 */
	function getEndTime() {
		return $this->_EndTime;
	}
	
	/**
	 * Set $_EndTime to $inEndTime
	 *
	 * @param float $inEndTime
	 * @return utilityStopWatch
	 * @access protected
	 * @internal 
	 */
	protected function _setEndTime($inEndTime) {
		if ( $this->_EndTime !== $inEndTime ) {
			$this->_EndTime = $inEndTime;
		}
		return $this;
	}

	/**
	 * Returns $_SplitTimes
	 *
	 * @return array
	 * @access public
	 */
	function getSplitTimes() {
		return $this->_SplitTimes;
	}
	
	/**
	 * Record a split time against the event $inMarker, use public method {@link utilityStopWatch::split}
	 *
	 * @param string $inMarker
	 * @return utilityStopWatch
	 * @internal 
	 * @access protected
	 */
	protected function _markSplitTime($inMarker = null) {
		if ( $this->getStatus() !== self::STAT_STOPPED ) {
			$time = $this->_getTime();
			$split = round($time-$this->getLastSplitTime(), $this->getPrecision());
			$this->_LastSplitTime = $time;
			$this->_SplitTimes[$this->_getUniqueMarker($inMarker)] = $split;
		}
		return $this;
	}
	
	/**
	 * Returns the last split time entered on the split stack
	 *
	 * @return float
	 */
	function getLastSplitTime() {
		if ( $this->_LastSplitTime == 0 ) {
			return $this->getStartTime();
		}
		return $this->_LastSplitTime;
	}
	
	/**
	 * Returns a unique reference for $inMarker
	 *
	 * @param string $inMarker
	 * @return string
	 * @internal 
	 * @access protected
	 */
	protected function _getUniqueMarker($inMarker) {
		$i = 1;
		$mark = $inMarker;
		while ( isset($this->_SplitTimes[$mark]) ) {
			$mark = $inMarker.$i;
			++$i;
		}
		return $mark;
	}
	
	/**
	 * Returns all split times matching $inSplit
	 *
	 * @param string $inSplit
	 * @return array
	 */
	function getSplitsMatching($inSplit) {
		$results = array();
		if ( count($this->_SplitTimes) > 0 ) {
			foreach ( $this->_SplitTimes as $marker => $time ) {
				if ( strpos($marker, $inSplit) !== false ) {
					$results[$marker] = $time;
				}
			}
		}
		return $results;
	}

	/**
	 * Returns $_Precision
	 *
	 * @return integer
	 */
	function getPrecision() {
		return $this->_Precision;
	}
	
	/**
	 * Set $_Precision to $inPrecision
	 *
	 * @param integer $inPrecision
	 * @return utilityStopWatch
	 */
	function setPrecision($inPrecision) {
		if ( $inPrecision !== $this->_Precision ) {
			$this->_Precision = $inPrecision;
		}
		return $this;
	}
}