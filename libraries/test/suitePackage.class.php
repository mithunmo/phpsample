<?php
/**
 * testSuitePackage
 * 
 * Store in testSuitePackage.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage testSuite
 * @category testSuitePackage
 * @version $Rev: 650 $
 */


/**
 * testSuitePackage class
 * 
 * Holds information about a test suite package
 * 
 * @package scorpio
 * @subpackage testSuite
 * @category testSuitePackage
 */
class testSuitePackage {
	
	/**
	 * Stores $_Modified
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified;
	
	/**
	 * Stores $_PackageName
	 *
	 * @var string
	 * @access protected
	 */
	protected $_PackageName;
	
	/**
	 * Stores $_Location
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Location;
	
	/**
	 * Stores $_PackageSet
	 *
	 * @var testSuitePackages
	 * @access protected
	 */
	protected $_PackageSet;
	
	
	
	/**
	 * Creates a new package object
	 *
	 * @param string $inPackageName
	 * @param string $inLocation
	 */
	function __construct($inPackageName, $inLocation = null, $inPackages = null) {
		$this->reset();
		$this->setPackageName($inPackageName);
		$this->setLocation($inLocation);
		if ( $inPackages !== null && $inPackages instanceof testSuitePackages ) {
			$this->setPackageSet($inPackages);
		}
	}
	
	/**
	 * Resets the object
	 *
	 * @return void
	 */
	function reset() {
		$this->_PackageName = null;
		$this->_Location = null;
		$this->_PackageSet = null;
		$this->_Modified = false;
	}
	
	/**
	 * Returns true if object has been modified
	 *
	 * @return boolean
	 */
	function isModified() {
		return $this->_Modified;
	}
	
	/**
	 * Set $_Modified to $inStatus
	 *
	 * @param boolean $inStatus
	 * @return testSuitePackage
	 */
	function setModified($inStatus = true) {
		$this->_Modified = $inStatus;
		return $this;
	}
	
	/**
	 * Returns $_PackageName
	 *
	 * @return string
	 * @access public
	 */
	function getPackageName() {
		return $this->_PackageName;
	}
	
	/**
	 * Set $_PackageName to $inPackageName
	 *
	 * @param string $inPackageName
	 * @return testSuitePackage
	 * @access public
	 */
	function setPackageName($inPackageName) {
		if ( $this->_PackageName !== $inPackageName ) {
			$this->_PackageName = $inPackageName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Location
	 *
	 * @return string
	 * @access public
	 */
	function getLocation() {
		return $this->_Location;
	}
	
	/**
	 * Set $_Location to $inLocation
	 *
	 * @param string $inLocation
	 * @return testSuitePackage
	 * @access public
	 */
	function setLocation($inLocation) {
		if ( $this->_Location !== $inLocation ) {
			$this->_Location = $inLocation;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_PackageSet
	 *
	 * @return testSuitePackages
	 * @access public
	 */
	function getPackageSet() {
		if ( !$this->_PackageSet instanceof testSuitePackages ) {
			$this->_PackageSet = new testSuitePackages();
		}
		return $this->_PackageSet;
	}
	
	/**
	 * Set $_PackageSet to $inPackageSet
	 *
	 * @param testSuitePackages $inPackageSet
	 * @return testSuitePackage
	 * @access public
	 */
	function setPackageSet($inPackageSet) {
		if ( $this->_PackageSet !== $inPackageSet ) {
			$this->_PackageSet = $inPackageSet;
			$this->setModified();
		}
		return $this;
	}
}