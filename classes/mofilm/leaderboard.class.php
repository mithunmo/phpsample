<?php
/**
 * mofilmLeaderboard
 * 
 * Stored in mofilmLeaderboard.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmLeaderboard
 * @category mofilmLeaderboard
 * @version $Rev: 41 $
 */


/**
 * mofilmLeaderboard Class
 * 
 * Handles building and returning the leaderboard for user points.
 * 
 * @package mofilm
 * @subpackage mofilmLeaderboard
 * @category mofilmLeaderboard
 */
class mofilmLeaderboard extends baseSearch {

	const ORDERBY_SCORE = 'userPoints.score';
	const ORDERBY_HIGHSCORE = 'userPoints.highScore';

	/**
	 * Stores $_TerritoryID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_TerritoryID;

	/**
	 * Stores $_AllTimeUsers
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_AllTimeUsers;

	/**
	 * Stores $_LoadUserDetails
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_LoadUserDetails;
	


	/**
	 * @see baseSearch::reset()
	 */
	function reset() {
		parent::reset();
		$this->_TerritoryID = null;
		$this->_AllTimeUsers = false;
		$this->_LoadUserDetails = true;

		$this->_OrderBy = self::ORDERBY_SCORE;
		$this->_AllowedOrderBy = array(self::ORDERBY_SCORE, self::ORDERBY_HIGHSCORE);
	}

	/**
	 * @see baseSearch::initialise()
	 */
	function initialise() {
		parent::initialise();
	}

	/**
	 * Runs the search using the supplied data
	 *
	 * @return mofilmLeaderboardResult
	 */
	function search() {
		if ( $this->canSearchRun() ) {
			$query = '';
			$this->buildSelect($query);
			$this->buildWhere($query);
			$this->buildOrderBy($query);
			$this->buildLimit($query);
			
			$count = 0;
			$list = array();
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				$tmp = array();
				foreach ( $oStmt as $row ) {
					$tmp[] = $row['ID'];
				}

				$count = dbManager::getInstance()->query('SELECT FOUND_ROWS() AS Results')->fetchColumn();
				if ( count($tmp) > 0 ) {
					$oUserMan = mofilmUserManager::getInstance();
					$oUserMan->setLoadOnlyActive(false);
					$oUserMan->setLoadUserDetails($this->getLoadUserDetails());
					$list = $oUserMan->loadUsersByArray($tmp);
				}
			}
			$oStmt->closeCursor();

			return new mofilmLeaderboardResult($list, $count, $this);
		}
		/*
		 * Always return empty result set
		 */
		return new mofilmLeaderboardResult(array(), 0, $this);
	}

	/**
	 * @see baseSearchInterface::canSearchRun()
	 */
	function canSearchRun() {
		$return = true;

		return $return;
	}

	/**
	 * @see baseSearchInterface::buildSelect()
	 */
	function buildSelect(&$inQuery) {
		$inQuery = '
			SELECT SQL_CALC_FOUND_ROWS userPoints.userID AS ID
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userPoints ';

		if ( $this->getTerritoryID() ) {
			$inQuery .= '
			       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.users ON (userPoints.userID = users.ID) ';
		}
		
		if ( $this->getKeywords() ) {
			$inQuery = 'SELECT SQL_CALC_FOUND_ROWS DISTINCT(users.ID) AS ID ';
			$inQuery .= ' FROM '.system::getConfig()->getDatabase('mofilm_content').'.users ';
			$inQuery .=" INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".userPoints ON (users.ID = userPoints.userID)";

		}
	}

	/**
	 * @see baseSearchInterface::buildWhere()
	 */
	function buildWhere(&$inQuery) {
		$where = array();

		if ( $this->getAllTimeUsers() ) {
			$where[] = 'userPoints.highScore > 0';
		} else {
			if ( !$this->getKeywords() ) {
				$where[] = 'userPoints.score > 400';
			}	
		}

		if ( $this->getTerritoryID() ) {
			$where[] = 'users.territoryID = '.dbManager::getInstance()->quote($this->getTerritoryID());
		}

		if ( count($where) > 0 ) {
			$join = $this->getWhereType() == self::WHERE_USING_OR ? ' OR ' : ' AND ';
			$inQuery .= ' WHERE '.implode($join, $where);
		}
		
		if ( $this->getKeywords() ) {
			//$where[] = ' users.email LIKE '.dbManager::getInstance()->quote('%'.str_replace(' ', '%', $this->getKeywords()).'%');
			$where[] = ' ( MATCH (users.firstname, users.surname) AGAINST ('.dbManager::getInstance()->quote($this->getKeywords()).')';
			$where[] = ' OR users.firstname LIKE '.dbManager::getInstance()->quote('%'.str_replace(' ', '%', $this->getKeywords()).'%');
			$where[] = ' OR users.surname LIKE '.dbManager::getInstance()->quote('%'.str_replace(' ', '%', $this->getKeywords()).'%');
			$where[] = ' ) AND userPoints.score >= 25';
			$inQuery .= ' WHERE '.implode($join, $where);
		}
		
		
	}

	/**
	 * Adds the order by clause to the query
	 *
	 * @param string &$inQuery
	 */
	function buildOrderBy(&$inQuery) {
		$dir = $this->getOrderDirection() == self::ORDER_ASC ? 'ASC' : 'DESC';

		if ( $this->getAllTimeUsers() ) {
			$this->setOrderBy(self::ORDERBY_HIGHSCORE);
		}
		
		if ( !$this->getKeywords() ) {
			$inQuery .= ' ORDER BY '.$this->getOrderBy().' '.$dir;
		}
	}



	/**
	 * Returns $_TerritoryID
	 *
	 * @return integer
	 */
	function getTerritoryID() {
		return $this->_TerritoryID;
	}

	/**
	 * Set $_TerritoryID to $inTerritoryID
	 *
	 * @param integer $inTerritoryID
	 * @return mofilmUserSearch
	 */
	function setTerritoryID($inTerritoryID) {
		if ( $inTerritoryID !== $this->_TerritoryID ) {
			$this->_TerritoryID = $inTerritoryID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the value of $_AllTimeUsers
	 *
	 * @return boolean
	 */
	function getAllTimeUsers() {
		return $this->_AllTimeUsers;
	}

	/**
	 * Set $_AllTimeUsers to $inAllTimeUsers
	 *
	 * @param boolean $inAllTimeUsers
	 * @return mofilmLeaderboard
	 */
	function setAllTimeUsers($inAllTimeUsers) {
		if ( $inAllTimeUsers !== $this->_AllTimeUsers ) {
			$this->_AllTimeUsers = $inAllTimeUsers;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_LoadUserDetails
	 *
	 * @return boolean
	 */
	function getLoadUserDetails() {
		return $this->_LoadUserDetails;
	}
		
	/**
	 * Set $_LoadUserDetails to $inLoadUserDetails
	 *
	 * @param boolean $inLoadUserDetails
	 * @return mofilmUserSearch
	 */
	function setLoadUserDetails($inLoadUserDetails) {
		if ( $inLoadUserDetails !== $this->_LoadUserDetails ) {
			$this->_LoadUserDetails = $inLoadUserDetails;
			$this->setModified();
}
		return $this;
	}
}
