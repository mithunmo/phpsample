<?php
/**
 * mvcViewJavascript.class.php
 * 
 * mvcViewJavascript class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewJavascript
 * @version $Rev: 707 $
 */


/**
 * mvcViewJavascript class
 * 
 * Represents an additional javascript resource to be used in the view for rendering.
 * This allows for external libraries to be loaded and rendered more easily.
 * Generates a valid XHTML script or script src link.
 * 
 * <code>
 * // add a separate JS file
 * $oJs = new mvcViewJavascript('myjs', mvcViewJavascript::TYPE_FILE, '/path/to/file.js');
 * 
 * // add some inline JS
 * $oJs = new mvcViewJavascript('myjs', mvcViewJavascript::TYPE_INLINE, 'var = document.getElementById();');
 * 
 * // add some js specifically with a different type
 * $oJs = new mvcViewJavascript(
 *     'myjson', mvcViewJavascript::TYPE_INLINE, '{ data: { property: 'value' } }', 'application/json'
 * );
 * </code>
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewJavascript
 */
class mvcViewJavascript extends mvcViewResource {
	
	/**
	 * Creates a new Javascript object
	 * 
	 * @param string $inIdentifier A unique identifier, will be generated if not set
	 * @param string $inType Wether this is a file or code block
	 * @param string $inResource Either the path or a javascript block
	 * @param string $inMimeType Mime-type, text/css
	 * @return mvcViewJavascript
	 */
	function __construct($inIdentifier = null, $inType = self::TYPE_FILE, $inResource = '', $inMimeType = 'text/javascript') {
		$this->reset();
		if ( $inIdentifier !== null ) {
			$this->setIdentifier($inIdentifier);
		}
		$this->setType($inType);
		$this->setResource($inResource);
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
			$return = '<script type="'.$this->getMimeType().'" src="'.$this->getResource().'"></script>';
		} else {
			$return = '<script type="'.$this->getMimeType().'">'."\n<!--//--><![CDATA[//><!--\n".$this->getResource()."\n//--><!]]>\n</script>";
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
		$this->_MimeType = 'text/javascript';
		$this->setModified(false);
	}
}