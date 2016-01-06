<?php
/**
 * mvcViewResource.class.php
 * 
 * mvcViewResource class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewResource
 * @version $Rev: 661 $
 */


/**
 * mvcViewResource class
 * 
 * An abstract resource body used by CSS and Javascript resources. Allows them
 * to be added more easily to views for rendering when external libraries are
 * needed in an adhoc basis.
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewResource
 */
abstract class mvcViewResource {
	
	/**
	 * Stores $_Modified
	 * 
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;
	
	/**
	 * Stores $_Identifier
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Identifier;
	
	/**
	 * Stores $_Type
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Type;
	
	const TYPE_FILE = 'file';
	const TYPE_INLINE = 'inline';
	
	/**
	 * Stores $_Resource
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Resource;
	
	/**
	 * Stores $_MimeType
	 *
	 * @var string
	 * @access protected
	 */
	protected $_MimeType;
	
	
	
	/**
	 * Converts the object to a string
	 * 
	 * @return string
	 */
	abstract function __toString();
	
	/**
	 * Resets the object
	 * 
	 * @return void
	 */
	abstract function reset();
	
	/**
	 * Additional wrapper for __toString()
	 * 
	 * @return string
	 */
	function toString() {
		return $this->__toString();
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
	 * Set the status of the object if it has been changed
	 * 
	 * @param boolean $status
	 * @return mvcViewResource
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}

	/**
	 * Returns the unique identifier for this CSS object
	 *
	 * @return string
	 */
	function getIdentifier() {
		return $this->_Identifier;
	}
	
	/**
	 * Set $_Identifier to $inIdentifier
	 *
	 * @param string $inIdentifier
	 * @return mvcViewResource
	 */
	function setIdentifier($inIdentifier) {
		if ( $inIdentifier !== $this->_Identifier ) {
			$this->_Identifier = $inIdentifier;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the type, either a file location or inline style rules
	 *
	 * @return string
	 */
	function getType() {
		return $this->_Type;
	}
	
	/**
	 * Set $_Type to $inType
	 *
	 * @param string $inType
	 * @return mvcViewResource
	 */
	function setType($inType) {
		if ( $inType !== $this->_Type ) {
			$this->_Type = $inType;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the resources, either a URI / path link or a block of CSS code
	 *
	 * @return string
	 */
	function getResource() {
		return $this->_Resource;
	}
	
	/**
	 * Set $_Resource to $inResource
	 *
	 * @param string $inResource
	 * @return mvcViewResource
	 */
	function setResource($inResource) {
		if ( $inResource !== $this->_Resource ) {
			$this->_Resource = $inResource;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the mime-type to use for the CSS
	 *
	 * @return string
	 */
	function getMimeType() {
		return $this->_MimeType;
	}
	
	/**
	 * Set $_MimeType to $inMimeType
	 *
	 * @param string $inMimeType
	 * @return mvcViewResource
	 */
	function setMimeType($inMimeType) {
		if ( $inMimeType !== $this->_MimeType ) {
			$this->_MimeType = $inMimeType;
			$this->setModified();
		}
		return $this;
	}
}