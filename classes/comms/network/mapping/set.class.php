<?php
/**
 * commsNetworkMappingSet
 *
 * Stored in commsNetworkMappingSet.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage commsNetworkMappingSet
 * @category commsNetworkMappingSet
 * @version $Rev: 10 $
 */


/**
 * commsNetworkMappingSet Class
 *
 * Holds a set of network mappings.
 *
 * @package comms
 * @subpackage commsNetworkMappingSet
 * @category commsNetworkMappingSet
 */
class commsNetworkMappingSet extends baseSet implements systemDaoInterface {
	
	/**
	 * Stores $_NetworkID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_NetworkID;
	
	
	
	/**
	 * Creates a new message set
	 * 
	 * @param integer $inNetworkID
	 */
	function __construct($inNetworkID = null) {
		$this->reset();
		if ( $inNetworkID !== null ) {
			$this->setNetworkID($inNetworkID);
			$this->load();
		}
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
		if ( $this->getNetworkID() ) {
			$array = commsNetworkMapping::listOfObjects(null, null, $this->getNetworkID());
			foreach ( $array as $oObject ) {
				$this->addObject($oObject);
			}
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
		if ( $this->getNetworkID() ) {
			if ( $this->isModified() ) {
				if ( $this->getCount() > 0 ) {
					if ( false ) $oObject = new commsNetworkMapping();
					foreach ( $this as $oObject ) {
						if ( $oObject->getMarkForDeletion() ) {
							$this->removeObject($oObject);
							$oObject->delete();
						} else {
							$oObject->setGatewayID($this->getNetworkID());
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
		$this->_NetworkID = null;
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
	 * Returns $_NetworkID
	 *
	 * @return integer
	 */
	function getNetworkID() {
		return $this->_NetworkID;
	}
	
	/**
	 * Set $_NetworkID to $inNetworkID
	 *
	 * @param integer $inNetworkID
	 * @return commsNetworkMappingSet
	 */
	function setNetworkID($inNetworkID) {
		if ( $inNetworkID !== $this->_NetworkID ) {
			$this->_NetworkID = $inNetworkID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Add a message to the set
	 * 
	 * @param commsNetworkMapping $inObject
	 * @return commsNetworkMappingSet
	 */
	function addObject(commsNetworkMapping $inObject) {
		return $this->_setItem($inObject->getPrimaryKey(), $inObject);
	}
	
	/**
	 * Removes a message from the set
	 * 
	 * @param commsNetworkMapping $inObject
	 * @return commsNetworkMappingSet
	 */
	function removeObject(commsNetworkMapping $inObject) {
		return $this->_removeItem($inObject->getPrimaryKey());
	}
	
	/**
	 * Returns the object by array key $inKey
	 *
	 * @param string $inKey
	 * @return commsNetworkMapping
	 */
	function getObjectByKey($inKey) {
		return $this->_getItem($inKey);
	}
}