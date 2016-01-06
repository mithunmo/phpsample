<?php
/**
 * utilityMemUsage Class
 * 
 * Stored in memUsage.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityMemUsage
 * @version $Rev: 706 $
 */


/**
 * utilityMemUsage Class
 * 
 * Provides a consistent way to reliably get the current processes memory
 * usage. Useful when the other methods are not returning what you expect.
 * 
 * This class uses code and examples featured on the PHP.net documentation
 * website.
 * 
 * <code>
 * // get the current memory usage
 * $mem = utilityMemUsage::getUsage();
 * 
 * // record usage and show diff
 * $oMemUsage = new utilityMemUsage();
 * $oMemUsage->takeReading();
 * // do something complicated
 * $diff = $oMemUsage->takeReading();
 * </code>
 * 
 * @package scorpio
 * @subpackage utility
 * @category utilityMemUsage
 */
class utilityMemUsage {
	
	/**
	 * Stores $_LastValue
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_LastValue;
	
	
	
	/**
	 * Returns a new utilityMemUsage object
	 *
	 * @return utilityMemUsage
	 */
	function __construct() {
		$this->clearValue();
	}

	
	
	/**
	 * Returns the current memory usage of the running script in bytes
	 *
	 * @return integer
	 * @static
	 * @link http://us2.php.net/manual/en/function.memory-get-usage.php
	 * @author e dot a dot schultz at gmail dot com
	 */
	static function getUsage() {
		// DR: this seems to be working quite well now, in PHP <5.2.X mem usage was tricky to get
		if ( function_exists('memory_get_peak_usage') && version_compare(PHP_VERSION, '5.2.1', '>') ) {
			return memory_get_peak_usage(true);
		}
		
		//If its Windows
		//Tested on Win XP Pro SP2. Should work on Win 2003 Server too
		//Doesn't work for 2000
		//If you need it to work for 2000 look at http://us2.php.net/manual/en/function.memory-get-usage.php#54642
		if ( substr(PHP_OS, 0, 3) == 'WIN' ) {
			$output = array();
			exec('tasklist /FI "PID eq ' . getmypid() . '" /FO LIST', $output);
			return preg_replace('/[\D]/', '', $output[5]) * 1024;
		} else {
			//We now assume the OS is UNIX
			//Tested on Mac OS X 10.4.6 and Linux Red Hat Enterprise 4
			//This should work on most UNIX systems
			$pid = getmypid();
			exec("ps -eo%mem,rss,pid | grep $pid", $output);
			
			$output[0] = trim(str_replace("  ", " ", $output[0]));
			$output = explode(" ", $output[0]);
			return ((int) ($output[1]) * 1024);
		}
	}
	
	
	
	/**
	 * Takes a new reading, clearing the old, called on instantiation
	 *
	 * @return utilityMemUsage
	 */
	function clearValue() {
		$this->_LastValue = self::getUsage();
		return $this;
	}
	
	/**
	 * Takes and stores the current mem usage, returning the difference since the last measurement
	 *
	 * Use this method during script execution to track memory leakage between loops.
	 * 
	 * @return integer
	 */
	function takeReading() {
		$newMem = (int) self::getUsage();
		$result = $newMem - $this->_LastValue;
		$this->_LastValue = $newMem;
		return $result;
	}
	
	/**
	 * Returns the last taken reading
	 *
	 * @return integer
	 */
	function getLastValue() {
		return $this->_LastValue;
	}
	
	/**
	 * Sets the last value
	 *
	 * @param integer $inValue
	 * @return utilityMemUsage
	 */
	function setLastValue($inValue) {
		if ( $inValue !== $this->_LastValue ) {
			$this->_LastValue = $inValue;
		}
		return $this;
	}
}