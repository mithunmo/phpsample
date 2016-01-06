<?php
/**
 * mofilmUserSearch
 *
 * Stored in search.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmUserSearch
 * @category mofilmUserSearch
 * @version $Rev: 209 $
 */


/**
 * mofilmUserSearch Class
 *
 * The main user search system.
 *
 * @package mofilm
 * @subpackage mofilmUserSearch
 * @category mofilmUserSearch
 */
class mofilmUserSearch extends baseSearch {

	const ORDERBY_ID = 'ID';
	const ORDERBY_DATE = 'registered';
	const ORDERBY_EMAIL = 'email';
	const ORDERBY_FULLNAME = 'fullname';
	
	/**
	 * Stores $_User
	 *
	 * @var mofilmUser
	 * @access protected
	 */
	protected $_User;
	
	/**
	 * Stores $_UserID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_UserID;

	/**
	 * Stores $_UserEmailAddress
	 *
	 * @var string
	 * @access protected
	 */
	protected $_UserEmailAddress;
	
	/**
	 * Stores $_Enabled
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Enabled;
	
	/**
	 * Stores $_Firstname
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Firstname;
	
	/**
	 * Stores $_Surname
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Surname;
	
	/**
	 * Stores $_ClientID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_ClientID;
	
	/**
	 * Stores $_TerritoryID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_TerritoryID;
	
	/**
	 * Stores $_HasUploadedMovie
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_HasUploadedMovie;
	
	/**
	 * Stores $_OnlyFinalists
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_OnlyFinalists;
	
	/**
	 * Stores $_AdminUsers
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_OnlyAdminUsers;
	
	/**
	 * Stores $_OnlyActiveUsers
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_OnlyActiveUsers;
	
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
		$this->_UserID = null;
		$this->_UserEmailAddress = null;
		$this->_User = null;
		$this->_Enabled = null;
		$this->_Firstname = null;
		$this->_Surname = null;
		$this->_ClientID = null;
		$this->_TerritoryID = null;
		$this->_HasUploadedMovie = null;
		$this->_OnlyFinalists = null;
		$this->_OnlyAdminUsers = null;
		$this->_OnlyActiveUsers = true;
		$this->_LoadUserDetails = false;

		$this->_OrderBy = self::ORDERBY_DATE;
		$this->_AllowedOrderBy = array(self::ORDERBY_DATE, self::ORDERBY_EMAIL, self::ORDERBY_FULLNAME, self::ORDERBY_ID);
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
	 * @return mofilmUserSearchResult
	 */
	function search() {
		if ( $this->canSearchRun() ) {
			if ( $this->getUserID() ) {
				return new mofilmUserSearchResult(array(mofilmUserManager::getInstanceByID($this->getUserID())), 1, $this);
			}

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
					$oUserMan->setLoadOnlyActive($this->getOnlyActiveUsers());
					$oUserMan->setLoadUserDetails($this->getLoadUserDetails());
					$list = $oUserMan->loadUsersByArray($tmp);
				}
			}
			$oStmt->closeCursor();

