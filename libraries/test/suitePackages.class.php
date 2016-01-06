<?php
/**
 * testSuitePackages
 * 
 * Store in testSuitePackages.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage testSuite
 * @category testSuitePackages
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
 * testSuitePackages class
 * 
 * Provides methods for creating test suite packages by locating test case files
 * in the tests folder in /data.
 * 
 * The testSuite classes require a correctly configured PHPUnit installation.
 * 
 * @package scorpio
 * @subpackage testSuite
 * @category testSuitePackages
 */
class testSuitePackages extends baseSet {
	
	/**
	 * Holds a single instance of the testSuitePackages
	 *
	 * @var testSuitePackages
	 * @access private
	 * @static
	 */
	private static $_Instance = null;
	
	
	
	/**
	 * Creates a new testSuitePackages object
	 *
	 * @return testSuiteObjects
	 */
	function __construct() {
		$this->reset();
	}
	
	

	/**
	 * Gets a single instance of the testSuitePackages object, creating and populating it
	 * if it does not exist already. Optionally specify the test suite folder.
	 *
	 * @param string $inTestSuiteFolder
	 * @return testSuitePackages
	 * @static
	 */
	static function getInstance($inTestSuiteFolder = null) {
		if ( !self::$_Instance instanceof testSuitePackages ) {
			self::$_Instance = self::buildPackageSet($inTestSuiteFolder);
		}
		return self::$_Instance;
	}
	
	/**
	 * Used to replace the existing instance of the package set. This method will
	 * nullify the existing instance. Nothing is returned.
	 *
	 * @param testSuitePackages $inPackageSet
	 * @return void
	 * @static
	 */
	static function setInstance(testSuitePackages $inPackageSet) {
		self::$_Instance = null;
		self::$_Instance = $inPackageSet;
	}
	
	/**
	 * Returns a testSuitePackages set containing the details of all test cases
	 * located within the current test folder location
	 *
	 * @param string $inTestSuiteFolder
	 * @return testSuitePackages
	 * @static
	 */
	static function buildPackageSet($inTestSuiteFolder = null) {
		if ( $inTestSuiteFolder !== null && is_readable($inTestSuiteFolder)) {
			$testFolder = $inTestSuiteFolder;
		} else {
			$testFolder = testSuiteTools::getTestFolder();
		}
		
		$oPackageSet = new testSuitePackages();
		$files = fileObject::parseDir($testFolder, true);
		if ( count($files) > 0 ) {
			if ( false ) $oFile = new fileObject();
			foreach ( $files as $oFile ) {
				/*
				 * Ignore exception classes
				 */
				if ( strpos($oFile->getPath(), 'exception') !== false ) {
					continue;
				}
				/*
				 * Ignore SVN folders
				 */
				if ( strpos($oFile->getPath(), '.svn') !== false ) {
					continue;
				}
				
				/*
				 * Use only .class.php files, ignore everything else
				 */
				if ( !preg_match('/.class.php$/', $oFile->getFilename()) ) {
					continue;
				}
				
				/*
				 * strip off the full path leaving just the stub
				 * e.g. /home/user/data/tests/someLibrary/xxxxxx.php
				 * becomes: someLibrary/xxxxxx.php
				 */
				$path = str_replace($testFolder, '', $oFile->getPath());
				if ( strpos($path, system::getDirSeparator()) === 0 ) {
					$path = substr($path, 1);
				}
				/*
				 * We can now explode the path into 2 chunks, the first should be the package, the second the file
				 */
				list($package, ) = explode(system::getDirSeparator(), $path, 2);
				
				$oPackage = $oPackageSet->getPackage($package);
				if ( !$oPackage instanceof testSuitePackage ) {
					$oPackage = new testSuitePackage($package, $path);
					$oPackageSet->addPackage($oPackage);
				}
				
				/*
				 * File is in a sub folder in the package
				 */
				if ( preg_match('/.php$/', $oFile->getFilename()) ) {
					$subpackage = $oFile->getFilename();
				}
				
				if ( $subpackage ) {
					list($subpackage, ) = explode('.', $subpackage);
					$oPackage->getPackageSet()->addPackage(
						new testSuitePackage($subpackage, $oFile->getPath().system::getDirSeparator().$oFile->getFilename())
					);
				}
			}
		}
		return $oPackageSet;
	}
	
	/**
	 * Creates a test suite object from the available packages and returns it.
	 * 
	 * Optionally, a test suite will be created for the specified package or
	 * the specific test case within a package. The returned SimpleTest
	 * TestSuite can then be used with any reporter / render.
	 *
	 * @param testSuitePackages $inTestSuitePackageSet
	 * @param string $inPackage
	 * @param string $inTestCase
	 * @return PHPUnit_Framework_TestSuite
	 * @static
	 */
	static function createTestSuite(testSuitePackages $inTestSuitePackageSet, $inPackage = null, $inTestCase = null) {
		$oTestSuite = new PHPUnit_Framework_TestSuite();
		
		if ( false ) $oPackage = new testSuitePackage();
		if ( false ) $oTestDetails = new testSuitePackage();
		foreach ( $inTestSuitePackageSet as $packageName => $oPackage ) {
			foreach ( $oPackage->getPackageSet() as $testCaseName => $oTestDetails ) {
				if ( $inPackage === null && $inTestCase === null ) {
					$add = true;
				} elseif ( $inPackage && $packageName == $inPackage && $inTestCase === null ) {
					$add = true;
				} elseif ( $inPackage && $packageName == $inPackage && $inTestCase && $inTestCase == $testCaseName ) {
					$add = true;
				} else {
					$add = false;
				}
				
				if ( $add ) {
					systemLog::message("Adding $packageName @ {$oTestDetails->getLocation()} to test suite");
					$oTestSuite->addTestFile($oTestDetails->getLocation());
				}
			}
		}
		return $oTestSuite;
	}
	
	
	
	/**
	 * Resets the object
	 *
	 * @return void
	 */
	function reset() {
		parent::_resetSet();
	}
	
	/**
	 * Adds the test package to the set of packages
	 *
	 * @param testSuitePackage $inPackage
	 * @return testSuitePackages
	 */
	function addPackage(testSuitePackage $inPackage) {
		return $this->_setItem($inPackage->getPackageName(), $inPackage);
	}
	
	/**
	 * Removes the package from the set
	 *
	 * @param testSuitePackage $inPackage
	 * @return testSuitePackages
	 */
	function removePackage(testSuitePackage $inPackage) {
		return $this->_removeItem($inPackage->getPackageName());
	}
	
	/**
	 * Fetches the package named $inName, false if not found
	 *
	 * @param string $inName
	 * @return testSuitePackage
	 */
	function getPackage($inName) {
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oPackage ) {
				if ( $oPackage->getPackageName() == $inName ) {
					return $oPackage;
				}
			}
		}
		return false;
	}

	/**
	 * Returns true if the package exists in this package
	 *
	 * @param string $inName
	 * @return boolean
	 */
	function hasPackage($inName) {
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oPackage ) {
				if ( $oPackage->getPackageName() == $inName ) {
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 * Returns all packages
	 *
	 * @return array
	 */
	function getPackages() {
		return $this->_getItem();
	}
	
	/**
	 * Returns the number of packages
	 *
	 * @return integer
	 */
	function getCount() {
		return $this->_itemCount();
	}
}