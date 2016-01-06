<?php
/**
 * feedReaderBase
 * 
 * Stored in feedReaderBase
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage feed
 * @category feedReaderBase
 * @version $Rev: 650 $
 */


/**
 * feedReaderBase
 * 
 * Abstract feed reader for parsing content feeds into {@link feedChannel} objects.
 * This class requires extending into the appropriate format.
 * 
 * @package scorpio
 * @subpackage feed
 * @category feedReaderBase
 */
abstract class feedReaderBase {
	
	/**
	 * Stores $_DomDocument
	 *
	 * @var DOMDocument
	 * @access protected
	 */
	protected $_DomDocument;
	
	/**
	 * Stores $_DomXpath
	 *
	 * @var DOMXPath
	 * @access protected
	 */
	protected $_DomXpath;
	
	
	
	/**
	 * Creates a new reader object
	 * 
	 * @param DOMDocument $inDom
	 * @return feedReaderBase
	 */
	function __construct(DOMDocument $inDom) {
		$this->reset();
		$this->setDomDocument($inDom);
		$this->setDomXpath(new DOMXPath($this->getDomDocument()));
	}
	
	/**
	 * Resets the object
	 * 
	 * @return void
	 */
	function reset() {
		$this->_DomDocument = null;
		$this->_DomXpath = null;
	}
	
	
	/**
	 * Parses the DOMDocument into a feedChannel object
	 * 
	 * @return feedChannel
	 * @abstract
	 */
	abstract function parse();
	
	
	
	/**
	 * Returns $_DomDocument
	 *
	 * @return DOMDocument
	 * @access public
	 */
	function getDomDocument() {
		return $this->_DomDocument;
	}
	
	/**
	 * Set $_DomDocument to $inDomDocument
	 *
	 * @param DOMDocument $inDomDocument
	 * @return feedReaderBase
	 * @access public
	 */
	function setDomDocument($inDomDocument) {
		if ( $this->_DomDocument !== $inDomDocument ) {
			$this->_DomDocument = $inDomDocument;
		}
		return $this;
	}
	
	/**
	 * Returns the root document element
	 * 
	 * @return DOMElement
	 */
	function getDocumentElement() {
		return $this->getDomDocument()->documentElement();
	}
	
	/**
	 * Returns the DOMDocument object as a SimpleXML object
	 * 
	 * @return SimpleXMLElement
	 */
	function getDomAsSimpleXml() {
		return simplexml_import_dom($this->getDomDocument());
	}

	/**
	 * Returns $_DomXpath
	 *
	 * @return DOMXPath
	 * @access public
	 */
	function getDomXpath() {
		return $this->_DomXpath;
	}
	
	/**
	 * Set $_DomXpath to $inDomXpath
	 *
	 * @param DOMXPath $inDomXpath
	 * @return feedReaderBase
	 * @access public
	 */
	function setDomXpath($inDomXpath) {
		if ( $this->_DomXpath !== $inDomXpath ) {
			$this->_DomXpath = $inDomXpath;
		}
		return $this;
	}
}