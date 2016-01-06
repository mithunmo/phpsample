<?php
/**
 * testSuiteTools
 * 
 * Store in testSuiteTools.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage testSuite
 * @category testSuiteTools
 * @version $Rev: 650 $
 */


/**
 * testSuiteTools class
 * 
 * A set of static methods for working with the testSuite.
 * 
 * @package scorpio
 * @subpackage testSuite
 * @category testSuiteTools
 */
class testSuiteTools {
	
	const TEST_FOLDER = 'tests';
	
	/**
	 * Prevent instantiation
	 */
	private function __construct() {}
	
	
	
	/**
	 * Returns the test folder location
	 *
	 * @return string
	 * @static
	 */
	static function getTestFolder() {
		return system::getConfig()->getPathData()->getParamValue().system::getDirSeparator().self::TEST_FOLDER;
	}
}