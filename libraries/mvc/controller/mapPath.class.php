<?php
/**
 * mvcControllerMapPath.class.php
 * 
 * mvcControllerMapPath class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcControllerMapPath
 * @version $Rev: 707 $
 */


/**
 * mvcControllerMapPath
 * 
 * Object for holding the path components to make it easier to manipulate
 * under object wrapper. Used by {@link mvcControllerMap::getPathComponents()}
 * 
 * <code>
 * // set properties from constructor
 * $oMap = new mvcControllerMapPath('controller', 'Description', '/path/to/controller');
 * 
 * // set properties using methods
 * $oMap = new mvcControllerMapPath();
 * $oMap
 *     ->setControllerName()
 *     ->setPath();
 * </code>
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcControllerMapPath
 */
class mvcControllerMapPath {
	
	/**
	 * Stores $_Modified
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified;
	
	/**
	 * Stores $_ControllerName
	 *
	 * @var string
	 * @access protected
	 */
	protected $_ControllerName;
	
	/**
	 * Stores $_Description
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Description;
	
	/**
	 * Stores $_Path
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Path;
	
	
	
	/**
	 * Creates a new mvcControllerMapPath object
	 *
	 * @return void
	 */
	function __construct($inControllerName = null, $inDescription = null, $inPath = null) {
		$this->reset();
		if ( $inControllerName !== null ) {
			$this->setControllerName($inControllerName);
		}
		if ( $inDescription !== null ) {
			$this->setDescription($inDescription);
		}
		if ( $inPath !== null ) {
			$this->setPath($inPath);
		}
	}
	
	/**
	 * Resets object
	 *
	 * @return void
	 */
	function reset() {
		$this->_ControllerName = null;
		$this->_Description = null;
		$this->_Path = null;
		$this->_Modified = false;
	}
	
	
	
	/**
	 * Returns $_Modified
	 *
	 * @return boolean
	 */
	function isModified() {
		return $this->_Modified;
	}
	
	/**
	 * Set $_Modified to $inModified
	 *
	 * @param boolean $inModified
	 * @return mvcControllerMapPath
	 */
	function setModified($inModified = true) {
		if ( $inModified !== $this->_Modified ) {
			$this->_Modified = $inModified;
		}
		return $this;
	}
	
	/**
	 * Returns $_ControllerName
	 *
	 * @return string
	 */
	function getControllerName() {
		return $this->_ControllerName;
	}
	
	/**
	 * Set $_ControllerName to $inControllerName
	 *
	 * @param string $inControllerName
	 * @return mvcControllerMapPath
	 */
	function setControllerName($inControllerName) {
		if ( $inControllerName !== $this->_ControllerName ) {
			$this->_ControllerName = $inControllerName;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_Description
	 *
	 * @return string
	 */
	function getDescription() {
		return $this->_Description;
	}
	
	/**
	 * Set $_Description to $inDescription
	 *
	 * @param string $inDescription
	 * @return mvcControllerMapPath
	 */
	function setDescription($inDescription) {
		if ( $inDescription !== $this->_Description ) {
			$this->_Description = $inDescription;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_Path
	 *
	 * @return string
	 */
	function getPath() {
		return $this->_Path;
	}
	
	/**
	 * Set $_Path to $inPath
	 *
	 * @param string $inPath
	 * @return mvcControllerMapPath
	 */
	function setPath($inPath) {
		if ( $inPath !== $this->_Path ) {
			$this->_Path = $inPath;
			$this->setModified();
		}
		return $this;
	}
}