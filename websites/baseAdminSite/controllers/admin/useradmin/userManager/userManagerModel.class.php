<?php
/**
 * userManagerModel.class.php
 * 
 * userManagerModel.class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category userManagerModel
 * @version $Rev: 11 $
 */


/**
 * userManagerModel.class
 * 
 * Allows for editing users with access to the admin site.
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category userManagerModel
 */
class userManagerModel extends mofilmUser implements mvcDaoModelInterface {
	
	/**
	 * Stores $_CurrentUser
	 *
	 * @var mofilmUser
	 * @access protected
	 */
	protected $_CurrentUser;
	
	/**
	 * Stores $_SearchResult
	 *
	 * @var mofilmUserSearchResult
	 * @access protected
	 */
	protected $_SearchResult;
	
	/**
	 * Stores $_NewAdminUser
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_NewAdminUser = false;
	
	/**
	 * Stores $_Language
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Language = 'en';
	
	
	
	/**
	 * Returns $_CurrentUser
	 *
	 * @return mofilmUser
	 */
	function getCurrentUser() {
		return $this->_CurrentUser;
	}
	
	/**
	 * Set $_CurrentUser to $inCurrentUser
	 *
	 * @param mofilmUser $inCurrentUser
	 * @return userManagerModel
	 */
	function setCurrentUser($inCurrentUser) {
		if ( $inCurrentUser !== $this->_CurrentUser ) {
			$this->_CurrentUser = $inCurrentUser;
		}
		return $this;
	}

	/**
	 * Returns $_SearchResult
	 *
	 * @return mofilmUserSearchResult
	 */
	function getSearchResult() {
		return $this->_SearchResult;
	}
	
	/**
	 * Set $_SearchResult to $inSearchResult
	 *
	 * @param mofilmUserSearchResult $inSearchResult
	 * @return userManagerModel
	 */
	function setSearchResult($inSearchResult) {
		if ( $inSearchResult !== $this->_SearchResult ) {
			$this->_SearchResult = $inSearchResult;
			$this->setModified();
		}
		return $this;
	}
	
	
	
	/**
	 * Override default delete and only allow permissions to be removed
	 * 
	 * @return boolean
	 */
	function delete() {
		$return = false;
		if ( $this->getID() ) {
			$return = $this->getPermissions()->delete();
		}
		return $return;
	}
	
	/**
	 * Override save so that we can inject the messaging requirements
	 * 
	 * @return boolean
	 */
	function save() {
		$newUser = $msgGroup = false;
		if ( !$this->getID() ) {
			$newUser = true;
			systemLog::message('Forcing new user to require registration');
			$this->setEnabled('N');
			$this->setHash(mofilmUtilities::buildMiniHash($this, 10));
		}
		
		$return = parent::save();
		
		if ( $newUser ) {
			if ( $this->getPermissionGroup()->getID() >= 0 ) {
				$msgGroup = mofilmMessages::MSG_GRP_ADMIN_REGISTRATION;
			} else {
				$msgGroup = mofilmMessages::MSG_GRP_CLIENT_REGISTRATION;
			}
		} elseif ( $this->getNewAdminUser() ) {
			$msgGroup = mofilmMessages::MSG_GRP_ADMIN_PROMOTION;
		}
		
		if ( $msgGroup ) {
			if ( $this->getTerritory()->getID() > 0 ) {
				$lang = $this->getTerritory()->getLanguageSet()->getFirst()->getIso();
			} else {
				$lang = 'en';
			}
			
			systemLog::message('Sending message group: '.$msgGroup);
			$oQueue = commsOutboundManager::newQueueFromApplicationMessageGroup(
				0, $msgGroup, $lang
			);
			
			commsOutboundManager::setRecipientInMessageStack($oQueue, $this->getEmail());
			commsOutboundManager::replaceDataInMessageStack(
				$oQueue,
				array('%emailAddr%', '%regUrl%'),
				array($this->getEmail(), $this->getRegistrationUri())
			);
			
			$oQueue->send();
		}
		
		return $return;
	}
	
	
	
	/**
	 * Returns a list of objects, optionally from $inOffset for $inLimit
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param integer $inClientID
	 * @param string $inEmailAddress
	 * @return array
	 */
	function getObjectList($inOffset = null, $inLimit = 30, $inClientID = null, $inEmailAddress = null) {
		$oSearch = new mofilmUserSearch();
		$oSearch->setUser($this->getCurrentUser());
		$oSearch->setOnlyAdminUsers(true);
		if ( $this->getCurrentUser()->getClientID() == mofilmClient::MOFILM || $this->getCurrentUser()->getPermissions()->isRoot() ) {
			$oSearch->setOnlyActiveUsers(false);
		}
		if ( $inClientID !== null && is_numeric($inClientID) && $inClientID > 0 ) {
			$oSearch->setClientID($inClientID);
		}
		if ( $inEmailAddress !== null && $inEmailAddress != 'Search by email address' ) {
			$oSearch->setUserEmailAddress($inEmailAddress);
		}
		$oSearch->setOffset($inOffset);
		$oSearch->setLimit($inLimit);
		$oSearch->setOrderBy(mofilmUserSearch::ORDERBY_FULLNAME);
		$oSearch->setOrderDirection(mofilmUserSearch::ORDER_ASC);
		$this->setSearchResult($oSearch->search());
		return $this->getSearchResult();
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
		if ( $this->getSearchResult() instanceof mofilmUserSearchResult ) {
			return $this->getSearchResult()->getTotalResults();
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
		return new mofilmUser();
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
	 * Returns $_NewAdminUser
	 *
	 * @return boolean
	 */
	function getNewAdminUser() {
		return $this->_NewAdminUser;
	}
	
	/**
	 * Set $_NewAdminUser to $inNewAdminUser
	 *
	 * @param boolean $inNewAdminUser
	 * @return userManagerModel
	 */
	function setNewAdminUser($inNewAdminUser) {
		if ( $inNewAdminUser !== $this->_NewAdminUser ) {
			$this->_NewAdminUser = $inNewAdminUser;
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
	 * @return userManagerModel
	 */
	function setLanguage($inLanguage) {
		if ( $inLanguage !== $this->_Language ) {
			$this->_Language = $inLanguage;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Creates the confirmation URI
	 * 
	 * @return string
	 * @access private
	 */
	private function getRegistrationUri() {
		return mofilmConstants::getRegistrationUri().$this->getHash();
	}
}