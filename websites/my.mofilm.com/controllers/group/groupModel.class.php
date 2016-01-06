<?php
/**
 * groupModel.class.php
 * 
 * groupModel class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_my.mofilm.com
 * @subpackage controllers
 * @category groupModel
 * @version $Rev: 11 $
 */


/**
 * groupModel class
 * 
 * Provides the "group" page
 * 
 * @package websites_my.mofilm.com
 * @subpackage controllers
 * @category groupModel
 */
class groupModel extends mvcModelBase {
	
	/**
	 * Stores $_GroupName
	 *
	 * @var string
	 * @access protected
	 */
	protected $_GroupName;
	
	/**
	 * Stores $_Group
	 *
	 * @var mofilmUserGroup
	 * @access protected
	 */
	protected $_Group;
	
	
	
	/**
	 * @see mvcModelBase::__construct()
	 */
	function __construct() {
		$this->reset();
	}
	
	/**
	 * Reset the object
	 * 
	 * @return void
	 */
	function reset() {
		$this->_GroupName = null;
		$this->_Group = null;
		$this->setModified(false);
	}

	/**
	 * Returns $_GroupName
	 *
	 * @return string
	 */
	function getGroupName() {
		return $this->_GroupName;
	}
	
	/**
	 * Set $_GroupName to $inGroupName
	 *
	 * @param string $inGroupName
	 * @return groupModel
	 */
	function setGroupName($inGroupName) {
		if ( $inGroupName !== $this->_GroupName ) {
			$this->_GroupName = $inGroupName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Group
	 *
	 * @return mofilmUserGroup
	 */
	function getGroup() {
		return $this->_Group;
	}
	
	/**
	 * Set $_Group to $inGroup
	 *
	 * @param mofilmUserGroup $inGroup
	 * @return groupModel
	 */
	function setGroup($inGroup) {
		if ( $inGroup !== $this->_Group ) {
			$this->_Group = $inGroup;
			$this->setModified();
		}
		return $this;
	}
}