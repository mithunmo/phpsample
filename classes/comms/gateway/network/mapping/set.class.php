<?php
/**
 * commsGatewayNetworkMappingSet
 *
 * Stored in commsGatewayNetworkMappingSet.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage commsGatewayNetworkMappingSet
 * @category commsGatewayNetworkMappingSet
 * @version $Rev: 10 $
 */


/**
 * commsGatewayNetworkMappingSet Class
 *
 * Holds a set of gateway mappings.
 *
 * @package comms
 * @subpackage commsGatewayNetworkMappingSet
 * @category commsGatewayNetworkMappingSet
 */
class commsGatewayNetworkMappingSet extends baseSet implements systemDaoInterface {
	
	/**
	 * Stores $_GatewayID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_GatewayID;
	
	
	
	/**
	 * Creates a new message set
	 * 
	 * @param integer $inGatewayID
	 */
	function __construct($inGatewayID = null) {
		$this->reset();
		if ( $inGatewayID !== null ) {
			$this->setGatewayID($inGatewayID);
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
		if ( $this->getGatewayID() ) {
			$array = commsGatewayNetworkMapping::listOfObjects(null, null, $this->getGatewayID());
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
		if ( $this->getGatewayID() ) {
			if ( $this->isModified() ) {
				if ( $this->getCount() > 0 ) {
					if ( false ) $oObject = new commsGatewayNetworkMapping();
					foreach ( $this as $oObject ) {
						if ( $oObject->getMarkForDeletion() ) {
							$this->removeObject($oObject);
							$oObject->delete();
						} else {
							$oObject->setGatewayID($this->getGatewayID());
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
		$this->_GatewayID = null;
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
	 * Returns $_GatewayID
	 *
	 * @return integer
	 */
	function getGatewayID() {
		return $this->_GatewayID;
	}
	
	/**
	 * Set $_GatewayID to $inGatewayID
	 *
	 * @param integer $inGatewayID
	 * @return commsGatewayNetworkMappingSet
	 */
	function setGatewayID($inGatewayID) {
		if ( $inGatewayID !== $this->_GatewayID ) {
			$this->_GatewayID = $inGatewayID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Add a message to the set
	 * 
	 * @param commsGatewayNetworkMapping $inObject
	 * @return commsGatewayNetworkMappingSet
	 */
	function addObject(commsGatewayNetworkMapping $inObject) {
		return $this->_setItem($inObject->getGatewayRef(), $inObject);
	}
	
	/**
	 * Removes a message from the set
	 * 
	 * @param commsGatewayNetworkMapping $inObject
	 * @return commsGatewayNetworkMappingSet
	 */
	function removeObject(commsGatewayNetworkMapping $inObject) {
		return $this->_removeItem($inObject->getGatewayRef());
	}
	
	/**
	 * Returns the message at array index $inKey, false if not found
	 * 
	 * @param $inKey
	 * @return commsGatewayNetworkMapping
	 */
	function getObjectByGatewayRef($inKey) {
		return $this->_getItem($inKey);
	}

	/**
	 * Returns the mapping matching $inGatewayRef
	 *
	 * @param string $inGatewayRef
	 * @return commsGatewayNetworkMapping
	 */
	function getNetworkIdFromGatewayRef($inGatewayRef) {
		return $this->getObjectByGatewayRef($inGatewayRef);
	}
	
	/**
	 * Returns the mapping matching the networkID
	 *
	 * @param integer $inNetworkID
	 * @return commsGatewayNetworkMapping
	 */
	function getGatewayRefFromNetworkId($inNetworkID) {
		if ( is_numeric($inNetworkID) && $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				if ( $oObject->getNetworkID() == $inNetworkID ) {
					return $oObject;
				}
			}
		}
		return false;
	}
}