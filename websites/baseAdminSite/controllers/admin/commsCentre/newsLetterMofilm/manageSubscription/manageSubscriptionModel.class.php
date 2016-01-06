<?php
/**
 * manageSubscriptionModel.class.php
 * 
 * manageSubscriptionModel class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category manageSubscriptionModel
 * @version $Rev: 624 $
 */


/**
 * manageSubscriptionModel class
 * 
 * Provides the "manageSubscription" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category manageSubscriptionModel
 */
class manageSubscriptionModel extends mofilmCommsSubscription implements mvcDaoModelInterface {

	/**
	 * Stores $_Keyword
	 *
	 * @var string $_Keyword
	 * @access protected
	 */
	protected $_Keyword;

	/**
	 * Stores $_Useremail
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Useremail;

	/**
	 * Stores $_UserID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_UserID;

	/**
	 * Stores $_SearchResult
	 *
	 * @var mofilmUserSearchResult
	 * @access protected
	 */
	protected $_SearchResult;

	/**
	 * Stores $_CurrentUser
	 *
	 * @var mofilmUser
	 * @access protected
	 */
	protected $_CurrentUser;



	/**
	 * Returns a list of objects, optionally from $inOffset for $inLimit
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 */
	function getObjectList($inList = null, $inOffset = null, $inLimit = 30) {
		return mofilmCommsSubscription::getEmailListByListID($inList,$inOffset, $inLimit);
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
	function getTotalObjects($inEmail = null ) {	    
		$query = 'SELECT COUNT(*) AS Count
				FROM '.system::getConfig()->getDatabase('mofilm_comms').'.subscriptions';
		$where = array();
		$where[] = ' listID = :listID ';
		$query .= ' WHERE ' . implode(' AND ', $where);
		$oRes = dbManager::getInstance()->prepare($query);
		$oRes->bindValue(':listID', $inEmail);
		$oRes->execute();
		return $oRes->fetchColumn();
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
		return new mofilmCommsSubscription();
	}
	
	/**
	 * Loads an existing object with $inPrimaryKey
	 *
	 * @param string $inPrimaryKey
	 * @return systemDaoInterface
	 */
	function getExistingObject($inPrimaryKey) {
		//$this->setID($inPrimaryKey);
		$this->setEmailID($inPrimaryKey);
		$this->load();
		return $this;
	}

	/**
	 * Gets the list name by ID
	 *
	 * @param integer $inListID
	 */
	function getListNameByID($inListID) {
		return mofilmCommsListType::getInstance($inListID)->getName();
	}

	/**
	 * Gets the Email address by EmailID
	 *
	 * @param integer $inEmailID
	 * @return string 
	 */
	function getEmailByEmailID($inEmailID) {
		return mofilmCommsEmail::getInstance($inEmailID)->getEmail();
	}

	/**
	 * Returns the Keyword search for email
	 * @return <type>
	 */
	function getKeyword() {
		return $this->_Keyword;
	}

	/**
	 * Sets the keyword for the email search
	 * @param string $inKeyword
	 * @return manageSubscriptionModel
	 */
	function setKeyword($inKeyword) {
		if ( $inKeyword !== $this->_Keyword ) {
			$this->_Keyword = $inKeyword;
			$this->setModified();
		}
	return $this;
	}

	/**
	 * Sets the user email address 
	 * @param string $inUserEmail
	 * @return manageSubscriptionModel
	 */
	function setUserEamil($inUserEmail) {
		if ( $inUserEmail !== $this->_Useremail ) {
			$this->_Useremail = $inUserEmail;
			$this->setModified();
		}
	return $this;
	}

	/**
	 * Returns the user email address
	 *
	 * @return string
	 */
	function getUserEmail() {
		return $this->_Useremail;
	}

	/**
	 * Sets the mofilmUser ID
	 *
	 * @param integer $inUserID
	 * @return manageSubscriptionModel
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
			$this->setModified();
		}
	return $this;
	}

	/**
	 * Returns the mofilmUser ID
	 * @return integer
	 */
	function getUserID() {
		return $this->_UserID;
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
	 * Adds a user email address to a subscription list 
	 *
	 * @return void
	 */
	function addUserToSubsList() {

		try {
			$oMofilmCommsEmail = mofilmCommsEmail::getInstanceByEmail($this->getUserEmail());
		} catch ( mofilmException $e ) {
			systemLog::error($e->getMessage());
		}
		
		if ( isset( $oMofilmCommsEmail ) && $oMofilmCommsEmail->getID() > 0 ) {

			$oExisMofilmCommsSub = mofilmCommsSubscription::getInstance($oMofilmCommsEmail->getID(),$this->getListID());
			if ( isset( $oExisMofilmCommsSub ) && $oExisMofilmCommsSub->getListID() == $this->getListID() ) {
				return false;
			} else {
				$oMofilmCommsSubs = new mofilmCommsSubscription();
				$oMofilmCommsSubs->setEmailID($oMofilmCommsEmail->getID());
				$oMofilmCommsSubs->setListID($this->getListID());
				$oMofilmCommsSubs->setHash(md5($oMofilmCommsEmail->getID() . ':' . $this->getUserEmail() .':'. $this->getListID()));
				$return = $oMofilmCommsSubs->save();
				return true;
			}
		} else {
			$oNewMofilmCommsEmail = new mofilmCommsEmail();
			$oNewMofilmCommsEmail->setEmail($this->getUserEmail());
			$oNewMofilmCommsEmail->setUserID($this->getUserID());
			$oNewMofilmCommsEmail->setHash(md5(date('U') . ':' . $this->getUserEmail()));
			$oNewMofilmCommsEmail->save();

			$oMofilmCommsSubs = new mofilmCommsSubscription();
			$oMofilmCommsSubs->setEmailID($oNewMofilmCommsEmail->getID());
			$oMofilmCommsSubs->setListID($this->getListID());
			$oMofilmCommsSubs->setHash(md5($oNewMofilmCommsEmail->getID() . ':' . $this->getUserEmail() .':'. $this->getListID()));
			$oMofilmCommsSubs->save();
			return true;
		}
	}

	/**
	 * Deletes the user email address from the subscription list 
	 * @return void
	 */
	function deleteUserToSubsList() {
		$oMofilmCommsEmail = mofilmCommsEmail::getInstanceByEmail($this->getUserEmail());
		if ( isset($oMofilmCommsEmail) && $oMofilmCommsEmail->getID() > 0 ) {
			$oMofilmCommsSubs = mofilmCommsSubscription::getInstanceByHash(md5($oMofilmCommsEmail->getID() . ':' . $oMofilmCommsEmail->getEmail() . ':' . $this->getListID()));
			$oMofilmCommsSubs->delete();
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Gets the list of users based on the search result 
	 *
	 * @return void
	 */
	function getUsersResult() {
		$oMofilmUserSearch = new mofilmUserSearch();
		$oMofilmUserSearch->setUser($this->getCurrentUser());
		$oMofilmUserSearch->setUserEmailAddress($this->getKeyword());
		$oMofilmUserSearch->setLimit(100);
		$this->setSearchResult($oMofilmUserSearch->search());
	}


}