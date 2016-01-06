<?php
/**
 * commsApplicationMessageSet
 *
 * Stored in commsApplicationMessageSet.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage commsApplicationMessageSet
 * @category commsApplicationMessageSet
 * @version $Rev: 10 $
 */


/**
 * commsApplicationMessageSet Class
 *
 * Holds a set of messages for a particular message group.
 *
 * @package comms
 * @subpackage commsApplicationMessageSet
 * @category commsApplicationMessageSet
 */
class commsApplicationMessageSet extends baseSet implements systemDaoInterface {
	
	/**
	 * Stores $_ApplicationID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_ApplicationID;
	
	/**
	 * Stores $_MessageGroupID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_MessageGroupID;
	
	/**
	 * Stores $_Language
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Language;
	
	
	
	/**
	 * Creates a new message set
	 * 
	 * @param integer $inApplicationID
	 * @param integer $inMessageGroup
	 * @param string $inLanguage
	 */
	function __construct($inApplicationID = null, $inMessageGroupID = null, $inLanguage = null) {
		$this->reset();
		if ( $inApplicationID !== null ) {
			$this->setApplicationID($inApplicationID);
		}
		if ( $inMessageGroupID !== null ) {
			$this->setMessageGroupID($inMessageGroupID);
		}
		if ( $inLanguage !== null ) {
			$this->setLanguage($inLanguage);
		}
		$this->load();
	}
	
	
	
	/**
	 * Deletes the set and sub-objects
	 * 
	 * @return boolean
	 */
	public function delete() {
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				$oObject->delete();
			}
			$this->_setItem(array());
			return true;
		}
		return false;
	}

	/**
	 * Loads the set with objects
	 * 
	 * @return boolean
	 */
	public function load() {
		if ( $this->getApplicationID() !== null && $this->getMessageGroupID() ) {
			$this->_setItem(
				commsApplicationMessage::listOfObjects(
					null, null, $this->getApplicationID(), $this->getMessageGroupID(), $this->getLanguage()
				)
			);
			$this->setModified(false);
			return true;
		}
		return false;
	}

	/**
	 * Saves the object and any sub-objects
	 * 
	 * @return boolean
	 */
	public function save() {
		if ( $this->getMessageGroupID() ) {
			if ( $this->isModified() ) {
				if ( $this->getCount() > 0 ) {
					if ( false ) $oObject = new commsApplicationMessage();
					foreach ( $this as $oObject ) {
						if ( $oObject->getMarkForDeletion() ) {
							$this->removeMessage($oObject);
							$oObject->delete();
						} else {
							$oObject->setApplicationID($this->getApplicationID());
							$oObject->setMessageGroupID($this->getMessageGroupID());
							$oObject->save();
						}
					}
				}
				$this->setModified(false);
				return true;
			}
		}
		return false;
	}

	/**
	 * Returns the object as an array
	 * 
	 * @return array
	 */
	public function toArray() {
		return get_object_vars($this);
	}
	
	/**
	 * Resets the set
	 * 
	 * @return void
	 */
	function reset() {
		$this->_ApplicationID = null;
		$this->_MessageGroupID = null;
		$this->_Language = null;
		parent::_resetSet();
	}
	
	
	
	/**
	 * Returns true if object or sub-objects have been modified
	 * 
	 * @return boolean
	 */
	function isModified() {
		$modified = $this->_Modified;
		if ( !$modified && $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				$modified = $oObject->isModified() || $modified;
			}
		}
		return $modified;
	}
	
	/**
	 * Returns $_ApplicationID
	 *
	 * @return integer
	 */
	function getApplicationID() {
		return $this->_ApplicationID;
	}
	
	/**
	 * Set $_ApplicationID to $inApplicationID
	 *
	 * @param integer $inApplicationID
	 * @return commsApplicationMessageSet
	 */
	function setApplicationID($inApplicationID) {
		if ( $inApplicationID !== $this->_ApplicationID ) {
			$this->_ApplicationID = $inApplicationID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_MessageGroupID
	 *
	 * @return integer
	 */
	function getMessageGroupID() {
		return $this->_MessageGroupID;
	}
	
	/**
	 * Set $_MessageGroupID to $inMessageGroupID
	 *
	 * @param integer $inMessageGroupID
	 * @return commsApplicationMessageSet
	 */
	function setMessageGroupID($inMessageGroupID) {
		if ( $inMessageGroupID !== $this->_MessageGroupID ) {
			$this->_MessageGroupID = $inMessageGroupID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Language
	 *
	 * @return string
	 */
	function getLanguage() {
		return $this->_Language;
	}
	
	/**
	 * Set $_Language to $inLanguage
	 *
	 * @param string $inLanguage
	 * @return commsApplicationMessageSet
	 */
	function setLanguage($inLanguage) {
		if ( $inLanguage !== $this->_Language ) {
			$this->_Language = $inLanguage;
			$this->setModified();
		}
		return $this;
	}
	
	
	
	/**
	 * Add a message to the set
	 * 
	 * @param commsApplicationMessage $inMessage
	 * @return commsApplicationMessageSet
	 */
	function addMessage(commsApplicationMessage $inMessage) {
		return $this->_setValue($inMessage);
	}
	
	/**
	 * Removes a message from the set
	 * 
	 * @param commsApplicationMessage $inMessage
	 * @return commsApplicationMessageSet
	 */
	function removeMessage(commsApplicationMessage $inMessage) {
		return $this->_removeItemWithValue($inMessage);
	}
	
	/**
	 * Returns the message at array index $inKey, false if not found
	 * 
	 * @param $inKey
	 * @return commsApplicationMessage
	 */
	function getMessageByKey($inKey) {
		return $this->_getItem($inKey);
	}
	
	/**
	 * Returns the message object with ID $inMessageID, false if not found
	 * 
	 * @param integer $inMessageID
	 * @return commsApplicationMessage
	 */
	function getMessageByID($inMessageID) {
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				if ( $oObject->getMessageID() == $inMessageID ) {
					return $oObject;
				}
			}
		}
		return false;
	}
	
	/**
	 * Returns an array of messages in the group in language $inLanguage
	 * 
	 * @param string $inLanguage
	 * @return array
	 */
	function getMessagesByLanguage($inLanguage) {
		$return = array();
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				if ( $oObject->getLanguage() == $inLanguage ) {
					$return[] = $oObject;
				}
			}
		}
		return $return;
	}
}