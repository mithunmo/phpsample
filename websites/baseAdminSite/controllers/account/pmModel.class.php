<?php
/**
 * pmModel.class.php
 * 
 * pmModel class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category pmModel
 * @version $Rev: 11 $
 */


/**
 * pmModel class
 * 
 * Provides the "account" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category pmModel
 */
class pmModel extends mvcModelBase {
	
	/**
	 * Stores $_User
	 *
	 * @var mofilmUser
	 * @access protected
	 */
	protected $_User;
	
	/**
	 * Stores $_Language
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Language;
	
	/**
	 * Stores $_Offset
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_Offset;
	
	/**
	 * Stores $_Limit
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_Limit;
	
	/**
	 * Stores $_Message
	 *
	 * @var mofilmUserPrivateMessage
	 * @access protected
	 */
	protected $_Message;
	
	/**
	 * Stores $_MessageType
	 *
	 * @var string
	 * @access protected
	 */
	protected $_MessageType;
	
	
	
	/**
	 * Creates a new account model object
	 */
	function __construct() {
		$this->reset();
	}

	/**
	 * Resets the model
	 *
	 * @return void
	 */
	function reset() {
		$this->_User = null;
		$this->_Message = null;
		$this->_Language = 'en';
		$this->_MessageType = 'inbox';
		$this->_Offset = 0;
		$this->_Limit = 30;
		$this->setModified(false);
	}
	
	/**
	 * Returns the users messages if any
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 */
	function getInboxMessages($inOffset = 0, $inLimit = 30) {
		return mofilmUserPrivateMessage::listOfObjects($inOffset, $inLimit, $this->getUser()->getID());
	}
	
	/**
	 * Returns the users sent items if any
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 */
	function getSentMessages($inOffset = 0, $inLimit = 30) {
		return mofilmUserPrivateMessageLog::listOfObjects($inOffset, $inLimit, $this->getUser()->getID());
	}
	
	/**
	 * Returns the total number of messages for the user
	 * 
	 * @return integer
	 */
	function getTotalInboxMessages() {
		return mofilmUserPrivateMessage::getMessageCount($this->getUser()->getID());
	}
	
	/**
	 * Returns the total number of sent items for the user
	 * 
	 * @return integer
	 */
	function getTotalSentMessages() {
		return mofilmUserPrivateMessageLog::getMessageCount($this->getUser()->getID());
	}
	
	/**
	 * Returns the limit needed to get to the last page of results
	 *
	 * @param integer $inLimit
	 * @return integer
	 */
	function getLastPageOffset($inLimit) {
		if ( $this->getMessageType() == 'sent' ) {
			$total = $this->getTotalSentMessages();
		} else {
			$total = $this->getTotalInboxMessages();
		}
		
		if ( $inLimit > 0 ) {
			return $inLimit*floor($total/$inLimit);
		} else {
			return 0;
		}
	}
	
	/**
	 * Returns the inbox message from $inMessageID checking that the current user
	 * can access the message, returns null if not found or not allowed
	 * 
	 * @param integer $inMessageID
	 * @return mofilmUserPrivateMessage
	 */
	function fetchInboxMessage($inMessageID) {
		$oMessage = mofilmUserPrivateMessage::getInstance($inMessageID);
		if ( $oMessage->getMessageID() > 0 && $oMessage->getToUserID() == $this->getUser()->getID() ) {
			$this->setMessage($oMessage);
			return $oMessage;
		} else {
			return null;
		}
	}

	/**
	 * Returns the sent message from $inMessageID checking that the current user
	 * can access the message, returns null if not found or not allowed
	 * 
	 * @param integer $inMessageID
	 * @return mofilmUserPrivateMessageLog
	 */
	function fetchSentMessage($inMessageID) {
		$oMessage = mofilmUserPrivateMessageLog::getInstance($inMessageID);
		if ( $oMessage->getMessageID() > 0 && $oMessage->getFromUserID() == $this->getUser()->getID() ) {
			$this->setMessage($oMessage);
			return $oMessage;
		} else {
			return null;
		}
	}
	
