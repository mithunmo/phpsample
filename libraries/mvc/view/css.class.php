<?php
/**
 * mvcViewCss.class.php
 * 
 * mvcViewCss class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewCss
 * @version $Rev: 707 $
 */


/**
 * mvcViewCss class
 * 
 * Represents an additional CSS resource to be used in the view for rendering.
 * This allows for external libraries to be loaded and rendered more easily.
 * Generates a valid XHTML style block or <link ... /> depending on the type.
 * 
 * <code>
 * // add a separate stylesheet file
 * $oCss = new mvcViewCss('mycss', mvcViewCss::TYPE_FILE, '/path/to/file.css');
 * 
 * // add some inline CSS
 * $oCss = new mvcViewCss('mycss', mvcViewCss::TYPE_INLINE, 'body { font-weight: bold; font-size: 12px; }');
 * 
 * // add some CSS specifically for a media type
 * $oCss = new mvcViewCss(
 *     'mycss', mvcViewCss::TYPE_INLINE, 'body { font-weight: normal; font-size: small; }', mvcViewCss::MEDIA_MOBILE
 * );
 * </code>
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewCss
 */
class mvcViewCss extends mvcViewResource {
	
	/**
	 * Stores $_MediaType
	 *
	 * @var string
	 * @access protected
	 */
	protected $_MediaType;
	
	const MEDIA_TYPE_ALL = 'all';
	const MEDIA_TYPE_BRAILLE = 'braille';
	const MEDIA_TYPE_EMBOSSED = 'embossed';
	const MEDIA_TYPE_HANDHELD = 'handheld';
	const MEDIA_TYPE_MOBILE = 'handheld';
	const MEDIA_TYPE_PRINT = 'print';
	const MEDIA_TYPE_PROJECTION = 'projection';
	const MEDIA_TYPE_SCREEN = 'screen';
	const MEDIA_TYPE_SPEECH = 'speech';
	const MEDIA_TYPE_TTY = 'tty';
	const MEDIA_TYPE_TV = 'tv';
	
	/**
	 * Stores $_LinkType
	 *
	 * @var string
	 * @access protected
	 */
	protected $_LinkType;
	
	const LINK_TYPE_STYLESHEET = 'stylesheet';
	const LINK_TYPE_ALTERNATE_STYLESHEET = 'alternate stylesheet';
	
	
	
	/**
	 * Creates a new CSS object
	 * 
	 * @param string $inIdentifier A unique identifier, will be generated if not set
	 * @param string $inType Either a file or CSS code block
	 * @param string $inResource Either the path or a CSS block
	 * @param string $inMediaType The media type
	 * @param string $inLinkType For files, sets if stylesheet or alternate
	 * @param string $inMimeType Mime-type, text/css
	 * @return mvcViewCss
	 */
	function __construct($inIdentifier = null, $inType = self::TYPE_FILE, $inResource = '', $inMediaType = self::MEDIA_TYPE_SCREEN, $inLinkType = self::LINK_TYPE_STYLESHEET, $inMimeType = 'text/css') {
		$this->reset();
		if ( $inIdentifier !== null ) {
			$this->setIdentifier($inIdentifier);
		}
		$this->setType($inType);
		$this->setResource($inResource);
		$this->setMediaType($inMediaType);
		$this->setLinkType($inLinkType);
		$this->setMimeType($inMimeType);
	}
	
	/**
	 * Converts the object to a string
	 * 
	 * @return string
	 */
	function __toString() {
		$return = '';
		if ( $this->getType() == self::TYPE_FILE ) {
			$return = '<link rel="'.$this->getLinkType().'" type="'.$this->getMimeType().'" href="'.$this->getResource().'" media="'.$this->getMediaType().'" />';
		} else {
			$return = '<style type="'.$this->getMimeType().'">'."\n<!--/*--><![CDATA[/*><!-- */\n".$this->getResource()."\n/*]]>*/-->\n</style>";
		}
		return $return;
	}
	
	/**
	 * Resets the object
	 * 
	 * @return void
	 */
	function reset() {
		$this->_Identifier = md5(microtime(true));
		$this->_Type = self::TYPE_FILE;
		$this->_Resource = '';
		$this->_MediaType = self::MEDIA_TYPE_SCREEN;
		$this->_LinkType = self::LINK_TYPE_STYLESHEET;
		$this->_MimeType = 'text/css';
		$this->setModified(false);
	}
	
	
	
	/**
	 * Returns the media type e.g. screen, print etc
	 *
	 * @return string
	 */
	function getMediaType() {
		return $this->_MediaType;
	}
	
	/**
	 * Set $_MediaType to $inMediaType
	 *
	 * @param string $inMediaType
	 * @return mvcViewCss
	 */
	function setMediaType($inMediaType) {
		if ( $inMediaType !== $this->_MediaType ) {
			$this->_MediaType = $inMediaType;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the link type, usually stylesheet but also alternate stylesheet
	 *
	 * @return string
	 */
	function getLinkType() {
		return $this->_LinkType;
	}
	
	/**
	 * Set $_LinkType to $inLinkType
	 *
	 * @param string $inLinkType
	 * @return mvcViewCss
	 */
	function setLinkType($inLinkType) {
		if ( $inLinkType !== $this->_LinkType ) {
			$this->_LinkType = $inLinkType;
			$this->setModified();
		}
		return $this;
	}
}