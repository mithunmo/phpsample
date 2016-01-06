<?php
/**
 * mvcViewMeta.class.php
 * 
 * mvcViewMeta class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewMeta
 * @version $Rev: 813 $
 */


/**
 * mvcViewMeta class
 * 
 * Represents an additional meta tag in the HTML <head> tag. There are two types
 * of meta tag: http-equiv that are interpreted as if they should have been sent
 * as a HTTP header, and "name" tags. Named tags usually contain the keywords,
 * description and other custom head tags.
 * 
 * <code>
 * // add a meta redirect
 * $oMeta = new mvcViewMeta(
 *     'redirect', mvcViewMeta::META_TYPE_HTTP_EQUIV, 'redirect', '10;url=http://domain.com'
 * );
 * </code>
 * 
 * If an unsupported meta type is used, the output will be a standard <meta name=
 * tag.
 * 
 * MimeType and Resource type are not used by this resource.
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewMeta
 */
class mvcViewMeta extends mvcViewResource {
	
	/**
	 * Stores $_MetaType
	 *
	 * @var string
	 * @access protected
	 */
	protected $_MetaType;
	
	const META_TYPE_HTTP_EQUIV = 1;
	const META_TYPE_NAME = 2;
	
	/**
	 * Stores $_MetaName
	 *
	 * @var string
	 * @access protected
	 */
	protected $_MetaName;
	
	const META_NAME_AUTHOR = 'author';
	const META_NAME_COPYRIGHT = 'copyright';
	const META_NAME_DESCRIPTION = 'description';
	const META_NAME_KEYWORDS = 'keywords';
	const META_NAME_REDIRECT = 'refresh';
	const META_NAME_REFRESH = 'refresh';
	const META_NAME_ROBOTS = 'robots';
	
	/**
	 * Stores $_Content
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Content;
	
	
	
	/**
	 * Creates a new CSS object
	 * 
	 * @param string $inIdentifier A unique identifier, will be generated if not set
	 * 
	 * @return mvcViewMeta
	 */
	function __construct($inIdentifier = null, $inMetaType = self::META_TYPE_NAME, $inMetaName = '', $inContent = '') {
		$this->reset();
		if ( $inIdentifier !== null ) {
			$this->setIdentifier($inIdentifier);
		}
		$this->setMetaType($inMetaType);
		$this->setMetaName($inMetaName);
		$this->setContent($inContent);
	}
	
	/**
	 * Creates a meta-redirect link for a view
	 * 
	 * @param string $inLocation (required) Location to redirect to, URIs require protocol
	 * @param integer $inTimeout (optional) Delay before redirection occurs (default 10 seconds)
	 * @return mvcViewMeta
	 * @static
	 */
	static function factoryMetaRedirect($inLocation, $inTimeout = 10) {
		$oObject = new self('redirect', self::META_TYPE_HTTP_EQUIV, self::META_NAME_REDIRECT, sprintf('%d:url=%s', $inTimeout, $inLocation));
		return $oObject;
	}
	
	/**
	 * Converts the object to a string
	 * 
	 * @return string
	 */
	function __toString() {
		if ( $this->getMetaType() == self::META_TYPE_HTTP_EQUIV ) {
			$return = '<meta http-equiv="'.$this->getMetaName().'" content="'.$this->getContent().'" />';
		} else {
			$return = '<meta name="'.$this->getMetaName().'" content="'.$this->getContent().'" />';
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
		$this->_MetaType = self::META_TYPE_NAME;
		$this->_MetaName = '';
		$this->_Content = '';
		$this->setModified(false);
	}

	/**
	 * Returns $_MetaType
	 *
	 * @return string
	 */
	function getMetaType() {
		return $this->_MetaType;
	}
	
	/**
	 * Set $_MetaType to $inMetaType
	 *
	 * @param string $inMetaType
	 * @return mvcViewMeta
	 */
	function setMetaType($inMetaType) {
		if ( $inMetaType !== $this->_MetaType ) {
			$this->_MetaType = $inMetaType;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_MetaName
	 *
	 * @return string
	 */
	function getMetaName() {
		return $this->_MetaName;
	}
	
	/**
	 * Set $_MetaName to $inMetaName
	 *
	 * @param string $inMetaName
	 * @return mvcViewMeta
	 */
	function setMetaName($inMetaName) {
		if ( $inMetaName !== $this->_MetaName ) {
			$this->_MetaName = $inMetaName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Content
	 *
	 * @return string
	 */
	function getContent() {
		return $this->_Content;
	}
	
	/**
	 * Set $_Content to $inContent
	 *
	 * @param string $inContent
	 * @return mvcViewMeta
	 */
	function setContent($inContent) {
		if ( $inContent !== $this->_Content ) {
			$this->_Content = $inContent;
			$this->setModified();
		}
		return $this;
	}
}