	/**
	 * Handles checking and sending the private message, returns true or false
	 * 
	 * @param array $data Filtered message data
	 * @return boolean
	 * @throws mvcModelException
	 */
	function sendMessage(array $data) {
		if ( !isset($data['Recipient']) || !is_array($data['Recipient']) || count($data['Recipient']) < 1 ) {
			throw new mvcModelException('No recipient was set, please ensure at least 1 (one) recipient is set.');
		}
		
		if ( strlen(trim($data['Subject'])) == 0 ) {
			$data['Subject'] = 'Re:';
		}
		
		if ( isset($data['MessageAction']) && $data['MessageAction'] == 'reply' ) {
			$oOrigMessage = mofilmUserPrivateMessage::getInstance($data['MessageID']);
			if ( $oOrigMessage->getMessageID() > 0 && $oOrigMessage->getToUserID() == $this->getUser()->getID() ) {
				$oOrigMessage->setStatus(mofilmUserPrivateMessage::STATUS_REPLIED);
				$oOrigMessage->save();
			}
			if ( $oOrigMessage->getFromUserID() > 0 && $data['Recipient'][0] != $oOrigMessage->getFromUserID() ) {
				$data['Recipient'][0] = $oOrigMessage->getFromUserID();
			}
		}
		
		$oMessage = new mofilmUserPrivateMessage();
		$oMessage->setFromUserID($this->getUser()->getID());
		$oMessage->setMessage($data['Message']);
		$oMessage->setStatus(mofilmUserPrivateMessage::STATUS_NEW);
		$oMessage->setSubject($data['Subject']);
		$oMessage->setToUserID($data['Recipient'][0]);
		
		try {
			systemLog::message('Sending private message to '.$data['Recipient'][0]);
			$oMessage->save();
			
			if ( array_key_exists('MovieID', $data) && $data['MovieID'] > 0 ) {
				$oMsgLog = mofilmMovieMessageHistory::factoryFromPrivateMessage($oMessage, $data['MovieID']);
				$oMsgLog->save();
			}
			
			try {
				$this->sendMessageNotification($oMessage);
			} catch ( Exception $e ) {
				systemLog::notice($e->getMessage());
			}
			
			return true;
			
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			throw new mvcModelException($e->getMessage());
		}
		return false;
	}
	
	/**
	 * Sends a notification mail to the private message recipient
	 * 
	 * @param mofilmUserPrivateMessage $inMessage
	 * @return boolean
	 * @throws mvcModelException
	 */
	function sendMessageNotification(mofilmUserPrivateMessage $inMessage) {
		if ( $inMessage->getRecipient()->getParamSet()->getParam('PrivateMessageAlerts', 1) ) {
			$lang = $inMessage->getRecipient()->getParamSet()->getParam('Language');
			
			if ( !$lang ) {
				if ( $inMessage->getRecipient()->getTerritory()->getID() > 0 ) {
					$lang = $inMessage->getRecipient()->getTerritory()->getLanguageSet()->getFirst()->getIso();
				} else {
					$lang = 'en';
				}
			}
			
			$oQueue = commsOutboundManager::newQueueFromApplicationMessageGroup(
				0, mofilmMessages::MSG_GRP_CLIENT_PRIVATE_MESSAGE, $lang
			);
			commsOutboundManager::setCustomerInMessageStack($oQueue, $inMessage->getRecipient()->getID());
			commsOutboundManager::setRecipientInMessageStack($oQueue, $inMessage->getRecipient()->getEmail());
			commsOutboundManager::replaceDataInMessageStack($oQueue, array(), array());
			if ( $oQueue->send() ) {
				return true;
			} else {
				throw new mvcModelException($oQueue->getLastException()->getMessage());
			}
		}
	}
	
	
	
	/**
	 * Returns $_User
	 *
	 * @return mofilmUser
	 */
	function getUser() {
		return $this->_User;
	}

	/**
	 * Set $_User to $inUser
	 *
	 * @param mofilmUser $inUser
	 * @return pmModel
	 */
	function setUser(mofilmUser $inUser) {
		if ( $inUser !== $this->_User ) {
			$this->_User = $inUser;
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
	 * @return pmModel
	 */
	function setLanguage($inLanguage) {
		if ( $inLanguage !== $this->_Language ) {
			$this->_Language = $inLanguage;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Offset
	 *
	 * @return integer
	 */
	function getOffset() {
		return $this->_Offset;
	}
	
	/**
	 * Set $_Offset to $inOffset
	 *
	 * @param integer $inOffset
	 * @return pmModel
	 */
	function setOffset($inOffset) {
		if ( $inOffset !== $this->_Offset ) {
			$this->_Offset = $inOffset;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Limit
	 *
	 * @return integer
	 */
	function getLimit() {
		return $this->_Limit;
	}
	
	/**
	 * Set $_Limit to $inLimit
	 *
	 * @param integer $inLimit
	 * @return pmModel
	 */
	function setLimit($inLimit) {
		if ( $inLimit !== $this->_Limit ) {
			$this->_Limit = $inLimit;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Message
	 *
	 * @return mofilmUserPrivateMessage
	 */
	function getMessage() {
		return $this->_Message;
	}
	
	/**
	 * Set $_Message to $inMessage
	 *
	 * @param mofilmUserPrivateMessage $inMessage
	 * @return pmModel
	 */
	function setMessage($inMessage) {
		if ( $inMessage !== $this->_Message ) {
			$this->_Message = $inMessage;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_MessageType
	 *
	 * @return string
	 */
	function getMessageType() {
		return $this->_MessageType;
	}
	
	/**
	 * Set $_MessageType to $inMessageType
	 *
	 * @param string $inMessageType
	 * @return pmModel
	 */
	function setMessageType($inMessageType) {
		if ( $inMessageType !== $this->_MessageType ) {
			$this->_MessageType = $inMessageType;
			$this->setModified();
		}
		return $this;
	}
}