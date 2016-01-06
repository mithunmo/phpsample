<?php
/**
 * testCaseScorpio
 * 
 * Store in testCaseScorpio.class.php
 * 
 * @author Dave Redfern
 * @author Rolf Wessels
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage testSuite
 * @category testCaseScorpio
 * @version $Rev: 736 $
 */


/*
 * Load dependencies
 */
if ( @file_exists('PHPUnit/Autoload.php') && is_readable('PHPUnit/Autoload.php') ) {
	require_once 'PHPUnit/Autoload.php';
} else {
	require_once 'PHPUnit/Framework.php';
}


/**
 * testCaseScorpio class
 * 
 * Extended unit test adding in database support
 * 
 * @package scorpio
 * @subpackage testSuite
 * @category testCaseScorpio
 * @abstract 
 */
abstract class testCaseScorpio extends PHPUnit_Framework_TestCase {

	/**
	 * PHPUnit, enforce no production test cases
	 * 
	 * @return void
	 */
	function setUp() {
		parent::setUp();
		if ( system::getConfig()->isProduction() ) {
			$this->markTestSkipped('System is set for production mode, all tests will be skipped');
		}
	}

	/**
	 * Stores $_database
	 *
	 * @var string
	 */
	protected $_database;

	/**
	 * Returns database
	 *
	 * @return string
	 */
	function getDatabase() {
		return $this->_database;
	}

	/**
	 * Sets the current database
	 *
	 * @param string $inDatabase
	 */
	function setDatabase($inDatabase) {
		if ( $inDatabase !== $this->_database ) {
			$this->_database = $inDatabase;
			$this->_changed = true;
		}
	}
	
	/**
	 * Pass depending on $value1
	 * 
	 * @param mixed $value1
	 * @return mixed
	 */
	function pass($value1) {
		// do nothing
	}
	
	/**
	 * Wraps previous use of assertEqual to PHPUnit assertEquals
	 * 
	 * @param mixed $value1
	 * @param mixed $value2
	 * @return mixed
	 */
	function assertEqual($value1, $value2) {
		return $this->assertEquals($value1, $value2);
	}

	/**
	 * We have to update the assert because original version allows
	 * 
	 * @return void
	 */
	static function assertType($expected, $object) {
		if ( is_string($expected) && is_object($object) ) {
			parent::assertType($expected, $object);
		} else {
			parent::assertType($object, $expected);
		}
	}
	
	/**
	 * Wrapper to PHPUnit assertPattern
	 * 
	 * @param string $expression
	 * @param mixed $value
	 */
	static function assertPattern($expression, $value) {
		parent::assertRegexp($expression, $value);
	}
	
	/**
	 * Wrapper to PHPUnit assertNoPattern
	 * 
	 * @param string $expression
	 * @param mixed $value
	 */
	static function assertNoPattern($expression, $value) {
		parent::assertNotRegExp($expression, $value);
	}
	
	/**
	 * Wrapper to PHPUnit not equals
	 * 
	 * @param mixed $value1
	 * @param mixed $value2
	 */
	static function assertNotEqual($value1, $value2) {
		parent::assertNotEquals($value1, $value2);
	}
	
	/**
	 * Wrapper for assertIsA
	 * 
	 * @param object $value1 Object
	 * @param string $value2 Class name
	 * @param string $value3 Message
	 */
	static function assertIsA($value1, $value2, $value3 = "") {
		parent::assertType($value2, $value1, $value3);
	}
	
	/**
	 * Wrapper for assertIdentical
	 * 
	 * @param mixed $value1
	 * @param mixed $value2
	 * @param string $value3
	 */
	static function assertIdentical($value1, $value2, $value3 = "") {
		parent::assertEquals($value1, $value2, $value3);
	}
}