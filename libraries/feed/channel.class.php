<?php
/**
 * feedChannel
 * 
 * Stored in feedChannel
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage feed
 * @category feedChannel
 * @version $Rev: 707 $
 */


/**
 * feedChannel
 * 
 * Stores the feed channel properties. If the channel is an RSS2 feed,
 * the factory method can be used with the parsed SimpleXMLElement.
 * 
 * <code>
 * // set properties
 * $oChannel = new feedChannel();
 * $oChannel->setTitle()->setDescription();
 * 
 * // set properties from array
 * $oChannel = new feedChannel();
 * $oChannel->loadFromArray($inArray);
 * 
 * // set properties from SimpleXML
 * $oChannel = feedChannel::factoryFromXml($inXML);
 * </code>
 * 
 * @package scorpio
 * @subpackage feed
 * @category feedChannel
 */
class feedChannel {
	
	/**
	 * Stores $_Modified
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified;
	
	/**
	 * Stores $_Title
	 *
	 * @var string
	 * @access protected
	 */ 
	protected $_Title;
	
	/**
	 * Stores $_Description
	 *
	 * @var string
	 * @access protected
	 */ 
	protected $_Description;
	
	/**
	 * Stores $_Language
	 *
	 * @var string
	 * @access protected
	 */ 
	protected $_Language;
	
	/**
	 * Stores $_Generator
	 *
	 * @var string
	 * @access protected
	 */ 
	protected $_Generator;
	
	/**
	 * Stores $_Ttl
	 *
	 * @var string
	 * @access protected
	 */ 
	protected $_Ttl;
	
	/**
	 * Stores $_Link
	 *
	 * @var string
	 * @access protected
	 */ 
	protected $_Link;
	
	/**
	 * Stores instance of feedItemSet
	 * 
	 * @var feedItemSet
	 * @access protected
	 */
	protected $_FeedItemSet;
	
	
	
	/**
	 * Returns a new feedChannel
	 * 
	 * @return feedChannel
	 */
	function __construct() {
		$this->reset();
	}
	
	/**
	 * Resets the object
	 * 
	 * @return void
	 */
	function reset() {
		$this->_Title = '';
		$this->_Description = '';
		$this->_Language = '';
		$this->_Generator = '';
		$this->_Ttl = 0;
		$this->_Link = '';
		$this->_FeedItemSet = null;
		$this->_Modified = false;
	}
	
	/**
	 * Loads properties from a simple xml element
	 * 
	 * @param SimpleXMLElement $inXML
	 * @return boolean
	 */
	function loadFromXml(SimpleXMLElement $inXML) {
		$this->setTitle(utilityXmlFunction::getValue($inXML, 'title', ''));
		$this->setDescription(utilityXmlFunction::getValue($inXML, 'description', ''));
		$this->setLanguage(utilityXmlFunction::getValue($inXML, 'language', ''));
		$this->setGenerator(utilityXmlFunction::getValue($inXML, 'generator', ''));
		$this->setTtl(utilityXmlFunction::getValue($inXML, 'ttl', ''));
		$this->setLink(utilityXmlFunction::getValue($inXML, 'link', ''));
		return true;
	}
	
	/**
	 * Loads properties from an associative array
	 * 
	 * @param array $inData
	 * @return boolean
	 */
	function loadFromArray(array $inData = array()) {
		$this->setTitle($inData['title']);
		$this->setDescription($inData['description']);
		$this->setLanguage($inData['language']);
		$this->setGenerator($inData['generator']);
		$this->setTtl($inData['ttl']);
		$this->setLink($inData['link']);
		return true;
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
	 * @return feedChannel
	 */
	function setModified($inStatus = true) {
		$this->_Modified = $inStatus;
		return $this;
	}
	
	/**
	 * Returns the feed title
	 * 
	 * @return string
	 */
	function getTitle() {
		return $this->_Title;
	}
	
	/**
	 * Sets the feed title
	 * 
	 * @param $inTitle
	 * @return feedChannel
	 */
	function setTitle($inTitle){
		if ($this->_Title !== $inTitle) {
			$this->_Title = $inTitle;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the feed description / summary
	 * 
	 * @return string
	 */
	function getDescription() {
		return $this->_Description;
	}
	
	/**
	 * Sets the feed description / summary
	 * 
	 * @param $inDescription
	 * @return feedChannel
	 */
	function setDescription($inDescription){
		if ($this->_Description !== $inDescription) {
			$this->_Description = $inDescription;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the feed language
	 * 
	 * @return string
	 */
	function getLanguage() {
		return $this->_Language;
	}
	
	/**
	 * Sets the feed language
	 * 
	 * @param $inLanguage
	 * @return feedChannel
	 */
	function setLanguage($inLanguage){
		if ($this->_Language !== $inLanguage) {
			$this->_Language = $inLanguage;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the feed generator program
	 * 
	 * @return string
	 */
	function getGenerator() {
		return $this->_Generator;
	}
	
	/**
	 * Sets the feed generator program name
	 * 
	 * @param $inGenerator
	 * @return feedChannel
	 */
	function setGenerator($inGenerator){
		if ($this->_Generator !== $inGenerator) {
			$this->_Generator = $inGenerator;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the feed refresh time
	 * 
	 * @return integer
	 */
	function getTtl() {
		return $this->_Ttl;
	}
	
	/**
	 * Sets the feed refresh time
	 * 
	 * @param $inTtl
	 * @return feedChannel
	 */
	function setTtl($inTtl){
		if ($this->_Ttl !== $inTtl) {
			$this->_Ttl = $inTtl;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the feed link
	 * 
	 * @return string
	 */
	function getLink() {
		return $this->_Link;
	}
	
	/**
	 * Set feed link
	 * 
	 * @param $inLink
	 * @return feedChannel
	 */
	function setLink($inLink){
		if ($this->_Link !== $inLink) {
			$this->_Link = $inLink;
			$this->setModified();
		}
		return $this;
	}
	
	
	
	/**
	 * Returns the feed item set
	 * 
	 * @return feedItemSet
	 */
	function getItemSet() {
		if ( !$this->_FeedItemSet instanceof feedItemSet ) {
			$this->_FeedItemSet = new feedItemSet();
		}
		return $this->_FeedItemSet;
	}
}