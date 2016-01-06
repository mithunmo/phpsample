<?php
/**
 * feedItem
 * 
 * Stored in feedItem
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage feed
 * @category feedItem
 * @version $Rev: 707 $
 */


/**
 * feedItem
 * 
 * Stores an item from an RSS feed. If the feed item is from an RSS2 feed
 * then the parsed XML item can be used with the factory method.
 * 
 * <code>
 * // set properties
 * $oItem = new feedItem();
 * $oItem->setTitle()->setDescription();
 * 
 * // set from a parsed array
 * $oItem = new feedItem();
 * $oItem->loadFromArray($array);
 * 
 * // set from a SimpleXMLElement from an RSS2 feed
 * $oItem feedItem::factoryFromXml($inXML);
 * </code>
 * 
 * @package scorpio
 * @subpackage feed
 * @category feedItem
 */
class feedItem {
	
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
	 * Stores $_Link
	 *
	 * @var string
	 * @access protected
	 */ 
	protected $_Link;
	
	/**
	 * Stores $_Guid
	 *
	 * @var string
	 * @access protected
	 */ 
	protected $_Guid;
	
	/**
	 * Stores $_PublishDate
	 *
	 * @var string
	 * @access protected
	 */ 
	protected $_PublishDate;
	
	
	
	/**
	 * Creates a new feedItem
	 * 
	 * @return feedItem
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
		$this->_Link = '';
		$this->_Guid = '';
		$this->_PublishDate = '';
		$this->_Modified = false;
	}
	
	/**
	 * Creates a new instance of feedItem from the $inXml fragment
	 * 
	 * @param SimpleXMLElement $inXml
	 * @return feedItem
	 * @static
	 */
	static function factoryFromXml(SimpleXMLElement $inXml) {
		$oObject = new feedItem();
		$oObject->loadFromXml($inXml);
		return $oObject;
	}
	
	/**
	 * Load properties from a SimpleXML fragment
	 * 
	 * <code>
	 * // rss item xml
	 * <item>
	 *     <title></title>
	 *     ...
	 *     <pubDate></pubDate>
	 * </item>
	 * </code>
	 * 
	 * @param SimpleXMLElement $inXml
	 * @return boolean
	 */
	function loadFromXml(SimpleXMLElement $inXml) {
		$this->setTitle(utilityXmlFunction::getValue($inXml, 'title', ''));
		$this->setDescription(utilityXmlFunction::getValue($inXml, 'description', ''));
		$this->setLink(utilityXmlFunction::getValue($inXml, 'link', ''));
		$this->setGuid(utilityXmlFunction::getValue($inXml, 'guid', ''));
		$this->setPublishDate(utilityXmlFunction::getValue($inXml, 'pubDate', date('D, d M Y H:i:s \G\M\T')));
		return true;
	}
	
	/**
	 * Loads properties from an associative array
	 * 
	 * @param $inData
	 * @return boolean
	 */
	function loadFromArray(array $inData = array()) {
		$this->setTitle($inData['title']);
		$this->setDescription($inData['description']);
		$this->setLink($inData['link']);
		$this->setGuid($inData['guid']);
		$this->setPublishDate($inData['pubDate']);
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
	 * @return feedItem
	 */
	function setModified($inStatus = true) {
		$this->_Modified = $inStatus;
		return $this;
	}
	
	/**
	 * Returns the title
	 * 
	 * @return string
	 */
	function getTitle() {
		return $this->_Title;
	}
	
	/**
	 * Set title to $inTitle
	 * 
	 * @param $inTitle
	 * @return feedItem
	 */
	function setTitle($inTitle){
		if ($this->_Title !== $inTitle) {
			$this->_Title = $inTitle;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the link description / summary
	 * 
	 * @return string
	 */
	function getDescription() {
		return $this->_Description;
	}
	
	/**
	 * Set the link description / summary
	 * 
	 * @param $inDescription
	 * @return feedItem
	 */
	function setDescription($inDescription){
		if ($this->_Description !== $inDescription) {
			$this->_Description = $inDescription;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return the link
	 * 
	 * @return string
	 */
	function getLink() {
		return $this->_Link;
	}
	
	/**
	 * Set the item link
	 * 
	 * @param $inLink
	 * @return feedItem
	 */
	function setLink($inLink){
		if ($this->_Link !== $inLink) {
			$this->_Link = $inLink;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return the globally unique ID
	 * 
	 * @return string
	 */
	function getGuid() {
		return $this->_Guid;
	}
	
	/**
	 * Set the globally unique ID
	 * 
	 * @param $inGuid
	 * @return feedItem
	 */
	function setGuid($inGuid){
		if ($this->_Guid !== $inGuid) {
			$this->_Guid = $inGuid;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return the publish date
	 * 
	 * @return datetime
	 */
	function getPublishDate() {
		return $this->_PublishDate;
	}
	
	/**
	 * Set publish date
	 * 
	 * @param $inPublishDate
	 * @return feedItem
	 */
	function setPublishDate($inPublishDate){
		if ($this->_PublishDate !== $inPublishDate) {
			$this->_PublishDate = $inPublishDate;
			$this->setModified();
		}
		return $this;
	}
}