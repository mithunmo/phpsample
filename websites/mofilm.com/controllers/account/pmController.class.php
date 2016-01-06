<?php
/**
 * pmController
 *
 * Stored in pmController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category pmController
 * @version $Rev: 11 $
 */


/**
 * pmController
 *
 * pmController class
 * 
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category pmController
 */
class pmController extends mvcController {
	
	const ACTION_READ = 'read';
	const ACTION_READ_SENT = 'readsent';
	
	const ACTION_DELETE = 'delete';
	const ACTION_DELETE_SENT = 'deletesent';
	
	const ACTION_SEND = 'send';
	const ACTION_MESSG = 'post';
	
	const ACTION_REPLY = 'reply';
	
	const ACTION_NEW = 'new';

	const ACTION_INBOX = 'inbox';
	const ACTION_SENT = 'sent';
	
	const VIEW_MESSAGE_CHECK = 'messageCheck';
	
	/**
	 * Stores $_MenuItems
	 *
	 * @var mvcControllerMenuItems
	 * @access protected
	 */
	protected $_MenuItems;
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setRequiresAuthentication(true);
		$this->setDefaultAction(self::ACTION_INBOX);
		$this
			->getControllerActions()
				->addAction(self::ACTION_READ)
				->addAction(self::ACTION_READ_SENT)
				
				->addAction(self::ACTION_DELETE)
				->addAction(self::ACTION_DELETE_SENT)
					
				->addAction(self::ACTION_NEW)
				->addAction(self::ACTION_MESSG)	
					
				->addAction(self::ACTION_INBOX)
				->addAction(self::ACTION_REPLY)
				
				->addAction(self::ACTION_SEND)
				->addAction(self::ACTION_SENT);
		
		$this
			->getControllerViews()
				->addView(self::VIEW_MESSAGE_CHECK);

