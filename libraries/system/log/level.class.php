<?php
/**
 * systemLogLevel class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemLogLevel
 * @version $Rev: 650 $
 */


/**
 * systemLogLevel Class
 * 
 * Holds logger error levels and methods to convert the numbers to strings.
 * 
 * @package scorpio
 * @subpackage system
 * @category systemLogLevel
 */
class systemLogLevel {
	
	/**
	 * Use to always log this error
	 *
	 * @var integer
	 */
	const ALWAYS					= 1;
	/**
	 * Use to log a critical error
	 *
	 * @var integer
	 */
	const CRITICAL					= 2;
	/**
	 * Use to log a severe error (usually unrecoverable)
	 *
	 * @var integer
	 */
	const ERROR						= 4;
	/**
	 * Use to log a warning (often recoverable)
	 *
	 * @var integer
	 */
	const WARNING					= 8;
	/**
	 * Use to log a notice (minor error always recoverable), this is the default log level
	 *
	 * @var integer
	 */
	const NOTICE					= 16;
	/**
	 * Use to log only for informational purposes (ignored by default)
	 *
	 * @var integer
	 */
	const INFO						= 32;
	/**
	 * Use to log debug info or very verbose info (only used for development)
	 *
	 * @var integer
	 */
	const DEBUG						= 64;
	
	/**
	 * Used to log successful audit actions
	 *
	 * @var integer
	 * @since 2008-09-19
	 */
	const AUDIT_SUCCESS				= 128;
	/**
	 * Used to log failed audit actions
	 *
	 * @var integer
	 * @since 2008-09-19
	 */
	const AUDIT_FAILURE				= 256;
	
	/**
	 * Constant to handle exceptions so we can trigger a fatal error; this is NOT used for logging
	 *
	 * @var integer
	 */
	const EXCEPTION					= 4096;
	
	
	
	/**
	 * Converts a PHP error code to a human readable string
	 *
	 * @param integer $inErrno
	 * @return string
	 * @access public
	 * @static 
	 */
	public static function convertErrorNoToString($inErrno) {
		$errortype = array (
			1    => 'ERROR',
			2    => 'WARNING',
			4    => 'PARSING ERROR',
			8    => 'NOTICE',
			9	 => 'GENERAL_MESSAGE',
			16   => 'CORE ERROR',
			32   => 'CORE WARNING',
			64   => 'COMPILE ERROR',
			128  => 'COMPILE WARNING',
			256  => 'FATAL ERROR',
			512  => 'BIG ERROR',
			1024 => 'USER NOTICE',
			2048 => 'PHP5 COMPLIANCE',
			4096 => 'RECOVERABLE ERROR', // php 5.2+ possibly was E_EXCEPTION?
			8192 => 'DEPRECATED', // php 5.3+
			16384 => 'USER DEPRECATED', // php 5.3+
		);
		if ( array_key_exists($inErrno, $errortype) ) {
			return $errortype[$inErrno];
		} else {
			return 'UNKNOWN ERROR';
		}
	}
	
	/**
	 * Converts the Scorpio logLevel to a string, returns 'custom' when not known
	 *
	 * @param integer $inLogLevel
	 * @return string
	 * @access public
	 * @static 
	 */
	public static function convertLogLevelToString($inLogLevel) {
		$logLevels = array(
			1	=> 'ALWAYS',
			2	=> 'CRITICAL',
			4	=> 'ERROR',
			8	=> 'WARNING',
			16	=> 'NOTICE',
			32	=> 'INFO',
			64	=> 'DEBUG',
			128 => 'AUDIT OK',
			256 => 'AUDIT FAIL',
		);
		if ( array_key_exists($inLogLevel, $logLevels) ) {
			return $logLevels[$inLogLevel];
		} else {
			return 'CUSTOM';
		}
	}
}