<?php
/**
 * userActivityLogModel.class.php
 * 
 * userActivityLogModel class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category userActivityLogModel
 * @version $Rev: 11 $
 */


/**
 * userActivityLogModel class
 * 
 * Provides the "userActivityLog" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category userActivityLogModel
 */
class userActivityLogModel extends mofilmUserLog implements mvcDaoModelInterface {
	
	/**
	 * Returns a list of objects, optionally from $inOffset for $inLimit
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param integer $inUserID
	 * @param string $inLogType
	 * @param string $inDescription
	 * @return array
	 */
	function getObjectList($inOffset = null, $inLimit = 30, $inUserID = null, $inLogType = null, $inDescription = null) {
		if ( strtolower($inDescription) == 'search by keyword' ) {
			$inDescription = null;
		}
		$this->setUserID($inUserID);
		$this->setType($inLogType);
		$this->setDescription($inDescription);
		
		return mofilmUserLog::listOfObjects($inOffset, $inLimit, $inUserID, $inLogType, $inDescription);
	}
	
	/**
	 * Returns the object primary key value
	 *
	 * @return string
	 */
	function getPrimaryKey() {
		return parent::getPrimaryKey();
	}
	
	/**
	 * Returns total object count for this table
	 *
	 * @return integer
	 */
	function getTotalObjects() {
		$query = '
			SELECT COUNT(*) AS Count
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userLog WHERE 1 ';
		
		if ( $this->getUserID() ) {
			$query .= ' AND userID = '.dbManager::getInstance()->quote($this->getUserID());
		}
		if ( $this->getType() && in_array($this->getType(), array(self::TYPE_LOGIN, self::TYPE_OTHER, self::TYPE_UPLOAD)) ) {
			$query .= ' AND type = '.dbManager::getInstance()->quote($this->getType());
		}
		if ( $this->getDescription() !== null && strlen($this->getDescription()) > 1 ) {
			$query .= ' AND description LIKE '.dbManager::getInstance()->quote('%'.str_replace(' ', '%', $this->getDescription()).'%');
		}
		
		$oRes = dbManager::getInstance()->query($query);
		$res = $oRes->fetch();
		if ( is_array($res) && count($res) > 0 ) {
			return $res['Count'];
		} else {
			return 0;
		}
	}
	
	/**
	 * Returns the limit needed to get to the last page of results
	 *
	 * @param integer $inLimit
	 * @return integer
	 */
	function getLastPageOffset($inLimit) {
		$total = $this->getTotalObjects();
		
		if ( $inLimit > 0 ) {
			return $inLimit*floor($total/$inLimit);
		} else {
			return 0;
		}
	}

	/**
	 * Returns a new blank object
	 *
	 * @return systemDaoInterface
	 */
	function getNewObject() {
		throw new mvcModelException('Log objects cannot be created manually.');
	}
	
	/**
	 * Loads an existing object with $inPrimaryKey
	 *
	 * @param string $inPrimaryKey
	 * @return systemDaoInterface
	 */
	function getExistingObject($inPrimaryKey) {
		$this->setID($inPrimaryKey);
		$this->load();
		return $this;
	}
	
	/**
	 * Override save
	 */
	function save() {
		throw new mvcModelException('Log objects cannot be saved.');
	}
	
	/**
	 * Override delete
	 */
	function delete() {
		throw new mvcModelException('Log objects cannot be removed.');
	}
}