		$this->addInputFilters();
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		switch ( $this->getAction() ) {
			case self::ACTION_READ:
				$this->readMessage();
			break;
			
			case self::ACTION_READ_SENT:
				$this->readSentMessage();
			break;
			
			case self::ACTION_REPLY:
				$this->replyToMessage();
			break;
			
			case self::ACTION_SEND:
				$this->sendMessage();
			break;
			
			case self::ACTION_DELETE:
				$this->deleteInboxMessage();
			break;
			
			case self::ACTION_DELETE_SENT:
				$this->deleteSentMessage();
			break;
			
			case self::ACTION_SENT:
				$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
				$data = $this->getInputManager()->doFilter();
				$this->addInputToModel($data, $this->getModel());
				$this->getModel()->setMessageType('sent');
				
				$this->getMenuItems()
					->addItem(new mvcControllerMenuItem($this->buildUriPath(self::ACTION_SENT), 'Sent Items', 'mail-folder-sent', 'Sent Items', false, mvcControllerMenuItem::PATH_TYPE_URI, false))
					->addItem(new mvcControllerMenuItem($this->buildUriPath(self::ACTION_INBOX), 'Inbox', 'mail-folder-inbox', 'Inbox', false, mvcControllerMenuItem::PATH_TYPE_URI, false));
				
				$oView = new pmView($this);
				$oView->showSentItems();
			break;
			
			case self::ACTION_NEW:
				$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
				$data = $this->getInputManager()->doFilter();
				//$userID = intval($data["userID"]);
				if ( is_int($data["userID"]) && mofilmUserManager::getInstanceByID($data["userID"]) ) {
					$this->getModel()->setContact($data["userID"]);
					$oView = new pmView($this);
					$oView->showNewMessageForm();
				} else {
					$this->redirect("/user/crew");
				}
			break;
		
			case self::ACTION_MESSG:			
				$this->postMessage();
				break;
		
			case self::ACTION_INBOX:
			default:
				$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
				$data = $this->getInputManager()->doFilter();
				$this->addInputToModel($data, $this->getModel());
				
				
				$this->getMenuItems()
					->addItem(new mvcControllerMenuItem($this->buildUriPath(self::ACTION_SENT), 'Sent Items', 'mail-folder-sent', 'Sent Items', false, mvcControllerMenuItem::PATH_TYPE_URI, false))
					->addItem(new mvcControllerMenuItem($this->buildUriPath(self::ACTION_INBOX), 'Inbox', 'mail-folder-inbox', 'Inbox', false, mvcControllerMenuItem::PATH_TYPE_URI, false));
				
				$oView = new pmView($this);
				$oView->showInbox();
		}
	}
	
	/**
	 * Handles reading an inbox message
	 * 
	 * @return void
	 */
	protected function readMessage() {
		$messageID = $this->getActionFromRequest(false, 1);
		
		if ( $this->getModel()->fetchInboxMessage($messageID) ) {
			if ( $this->getModel()->getMessage()->getStatus() == mofilmUserPrivateMessage::STATUS_NEW ) {
				systemLog::notice('User read inbox message ('.$messageID.')');
				$this->getModel()
					->getMessage()
						->setStatus(mofilmUserPrivateMessage::STATUS_READ)
						->setReadDate(date(system::getConfig()->getDatabaseDatetimeFormat()))
						->save();
			}
			
			$this->getMenuItems()
				->addItem(new mvcControllerMenuItem($this->buildUriPath(self::ACTION_DELETE, $messageID), 'Delete', 'action-delete-object', 'Delete', false, mvcControllerMenuItem::PATH_TYPE_URI, false))
				->addItem(new mvcControllerMenuItem($this->buildUriPath(self::ACTION_REPLY, $messageID), 'Reply', 'mail-reply-sender', 'Reply', false, mvcControllerMenuItem::PATH_TYPE_URI, false))
				->addItem(new mvcControllerMenuItem($this->buildUriPath(self::ACTION_INBOX), 'Inbox', 'mail-folder-inbox', 'Inbox', false, mvcControllerMenuItem::PATH_TYPE_URI, false));
			
			$oView = new pmView($this);
			$oView->showMessage();
		} else {
			$this->getRequest()->getSession()->setStatusMessage('Invalid or non-existant message requested.', mvcSession::MESSAGE_ERROR);
			$this->redirect($this->buildUriPath(self::ACTION_INBOX));
		}
	}
	
	/**
	 * Handles reading a sent message
	 * 
	 * @return void
	 */
	protected function readSentMessage() {
		$messageID = $this->getActionFromRequest(false, 1);
		
		if ( $this->getModel()->fetchSentMessage($messageID) ) {
			$this->getMenuItems()
				->addItem(new mvcControllerMenuItem($this->buildUriPath(self::ACTION_DELETE_SENT, $messageID), 'Delete', 'action-delete-object', 'Delete', false, mvcControllerMenuItem::PATH_TYPE_URI, false))
				->addItem(new mvcControllerMenuItem($this->buildUriPath(self::ACTION_SENT), 'Sent Items', 'mail-folder-sent', 'Sent Items', false, mvcControllerMenuItem::PATH_TYPE_URI, false))
				->addItem(new mvcControllerMenuItem($this->buildUriPath(self::ACTION_INBOX), 'Inbox', 'mail-folder-inbox', 'Inbox', false, mvcControllerMenuItem::PATH_TYPE_URI, false));
			
			$oView = new pmView($this);
			$oView->showSentMessage();
		} else {
			$this->getRequest()->getSession()->setStatusMessage('Invalid or non-existant message requested.', mvcSession::MESSAGE_ERROR);
			$this->redirect($this->buildUriPath(self::ACTION_INBOX));
		}
	}
	
	/**
	 * Replies to an existing message
	 * 
	 * @return void
	 */
	protected function replyToMessage() {
		$messageID = $this->getActionFromRequest(false, 1);
		
		if ( $this->getModel()->fetchInboxMessage($messageID) ) {
			$this->getMenuItems()
				->addItem(new mvcControllerMenuItem($this->buildUriPath(self::ACTION_SEND), 'Send', 'action-send', 'Send', false, mvcControllerMenuItem::PATH_TYPE_URI, true))
				->addItem(new mvcControllerMenuItem($this->buildUriPath(self::ACTION_INBOX), 'Cancel', 'action-cancel', 'Cancel', false, mvcControllerMenuItem::PATH_TYPE_URI, false))
				->addItem(new mvcControllerMenuItem($this->buildUriPath(self::ACTION_READ, $messageID), 'Back', 'action-back', 'Back', false, mvcControllerMenuItem::PATH_TYPE_URI, false));
					
			$oView = new pmView($this);
			$oView->showReplyForm();
		} else {
			$this->getRequest()->getSession()->setStatusMessage('Invalid or non-existant message requested.', mvcSession::MESSAGE_ERROR);
			$this->redirect($this->buildUriPath(self::ACTION_INBOX));
		}
	}
	
	/**
	 * Sends a message
	 * 
	 * @return void
	 */
	protected function sendMessage() {
		$data = $this->getInputManager()->doFilter();
		
		if (
			$this->getRequest()->getSession()->getUser()->getClientID() != mofilmClient::MOFILM && 
			!$this->getRequest()->getSession()->getUser()->getPermissions()->isRoot()
		) {
			$data['Recipient'][0] = 1;
		}
		
		try {
			$data['Subject'] = trim(strip_tags($_POST['Subject']));
			$data['Message'] = trim(strip_tags($_POST['Message']));
			
			if ( $this->getModel()->sendMessage($data) ) {
				$this->getRequest()->getSession()->setStatusMessage('Message sent successfully.', mvcSession::MESSAGE_OK);
				$this->redirect($this->buildUriPath(self::ACTION_INBOX));
				return;
			} else {
				throw new mvcModelException('Message sending failed. Please contact support if you continue to see this message');
			}
		} catch ( mvcModelException $e ) {
			$this->getRequest()->getSession()->setStatusMessage($e->getMessage(), mvcSession::MESSAGE_ERROR);
			$this->redirect($this->buildUriPath(self::ACTION_NEW));
		}
	}
	
	/**
	 * Post a message
	 * 
	 * void
	 */
	protected function postMessage() {
		
		$data = $this->getInputManager()->doFilter();

		try {
			$data['Subject'] = trim(strip_tags($_POST['Subject']));
			$data['Message'] = trim(strip_tags($_POST['Message']));
			
			if ( $this->getModel()->postMessage($data) ) {
				$this->getRequest()->getSession()->setStatusMessage('Message sent successfully.', mvcSession::MESSAGE_OK);
				$this->redirect($this->buildUriPath(self::ACTION_INBOX));
				return;
			} else {
				throw new mvcModelException('Message sending failed. Please contact support if you continue to see this message');
			}
		} catch ( mvcModelException $e ) {
			$this->getRequest()->getSession()->setStatusMessage($e->getMessage(), mvcSession::MESSAGE_ERROR);
			$this->redirect($this->buildUriPath(self::ACTION_NEW));
		}
		
	}
	
	/**
	 * Deletes an inbox message
	 * 
	 * @return void
	 */
	protected function deleteInboxMessage() {
		$messageID = $this->getActionFromRequest(false, 1);
		
		if ( $this->getModel()->fetchInboxMessage($messageID) ) {
			systemLog::notice('User is trying to delete inbox message ('.$messageID.')');
			if ( $this->getModel()->getMessage()->delete() ) {
				$msg = 'Message successfully deleted.';
				$level = mvcSession::MESSAGE_OK;
			} else {
				$msg = 'Oops, there was an error while trying to delete the message.';
				$level = mvcSession::MESSAGE_ERROR;
			}
			systemLog::notice($msg);
		} else {
			$msg = 'Invalid or non-existant message requested.';
			$level = mvcSession::MESSAGE_WARNING;
		}
		
		if ( $this->getRequest()->isAjaxRequest() ) {
			$oView = new pmView($this);
			$oView->showMessageDeletedResponse($msg, $level);
			exit;
		} else {
			$this->getRequest()->getSession()->setStatusMessage($msg, $level);
			$this->redirect($this->buildUriPath(self::ACTION_INBOX));
		}
	}
	
	/**
	 * Deletes a sent message
	 * 
	 * @return void
	 */
	protected function deleteSentMessage() {
		$messageID = $this->getActionFromRequest(false, 1);
				
		if ( $this->getModel()->fetchSentMessage($messageID) ) {
			systemLog::notice('User is trying to delete sent message ('.$messageID.')');
			if ( $this->getModel()->getMessage()->delete() ) {
				$msg = 'Message successfully deleted.';
				$level = mvcSession::MESSAGE_OK;
			} else {
				$msg = 'Oops, there was an error while trying to delete the message.';
				$level = mvcSession::MESSAGE_ERROR;
			}
			systemLog::notice($msg);
		} else {
			$msg = 'Invalid or non-existant message requested.';
			$level = mvcSession::MESSAGE_WARNING;
		}
		
		if ( $this->getRequest()->isAjaxRequest() ) {
			$oView = new pmView($this);
			$oView->showMessageDeletedResponse($msg, $level);
			exit;
		} else {
			$this->getRequest()->getSession()->setStatusMessage($msg, $level);
			$this->redirect($this->buildUriPath(self::ACTION_SENT));
		}
	}
	
	/**
	 * Handles standalone view requests
	 * 
	 * @param array $params
	 * @return string
	 */
	function fetchStandaloneView(array $params) {
		switch ( $params['view'] ) {
			case self::VIEW_MESSAGE_CHECK:
				$oView = new pmView($this);
				return $oView->getMessageCheckView();
			break;
		}
		return '';
	}
	
	
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('Offset', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Limit', utilityInputFilter::filterInt());
		
		$this->getInputManager()->addFilter('Recipient', utilityInputFilter::filterStringArray());
		$this->getInputManager()->addFilter('Subject', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Message', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('MessageAction', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('MessageID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('MovieID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('userID', utilityInputFilter::filterInt());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param pmModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		if ( !$inData['Limit'] || $inData['Limit'] > 30 ) {
			$inData['Limit'] = 30;
		}
		if ( !$inData['Offset'] || $inData['Offset'] < 0 ) {
			$inData['Offset'] = 0;
		}
		$inModel->setOffset($inData['Offset']);
		$inModel->setLimit($inData['Limit']);
	}
	
	/**
	 * Fetches the model
	 *
	 * @return pmModel
	 */
	function getModel() {
		if ( !parent::getModel() ) {
			$this->buildModel();
		}
		return parent::getModel();
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new pmModel();
		$oModel->setRequest($this->getRequest());
		$oModel->setUser($this->getRequest()->getSession()->getUser());
		$this->setModel($oModel);
	}

	/**
	 * Returns $_MenuItems
	 *
	 * @return mvcControllerMenuItems
	 */
	function getMenuItems() {
		if ( !$this->_MenuItems instanceof mvcControllerMenuItems ) {
			$this->_MenuItems = new mvcControllerMenuItems();
		}
		return $this->_MenuItems;
	}

	/**
	 * Set $_MenuItems to $inMenuItems
	 *
	 * @param mvcControllerMenuItems $inMenuItems
	 * @return mvcDaoController
	 */
	function setMenuItems(mvcControllerMenuItems $inMenuItems) {
		if ( $inMenuItems !== $this->_MenuItems ) {
			$this->_MenuItems = $inMenuItems;
			$this->setModified();
		}
		return $this;
	}
}