			return new mofilmUserSearchResult($list, $count, $this);
		}
		/*
		 * Always return empty result set
		 */
		return new mofilmUserSearchResult(array(), 0, $this);
	}

	/**
	 * @see baseSearchInterface::canSearchRun()
	 */
	function canSearchRun() {
		$return = true;
		if ( !$this->getUser() instanceof mofilmUser ) {
			systemLog::warning('Missing mofilmUser object for search');
			$return = false;
		}
		if (
			!$this->getKeywords() && !$this->getUserID() && !$this->getUserEmailAddress() &&
			!$this->getEnabled() && !$this->getFirstname() && !$this->getSurname() &&
			!$this->getTerritoryID() && is_null($this->getHasUploadedMovie()) && !$this->getClientID() &&
			$this->getUser()->getClientID() != 1
		) {
			$return = false;
		}
		return $return;
	}

	/**
	 * @see baseSearchInterface::buildSelect()
	 */
	function buildSelect(&$inQuery) {
		$inQuery = 'SELECT SQL_CALC_FOUND_ROWS DISTINCT(users.ID) ';
		$inQuery .= ' FROM '.system::getConfig()->getDatabase('mofilm_content').'.users ';
		
		if ( $this->getHasUploadedMovie() !== null || $this->getOnlyFinalists() ) {
			$inQuery .= ' LEFT JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movies ON (users.ID = movies.userID) ';
		}
		if ( $this->getOnlyAdminUsers() ) {
			$inQuery .= ' INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.userPermissions ON (users.ID = userPermissions.userID) ';
		}
		if ( $this->getOnlyFinalists() ) {
			$inQuery .= ' INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movieAwards ON (movies.ID = movieAwards.movieID) ';
		}
	}

	/**
	 * @see baseSearchInterface::buildWhere()
	 */
	function buildWhere(&$inQuery) {
		$where = array();

		if ( $this->getUserID() > 0 ) {
			$where[] = 'userID = '.$this->getUserID();
		}
		
		if ( $this->getUserEmailAddress() && strlen($this->getUserEmailAddress()) > 2 ) {
			$where[] = 'users.email LIKE '.dbManager::getInstance()->quote('%'.str_replace(' ', '%', $this->getUserEmailAddress()).'%');
		}
		
		/*
		 * If non-mofilm only allow access to active users on the same client
		 */
		if ( $this->getUser()->getClientID() == mofilmClient::MOFILM || $this->getUser()->getPermissions()->isRoot() ) {
			if ( $this->getEnabled() == 'Y' || $this->getEnabled() == 'N' ) {
				$where[] = 'users.enabled = '.dbManager::getInstance()->quote($this->getEnabled());
			}
			if ( $this->getClientID() ) {
				$where[] = 'users.clientID = '.$this->getClientID();
			}
		} else {
			$where[] = 'users.enabled = "Y"';
			$where[] = 'users.clientID = '.$this->getUser()->getClientID();
		}
		
		if ( $this->getTerritoryID() ) {
			$where[] = 'users.territoryID = '.dbManager::getInstance()->quote($this->getTerritoryID());
		}
		
		if ( $this->getHasUploadedMovie() !== null || $this->getOnlyFinalists() ) {
			$where[] = 'movies.ID IS NOT NULL';
		}
		
		if ( !$this->getKeywords() && $this->getFirstname() ) {
			$where[] = 'users.firstname LIKE '.dbManager::getInstance()->quote('%'.str_replace(' ', '%', $this->getFirstname()).'%');
		}
		if ( !$this->getKeywords() && $this->getSurname() ) {
			$where[] = 'users.surname LIKE '.dbManager::getInstance()->quote('%'.str_replace(' ', '%', $this->getSurname()).'%');
		}
		
		if ( $this->getKeywords() && (!$this->getFirstname() && !$this->getSurname()) ) {
			switch ( $this->getSearchTextType() ) {
				case self::SEARCH_TEXT_LIKE:
				case self::SEARCH_TEXT_MATCH:
					$where[] = ' MATCH (users.firstname, users.surname) AGAINST ('.dbManager::getInstance()->quote($this->getKeywords()).')';					
				break;
				case self::SEARCH_TEXT_MATCH_BOOLEAN:
					$where[] = ' MATCH (users.firstname, users.surname) AGAINST ('.dbManager::getInstance()->quote($this->getKeywords()).' IN BOOLEAN MODE)';
				break;
				case self::SEARCH_TEXT_EXACT:
                                        $nameArray = explode(' ', trim($this->getKeywords()));
                                        if (count($nameArray) > 1) {
                                            $where[] = '  users.firstname LIKE '.dbManager::getInstance()->quote("%".$nameArray[0]."%" ).' AND users.surname LIKE '.dbManager::getInstance()->quote("%".$nameArray[1]."%" );
                                        }else{
                                            $where[] = '  MATCH (users.firstname, users.surname) AGAINST ('.dbManager::getInstance()->quote($this->getKeywords()).')';
                                            $where[] = '  users.firstname LIKE '.dbManager::getInstance()->quote('%'.str_replace(' ', '%', $this->getKeywords()).'%');
                                            $where[] = '  users.surname LIKE '.dbManager::getInstance()->quote('%'.str_replace(' ', '%', $this->getKeywords()).'%');										
                                        }    
                                break;	
			}
		}

		if ( count($where) > 0 ) {
			$join = $this->getWhereType() == self::WHERE_USING_OR ? ' OR ' : ' AND ';
			$inQuery .= ' WHERE '.implode($join, $where);
		}
		
		if ( $this->getHasUploadedMovie() !== null || $this->getOnlyFinalists() ) {
			$inQuery .= ' GROUP BY users.ID ';
		}
	}

	/**
	 * Adds the order by clause to the query
	 *
	 * @param string &$inQuery
	 */
	function buildOrderBy(&$inQuery) {
		$dir = $this->getOrderDirection() == self::ORDER_ASC ? 'ASC' : 'DESC';
		if ( !$this->getKeywords() && $this->getOrderBy() ) {
			if ( $this->getOrderBy() == self::ORDERBY_FULLNAME ) {
				$inQuery .= ' ORDER BY users.firstname '.$dir .', users.surname '.$dir;
			                                
                        } else {
				$inQuery .= ' ORDER BY users.'.$this->getOrderBy().' '.$dir;
			}
		}
                if ( $this->getKeywords() && $this->getOrderBy() ) {
			if ( $this->getOrderBy() == self::ORDERBY_FULLNAME ) {
				$inQuery .= ' ORDER BY users.firstname '.$dir .', users.surname '.$dir;
			                                
                        } else {
				$inQuery .= ' ORDER BY users.'.$this->getOrderBy().' '.$dir;
			}
		}
	}



	/**
	 * Return value of $_User
	 *
	 * @return mofilmUser
	 * @access public
	 */
	function getUser() {
		return $this->_User;
	}

	/**
	 * Set $_User to $inUser
	 *
	 * @param mofilmUser $inUser
	 * @return mofilmUserSearch
	 * @access public
	 */
	function setUser(mofilmUser $inUser) {
		if ( $inUser !== $this->_User ) {
			$this->_User = $inUser;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_UserID
	 *
	 * @return integer
	 * @access public
	 */
	function getUserID() {
		return $this->_UserID;
	}

	/**
	 * Set $_UserID to $inUserID
	 *
	 * @param integer $inUserID
	 * @return mofilmUserSearch
	 * @access public
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_UserEmailAddress
	 *
	 * @return string
	 * @access public
	 */
	function getUserEmailAddress() {
		return $this->_UserEmailAddress;
	}

	/**
	 * Set $_UserEmailAddress to $inUserEmailAddress
	 *
	 * @param string $inUserEmailAddress
	 * @return mofilmUserSearch
	 * @access public
	 */
	function setUserEmailAddress($inUserEmailAddress) {
		if ( $inUserEmailAddress !== $this->_UserEmailAddress ) {
			$this->_UserEmailAddress = $inUserEmailAddress;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Enabled
	 *
	 * @return string
	 */
	function getEnabled() {
		return $this->_Enabled;
	}
	
	/**
	 * Set $_Enabled to $inEnabled
	 *
	 * @param string $inEnabled
	 * @return mofilmUserSearch
	 */
	function setEnabled($inEnabled) {
		if ( $inEnabled !== $this->_Enabled ) {
			$this->_Enabled = $inEnabled;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Firstname
	 *
	 * @return string
	 */
	function getFirstname() {
		return $this->_Firstname;
	}
	
	/**
	 * Set $_Firstname to $inFirstname
	 *
	 * @param string $inFirstname
	 * @return mofilmUserSearch
	 */
	function setFirstname($inFirstname) {
		if ( $inFirstname !== $this->_Firstname ) {
			$this->_Firstname = $inFirstname;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Surname
	 *
	 * @return string
	 */
	function getSurname() {
		return $this->_Surname;
	}
	
	/**
	 * Set $_Surname to $inSurname
	 *
	 * @param string $inSurname
	 * @return mofilmUserSearch
	 */
	function setSurname($inSurname) {
		if ( $inSurname !== $this->_Surname ) {
			$this->_Surname = $inSurname;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_ClientID
	 *
	 * @return integer
	 */
	function getClientID() {
		return $this->_ClientID;
	}
	
	/**
	 * Set $_ClientID to $inClientID
	 *
	 * @param integer $inClientID
	 * @return mofilmUserSearch
	 */
	function setClientID($inClientID) {
		if ( $inClientID !== $this->_ClientID ) {
			$this->_ClientID = $inClientID;
			$this->setModified();
		}
		return $this;
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
	 * Returns $_HasUploadedMovie
	 *
	 * @return boolean
	 */
	function getHasUploadedMovie() {
		return $this->_HasUploadedMovie;
	}

	/**
	 * Returns $_OnlyFinalists
	 *
	 * @return boolean
	 */
	function getOnlyFinalists() {
		return $this->_OnlyFinalists;
	}
	
	/**
	 * Set $_OnlyFinalists to $inOnlyFinalists
	 *
	 * @param boolean $inOnlyFinalists
	 * @return mofilmUserSearch
	 */
	function setOnlyFinalists($inOnlyFinalists) {
		if ( $inOnlyFinalists !== $this->_OnlyFinalists ) {
			$this->_OnlyFinalists = $inOnlyFinalists;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Set $_HasUploadedMovie to $inHasUploadedMovie
	 *
	 * @param boolean $inHasUploadedMovie
	 * @return mofilmUserSearch
	 */
	function setHasUploadedMovie($inHasUploadedMovie) {
		if ( $inHasUploadedMovie !== $this->_HasUploadedMovie ) {
			$this->_HasUploadedMovie = $inHasUploadedMovie;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_AdminUsers
	 *
	 * @return boolean
	 */
	function getOnlyAdminUsers() {
		return $this->_OnlyAdminUsers;
	}
	
	/**
	 * Set $_AdminUsers to $inAdminUsers
	 *
	 * @param boolean $inAdminUsers
	 * @return mofilmUserSearch
	 */
	function setOnlyAdminUsers($inAdminUsers) {
		if ( $inAdminUsers !== $this->_OnlyAdminUsers ) {
			$this->_OnlyAdminUsers = $inAdminUsers;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_OnlyActiveUsers
	 *
	 * @return boolean
	 */
	function getOnlyActiveUsers() {
		return $this->_OnlyActiveUsers;
	}
	
	/**
	 * Set $_OnlyActiveUsers to $inOnlyActiveUsers
	 *
	 * @param boolean $inOnlyActiveUsers
	 * @return mofilmUserSearch
	 */
	function setOnlyActiveUsers($inOnlyActiveUsers) {
		if ( $inOnlyActiveUsers !== $this->_OnlyActiveUsers ) {
			$this->_OnlyActiveUsers = $inOnlyActiveUsers;
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