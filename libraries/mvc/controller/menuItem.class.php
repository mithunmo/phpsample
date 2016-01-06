<?php
/**
 * mvcControllerMenuItem.class.php
 * 
 * mvcControllerMenuItem class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcControllerMenuItem
 * @version $Rev: 650 $
 */


/**
 * mvcControllerMenuItem
 * 
 * Holds information about a menu command and any related commands it may have. This is used
 * to build a menu system dynamically. A menu item can contain related sub-commands that may
 * be ajax functions, full URI paths or partial URIs.
 * 
 * Menu Items are used to great effect in the mvcDaoController and related sub-controllers
 * that are in use for many of the baseAdminSite controllers.
 * 
 * Example:
 * <code>
 * $oItem = new mvcControllerMenuItem(self::ACTION_VIEW, 'View', self::ACTION_VIEW, 'View Messages');
 * $oItem->addItem(new mvcControllerMenuItem(self::ACTION_VIEW, 'Refresh', self::ACTION_VIEW, 'Refresh list'));
 * $oItem->addItem(new mvcControllerMenuItem(self::ACTION_NEW, 'New', 'newMessage', 'Create a new message'));
 * $oItem->addItem(new mvcControllerMenuItem('/controlPanel/pms/sentItems', 'Sent Items', 'sentMessages', 'View sent messages', false, mvcControllerMenuItem::PATH_TYPE_URI));
 * 
 * $oMenuItems = new mvcControllerMenuItems();
 * $oMenuItems->addItem($oItem);
 * </code>
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcControllerMenuItem
 */
class mvcControllerMenuItem extends baseSet {	
	
	/**
	 * Stores $_ActionImage
	 *
	 * @var string
	 * @access protected
	 */
	protected $_ActionImage;
	
	/**
	 * Stores $_UriPath
	 *
	 * @var string
	 * @access protected
	 */
	protected $_UriPath;
	
	/**
	 * Stores $_PathType
	 *
	 * @var string
	 * @access protected
	 */
	protected $_PathType;
	
	const PATH_TYPE_AJAX = 'ajax';
	const PATH_TYPE_URI = 'uri';
	
	/**
	 * Stores $_Label
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Label;
	
	/**
	 * Stores $_Description
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Description;
	
	/**
	 * Stores $_Validate
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Validate;
	
	/**
	 * Stores $_SendFormData
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_SendFormData;
	
	
	
	/**
	 * Returns new instance of mvcControllerMenuItem
	 *
	 * @param string $inUriPath
	 * @param string $inLabel
	 * @param string $inActionImage
	 * @param string $inDescription
	 * @param boolean $inValidate
	 * @param string $inPathType One of ajax or uri
	 * @param boolean $inSendFormData
	 * @return mvcControllerMenuItem
	 */
	function __construct($inUriPath, $inLabel, $inActionImage = '', $inDescription = '', $inValidate = false, $inPathType = self::PATH_TYPE_AJAX, $inSendFormData = false) {
		$this->reset();
		$this->setUriPath($inUriPath);
		$this->setPathType($inPathType);
		$this->setLabel($inLabel);
		$this->setActionImage($inActionImage);
		$this->setDescription($inDescription);
		$this->setValidate($inValidate);
		$this->setSendFormData($inSendFormData);
	}
	
	
	
	/**
	 * Returns $_ActionImage
	 *
	 * @return string
	 */
	function getActionImage() {
		return $this->_ActionImage;
	}
	
	/**
	 * Set $_ActionImage to $inAction
	 *
	 * @param string $inAction
	 * @return mvcControllerMenuItem
	 */
	function setActionImage($inAction) {
		if ( $inAction !== $this->_ActionImage ) {
			$this->_ActionImage = $inAction;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_UriPath
	 *
	 * @return string
	 */
	function getUriPath() {
		return $this->_UriPath;
	}
	
	/**
	 * Set $_UriPath to $inUriPath
	 *
	 * @param string $inUriPath
	 * @return mvcControllerMenuItem
	 */
	function setUriPath($inUriPath) {
		if ( $inUriPath !== $this->_UriPath ) {
			$this->_UriPath = $inUriPath;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_PathType
	 *
	 * @return string
	 */
	function getPathType() {
		return $this->_PathType;
	}
	
	/**
	 * Set $_PathType to $inPathType
	 *
	 * @param string $inPathType
	 * @return mvcControllerMenuItem
	 */
	function setPathType($inPathType) {
		if ( $inPathType !== $this->_PathType && in_array($inPathType, array(self::PATH_TYPE_AJAX, self::PATH_TYPE_URI)) ) {
			$this->_PathType = $inPathType;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_Label
	 *
	 * @return string
	 */
	function getLabel() {
		return $this->_Label;
	}
	
	/**
	 * Set $_Label to $inLabel
	 *
	 * @param string $inLabel
	 * @return mvcControllerMenuItem
	 */
	function setLabel($inLabel) {
		if ( $inLabel !== $this->_Label ) {
			$this->_Label = $inLabel;
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
	 * @return mvcControllerMenuItem
	 */
	function setDescription($inDescription) {
		if ( $inDescription !== $this->_Description ) {
			$this->_Description = $inDescription;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_Validate
	 *
	 * @return boolean
	 */
	function getValidate() {
		return $this->_Validate;
	}
	
	/**
	 * Set $_Validate to $inValidate
	 *
	 * @param boolean $inValidate
	 * @return mvcControllerMenuItem
	 */
	function setValidate($inValidate) {
		if ( $inValidate !== $this->_Validate ) {
			$this->_Validate = $inValidate;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_SendFormData
	 *
	 * @return boolean
	 */
	function getSendFormData() {
		return $this->_SendFormData;
	}
	
	/**
	 * Set $_SendFormData to $inSendFormData
	 *
	 * @param boolean $inSendFormData
	 * @return mvcControllerMenuItem
	 */
	function setSendFormData($inSendFormData) {
		if ( $inSendFormData !== $this->_SendFormData ) {
			$this->_SendFormData = $inSendFormData;
			$this->setModified();
		}
		return $this;
	}
	
	
	
	/**
	 * Resets object to defaults
	 *
	 * @return void
	 */
	function reset() {
		$this->_resetSet();
		
		$this->_ActionImage = null;
		$this->_UriPath = null;
		$this->_Label = null;
		$this->_Description = null;
		$this->_Validate = null;
		$this->_PathType = self::PATH_TYPE_AJAX;
		$this->_SendFormData = false;
	}
	
	/**
	 * Adds the menuItem to the list
	 *
	 * @param mvcControllerMenuItem $inItem
	 * @return mvcControllerMenuItem
	 */
	function addItem(mvcControllerMenuItem $inItem) {
		return $this->_setItem($inItem->getUriPath(), $inItem);
	}
	
	/**
	 * Returns the menuItem named $inItem
	 *
	 * @param string $inItem
	 * @return mvcControllerMenuItem
	 */
	function getItem($inItem = null) {
		return $this->_getItem($inItem);
	}
	
	/**
	 * Removes the action from allowed actions list
	 *
	 * @param mvcControllerMenuItem $inItem
	 * @return mvcControllerMenuItem
	 */
	function removeItem(mvcControllerMenuItem $inItem) {
		return $this->_removeItem($inItem->getUriPath());
	}
	
	/**
	 * Returns the number of actions that have been assigned
	 *
	 * @return integer
	 */
	function getItemCount() {
		return $this->_itemCount();
	}
}