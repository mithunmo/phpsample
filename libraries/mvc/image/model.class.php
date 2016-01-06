<?php
/**
 * model.class.php
 * 
 * mvcImageModel class
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcImageModel
 * @version $Rev: 650 $
 */


/**
 * mvcImageModel
 * 
 * Provides the base "image" model including options support and some utility methods.
 * This class should be extended in your site libraries folder.
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcImageModel
 */
abstract class mvcImageModel extends mvcModelBase implements mvcImageProcessor {
	
	/**
	 * Stores $_OptionsSet
	 *
	 * @var baseOptionsSet
	 * @access protected
	 */
	protected $_OptionsSet;
	
	const OPTION_IMAGE_MODEL = 'image.model';
	const OPTION_IMAGE_IDENTIFIER = 'image.identifier';
	const OPTION_IMAGE_DIMENSIONS = 'image.dimensions';
	const OPTION_IMAGE_URINAME = 'image.uriname';
	const OPTION_SITE_IMAGES_PATH = 'site.images.path';
		
	/**
	 * Stores $_ImageLocation
	 *
	 * @var string
	 * @access protected
	 */
	protected $_ImageLocation;
	
	
	
	/**
	 * @see mvcModelBase::__construct()
	 */
	function __construct() {
		parent::__construct();
		
		$this->_OptionsSet = null;
		$this->_ImageLocation = '';
	}
	
	
	
	/**
	 * Returns the image identifier, false if not set
	 *
	 * @return string
	 */
	function getImageIdentifier() {
		return $this->getOptionsSet()->getOptions(self::OPTION_IMAGE_IDENTIFIER, false);
	}
	
	/**
	 * Returns the image dimensions if set, false otherwise
	 *
	 * @return string
	 */
	function getImageDimensions() {
		return $this->getOptionsSet()->getOptions(self::OPTION_IMAGE_DIMENSIONS, false);
	}
	
	/**
	 * Returns just the width component
	 *
	 * @return integer
	 */
	function getWidth() {
		list($width, $height) = explode('x', $this->getImageDimensions());
		return $width;
	}
	
	/**
	 * Returns just the height component
	 *
	 * @return integer
	 */
	function getHeight() {
		list($width, $height) = explode('x', $this->getImageDimensions());
		return $height;
	}
	
	/**
	 * Returns the image URI name, false if not set
	 *
	 * @return string
	 */
	function getImageUriName() {
		return $this->getOptionsSet()->getOptions(self::OPTION_IMAGE_URINAME, false);
	}
	
	/**
	 * Returns the full path to the current sites images folder
	 *
	 * @return string
	 */
	function getSiteImagesPath() {
		return $this->getOptionsSet()->getOptions(self::OPTION_SITE_IMAGES_PATH, false);
	}
	
	/**
	 * Sets an array of options
	 *
	 * @param array $inArray
	 * @return mvcImageModel
	 */
	function setOptions(array $inArray = array()) {
		$this->getOptionsSet()->setOptions($inArray);
		return $this;
	}
	
	/**
	 * Returns $_OptionsSet
	 *
	 * @return baseOptionsSet
	 * @access public
	 */
	function getOptionsSet() {
		if ( !$this->_OptionsSet instanceof baseOptionsSet ) {
			$this->_OptionsSet = new baseOptionsSet();
		}
		return $this->_OptionsSet;
	}
	
	/**
	 * Set $_OptionsSet to $inOptionsSet
	 *
	 * @param baseOptionsSet $inOptionsSet
	 * @return mvcImageModel
	 * @access public
	 */
	function setOptionsSet($inOptionsSet) {
		if ( $this->_OptionsSet !== $inOptionsSet ) {
			$this->_OptionsSet = $inOptionsSet;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Basic function to get the mime-type of an image from the extension
	 * 
	 * @see libraries/mvc/image/mvcImageProcessor#getImageMimeType()
	 * @return string
	 */
	function getImageMimeType() {
		$extn = false;
		if ( $this->getImageLocation() ) {
			$extn = substr($this->getImageLocation(), strrpos($this->getImageLocation(), '.')+1);
		}
		
		switch ( $extn ) {
			case 'jpeg':
			case 'jpg':
				return 'image/jpeg';
			break;
			
			case 'gif':
				return 'image/gif';
			break;
			
			case 'png':
				return 'image/png';
			break;
			
			case 'svg':
				return 'image/svg+xml';
			break;
			
			case 'bmp':
				return 'image/bmp';
			break;
			
			case 'swf':
				return 'application/x-shockwave-flash';
			break;
			
			default:
				return 'application/octet-stream';
		}
	}

	/**
	 * Returns $_ImageLocation, the final location of the image
	 *
	 * @return string
	 * @access public
	 */
	function getImageLocation() {
		return $this->_ImageLocation;
	}
	
	/**
	 * Set $_ImageLocation to $inImageLocation
	 *
	 * @param string $inImageLocation
	 * @return mvcImageModel
	 * @access public
	 */
	function setImageLocation($inImageLocation) {
		if ( $this->_ImageLocation !== $inImageLocation ) {
			$this->_ImageLocation = $inImageLocation;
			$this->setModified();
		}
		return $this;
	}
}