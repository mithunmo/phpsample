<?php
/**
 * usersModel.class.php
 * 
 * usersModel class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category usersModel
 * @version $Rev: 11 $
 */


/**
 * usersModel class
 * 
 * Provides the "users" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category usersModel
 */
class usersModel extends mvcModelBase {
	
	/**
	 * Stores $_CurrentUser
	 *
	 * @var mofilmUser
	 * @access protected
	 */
	protected $_CurrentUser;
	
	/**
	 * Stores an instance of mofilmUserSearch
	 * 
	 * @var mofilmUserSearch
	 * @access protected
	 */
	protected $_UserSearch;
	
	/**
	 * Stores the last run search result
	 * 
	 * @var mofilmUserSearchResult
	 * @access protected
	 */
	protected $_SearchResult;
	
	/**
	 * Stores an instance of mofilmUser
	 * 
	 * @var mofilmUser
	 * @access protected
	 */
	protected $_User;
	
	/**
	 * Stores $_UserMessage
	 *
	 * @var mofilmUserPrivateMessage
	 * @access protected
	 */
	protected $_UserMessage;
	
	/**
	 * Stores $_Stats
	 *
	 * @var mofilmUserStats
	 * @access protected
	 */
	protected $_Stats;
	
	/**
	 * Stores $_MessageParams
	 *
	 * @var array
	 * @access protected
	 */
	protected $_MessageParams;
	
	/**
	 * Stores $_MovieID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_MovieID;
	
	/**
	 * Stores $_Movie
	 *
	 * @var mofilmMovie
	 * @access protected
	 */
	protected $_Movie;
	
	/**
	 * Stores $_MessageTemplateID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_MessageTemplateID;
	
	/**
	 * Stores $_AppMessage
	 *
	 * @var commsApplicationMessage
	 * @access protected
	 */
	protected $_AppMessage;
	
	
	
