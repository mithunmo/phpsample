<?php
/**
 * movieAwardsModel.class.php
 * 
 * movieAwardsModel class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category movieAwardsModel
 * @version $Rev: 11 $
 */


/**
 * movieAwardsModel class
 * 
 * Provides the "movieAwards" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category movieAwardsModel
 */
class movieAwardsModel extends mofilmMovieAward implements mvcDaoModelInterface {
	
	/**
	 * Returns a list of objects, optionally from $inOffset for $inLimit
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 */
	function getObjectList($inOffset = null, $inLimit = 30, $inEventID = null, $inType = null) {
		$this->setEventID($inEventID);
		$this->setType($inType);
		
		return mofilmMovieAward::listOfObjects($inOffset, $inLimit, null, $inEventID, $inType);
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
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieAwards
			 WHERE 1';
		
		if ( $this->getEventID() > 0 ) {
			$query .= ' AND eventID = :EventID';
		}
		if ( $this->getType() ) {
			$query .= ' AND type = :Type';
		}
		
		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $this->getEventID() > 0 ) {
			$oStmt->bindValue(':EventID', $this->getEventID(), PDO::PARAM_INT);
		}
		if ( $this->getType() ) {
			$oStmt->bindValue(':Type', $this->getType());
		}
		$oStmt->execute();
		$res = $oStmt->fetch();
		$oStmt->closeCursor();
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
		return new mofilmMovieAward();
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
}