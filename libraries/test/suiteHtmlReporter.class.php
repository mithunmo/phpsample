<?php
/**
 * testSuiteHtmlReporter
 * 
 * Store in testSuiteHtmlReporter.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage testSuite
 * @category testSuiteHtmlReporter
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
 * testSuiteHtmlReporter class
 * 
 * Custom Reporter for the Scorpio framework. This reporter collects the output in an
 * internal buffer that can then be fetched and displayed. This is needed for ajax
 * running of the unit tests.
 * 
 * @package scorpio
 * @subpackage testSuite
 * @category testSuiteHtmlReporter
 */
class testSuiteHtmlReporter extends PHPUnit_Extensions_Story_ResultPrinter_HTML {
	
	/**
	 * Stores $_TestTitle
	 *
	 * @var string
	 * @access protected
	 */
	protected $_TestTitle;
	
	/**
	 * Stores $_Buffer
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Buffer;
	
	/**
	 * Toggles showing passes or not
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_ShowPasses;
	
	
	
	/**
	 * Creates the reporter
	 */
	function __construct($character_set = 'UTF-8', $inShowPasses = false) {
		parent::__construct();
		
		$this->_TestTitle = null;
		$this->_Buffer = null;
		$this->_ShowPasses = $inShowPasses;
	}

	/**
	 * Returns $_TestTitle
	 *
	 * @return string
	 * @access public
	 */
	function getTestTitle() {
		return $this->_TestTitle;
	}
	
	/**
	 * Set $_TestTitle to $inTestTitle
	 *
	 * @param string $inTestTitle
	 * @return testSuiteHtmlReporter
	 * @access public
	 */
	function setTestTitle($inTestTitle) {
		if ( $this->_TestTitle !== $inTestTitle ) {
			$this->_TestTitle = $inTestTitle;
		}
		return $this;
	}
	
	/**
	 * Returns $_Buffer
	 *
	 * @return string
	 * @access public
	 */
	function getBuffer() {
		return $this->_Buffer;
	}

	/**
	 * Set $_Buffer to $inBuffer
	 *
	 * @param string $inBuffer
	 * @return testSuiteHtmlReporter
	 * @access public
	 */
	function setBuffer($inBuffer) {
		if ( $this->_Buffer !== $inBuffer ) {
			$this->_Buffer = $inBuffer;
		}
		return $this;
	}
}