	/**
	 * @see mvcModelBase::__construct()
	 */
	function __construct() {
		parent::__construct();
		
		$this->_Stats = null;
		$this->_MovieID = null;
		$this->_Movie = null;
		$this->_MessageParams = array();
		$this->_MessageTemplateID = null;
		$this->_AppMessage = null;
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
	 * @return usersModel
	 */
	function setCurrentUser($inCurrentUser) {
		if ( $inCurrentUser !== $this->_CurrentUser ) {
			$this->_CurrentUser = $inCurrentUser;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Creates an returns a mofilmUserSearch object
	 * 
	 * @return mofilmUserSearch
	 */
	function getUserSearch() {
		if ( !$this->_UserSearch instanceof mofilmUserSearch ) {
			$this->_UserSearch = new mofilmUserSearch();
			$this->_UserSearch->setLoadUserDetails(true);
			$this->_UserSearch->setOnlyActiveUsers(true);
		}
		return $this->_UserSearch;
	}
	
	/**
	 * Returns the search result object, or null if no search has been run
	 * 
	 * @return mofilmUserSearchResult
	 */
	function getSearchResult() {
		return $this->_SearchResult;
	}
	
	/**
	 * Runs the search with whatever parameters are in it
	 * 
	 * @return mofilmUserSearchResult
	 */
	function doSearch() {
		$this->_SearchResult = $this->getUserSearch()->search();
		return $this->_SearchResult;
	}
	
	/**
	 * Returns the limit needed to get to the last page of results
	 *
	 * @param integer $inLimit
	 * @return integer
	 */
	function getLastPageOffset($inLimit) {
		$total = $this->getSearchResult()->getTotalResults();
		
		if ( $inLimit > 0 ) {
			return $inLimit*floor($total/$inLimit);
		} else {
			return 0;
		}
	}

	/**
	 * Returns $_MessageParams
	 *
	 * @return array
	 */
	function getMessageParams() {
		return $this->_MessageParams;
	}
	
	/**
	 * Adds a parameter to be attached to any outbound messages
	 * 
	 * @param string $inParamName
	 * @param mixed $inValue
	 * @return usersModel
	 */
	function addMessageParam($inParamName, $inValue) {
		$this->_MessageParams[$inParamName] = $inValue;
		$this->setModified();
		return $this;
	}
	
	/**
	 * Set $_MessageParams to $inMessageParams
	 *
	 * @param array $inMessageParams
	 * @return usersModel
	 */
	function setMessageParams($inMessageParams) {
		if ( $inMessageParams !== $this->_MessageParams ) {
			$this->_MessageParams = $inMessageParams;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Loads and returns the user according to $inUserid, returns currently loaded user if $inUserID is null
	 * 
	 * Throws exception if user cannot be found, or no user loaded.
	 * 
	 * @param integer $inUserID
	 * @return mofilmUser
	 * @throws mvcModelException
	 */
	function getUser($inUserID = null) {
		if ( !$this->_User instanceof mofilmUser && $inUserID === null ) {
			throw new mvcModelException('No user loaded and no UserID supplied to load');
		}
		if ( $inUserID !== null && is_numeric($inUserID) ) {
			$oUserMan =  mofilmUserManager::getInstance();
			if ( $this->getCurrentUser()->getPermissions()->isRoot() || $this->getCurrentUser()->getClientID() == mofilmClient::MOFILM ) {
				$oUserMan->setLoadOnlyActive(false);
			}
			$oUser =$oUserMan->getUserByID($inUserID);
			if ( !$oUser instanceof mofilmUser || $oUser->getID() == 0 ) {
				$oUser = null;
				throw new mvcModelException("Supplied UserID ($inUserID) is not valid");
			}
			$this->_User = $oUser;
		}

		return $this->_User;
	}

	/**
	 * Returns $_UserMessage
	 *
	 * @return mofilmUserPrivateMessage
	 */
	function getUserMessage() {
		if ( !$this->_UserMessage instanceof mofilmUserPrivateMessage ) {
			$this->_UserMessage = new mofilmUserPrivateMessage();
		}
		return $this->_UserMessage;
	}
	
	/**
	 * Set $_UserMessage to $inUserMessage
	 *
	 * @param mofilmUserPrivateMessage $inUserMessage
	 * @return usersModel
	 */
	function setUserMessage($inUserMessage) {
		if ( $inUserMessage !== $this->_UserMessage ) {
			$this->_UserMessage = $inUserMessage;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Sends a private message to the user and a notification mail to the recipient
	 * 
	 * @return boolean
	 * @throws mvcModelException
	 */
	function sendMessage() {
		$this->getUserMessage()->save();
		
		if ( $this->getMovieID() ) {
			$oMsgLog = mofilmMovieMessageHistory::factoryFromPrivateMessage($this->getUserMessage(), $this->getMovieID());
			$oMsgLog->save();
		}
		
		if ( $this->getUser()->getParamSet()->getParam('PrivateMessageAlerts', 1) ) {
			if ( $this->getUser()->getTerritory()->getID() > 0 ) {
				$lang = $this->getUser()->getTerritory()->getLanguageSet()->getFirst()->getIso();
			} else {
				$lang = 'en';
			}
			
			
			$oQueue = commsOutboundManager::newQueueFromApplicationMessageGroup(
				0, mofilmMessages::MSG_GRP_CLIENT_PRIVATE_MESSAGE, $lang
			);
			
			if ( count($this->_MessageParams) > 0 ) {
				$this->addMessageParam(mofilmMessages::MSG_PARAM_PM_ID, $this->getUserMessage()->getMessageID());
				commsOutboundManager::mergeParamsInMessageStack($oQueue, $this->getMessageParams());
			}
			commsOutboundManager::setCustomerInMessageStack($oQueue, $this->getUser()->getID());
			commsOutboundManager::setRecipientInMessageStack($oQueue, $this->getUser()->getEmail());
			commsOutboundManager::replaceDataInMessageStack($oQueue, array(), array());
			if ( $oQueue->send() ) {
				return true;
			} else {
				throw new mvcModelException($oQueue->getLastException()->getMessage());
			}
		}
	}
	
	/**
	 * Returns a search of messages sent through the comms system to the current user
	 * 
	 * @return commsOutboundSearchResult
	 */
	function getMessageHistory($inOffset = 0, $inLimit = 20) {
		$oSearch = new commsOutboundSearch();
		$oSearch->setOrderBy(commsOutboundSearch::ORDERBY_DATE_SENT);
		$oSearch->setOrderDirection(commsOutboundSearch::ORDER_DESC);
		$oSearch->setRecipient($this->getUser()->getEmail());
		$oSearch->setOffset($inOffset);
		$oSearch->setLimit($inLimit);
		return $oSearch->search();
	}
	
	/**
	 * Returns an array of private messages for the current user
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 */
	function getPrivateMessageHistory($inOffset = 0, $inLimit = 20) {
		return mofilmUserPrivateMessageLog::listOfObjects($inOffset, $inLimit, $this->getUser()->getID());
	}
	
	/**
	 * Returns $_Stats
	 *
	 * @return mofilmUserStats
	 */
	function getStats() {
		if ( !$this->_Stats instanceof mofilmUserStats ) {
			$this->_Stats = new mofilmUserStats();
			$this->_Stats->load();
		}
		return $this->_Stats;
	}
	
	/**
	 * Returns the message template body text
	 * 
	 * @return string
	 */
	function getMessageTemplate() {
		$string = '';
		if ( $this->getMessageTemplateID() ) {
			$oAppMsg = commsApplicationMessage::getInstance($this->getMessageTemplateID());
			if ( $oAppMsg->getMessageID() > 0 ) {
				$string = $oAppMsg->getMessageBody();
				$string = trim(
					strip_tags(
						preg_replace(
							'/<\s*style.+?<\s*\/\s*style.*?>/si',
							'',
							str_replace(
								array('</p>', '<br />', '<br>'),
								array("</p>\r\n", "<br />\r\n", "<br>\r\n"),
								$string
							)
						)
					)
				);
				$string = preg_replace("/\s{2,}/i", "\n\n", $string);
			}
		}
		return $string;
	}
	
	
	
	/**
	 * Returns $_MovieID
	 *
	 * @return integer
	 */
	function getMovieID() {
		return $this->_MovieID;
	}
	
	/**
	 * Set $_MovieID to $inMovieID
	 *
	 * @param integer $inMovieID
	 * @return usersModel
	 */
	function setMovieID($inMovieID) {
		if ( $inMovieID !== $this->_MovieID ) {
			$this->_MovieID = $inMovieID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Movie
	 *
	 * @return mofilmMovie
	 */
	function getMovie() {
		if ( !$this->_Movie instanceof mofilmMovie ) {
			$this->_Movie = mofilmMovieManager::getInstanceByID($this->getMovieID());
		}
		return $this->_Movie;
	}
	
	/**
	 * Set $_Movie to $inMovie
	 *
	 * @param mofilmMovie $inMovie
	 * @return usersModel
	 */
	function setMovie($inMovie) {
		if ( $inMovie !== $this->_Movie ) {
			$this->_Movie = $inMovie;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_MessageTemplateID
	 *
	 * @return integer
	 */
	function getMessageTemplateID() {
		return $this->_MessageTemplateID;
	}
	
	/**
	 * Set $_MessageTemplateID to $inMessageTemplateID
	 *
	 * @param integer $inMessageTemplateID
	 * @return usersModel
	 */
	function setMessageTemplateID($inMessageTemplateID) {
		if ( $inMessageTemplateID !== $this->_MessageTemplateID ) {
			$this->_MessageTemplateID = $inMessageTemplateID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_AppMessage
	 *
	 * @return commsApplicationMessage
	 */
	function getAppMessage() {
		return $this->_AppMessage;
	}
	
	/**
	 * Set $_AppMessage to $inAppMessage
	 *
	 * @param commsApplicationMessage $inAppMessage
	 * @return usersModel
	 */
	function setAppMessage($inAppMessage) {
		if ( $inAppMessage !== $this->_AppMessage ) {
			$this->_AppMessage = $inAppMessage;
			$this->setModified();
		}
		return $this;
	}
}