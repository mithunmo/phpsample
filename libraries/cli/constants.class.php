<?php
/**
 * cliConstants Class
 * 
 * Stored in constants.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliConstants
 * @version $Rev: 650 $
 */


/**
 * cliConstants
 *
 * Holds constants used through-out the CLI system so they are
 * centralised in one location.
 * 
 * @package scorpio
 * @subpackage cli
 * @category cliConstants
 */
class cliConstants {
	
	/**
	 * This is a static class
	 */
	private function __construct() {}
	
	/**
	 * Max number of characters to display on one line
	 *
	 * @var integer
	 */
	const CONSOLE_WIDTH = 80;
	
	/**
	 * The name of the string that is used to mark a line as being empty
	 *
	 * @var string
	 */
	const APP_ARGS_NEWLINE = 'NEWLINE';
}