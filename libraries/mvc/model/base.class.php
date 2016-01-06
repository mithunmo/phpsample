<?php
/**
 * mvcModelBase.class.php
 * 
 * mvcModelBase class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcModelBase
 * @version $Rev: 707 $
 */


/**
 * mvcModelBase class
 * 
 * mvcModelBase class that all models can inherit from. Provides the most
 * basic methods a model may need.
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcModelBase
 */
class mvcModelBase {

	/**
	 * Stores $_Modified
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified;
	
	
	
	/**
	 * Returns new mvcModelBase instance
	 *
	 * @return mvcModelBase
	 */
	function __construct() {
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
	 * @return mvcModelBase
	 */
	function setModified($inModified = true) {
		if ( $inModified !== $this->_Modified ) {
			$this->_Modified = $inModified;
		}
		return $this;
	}
}