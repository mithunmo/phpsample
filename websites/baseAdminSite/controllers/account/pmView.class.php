<?php
/**
 * pmView.class.php
 * 
 * pmView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category pmView
 * @version $Rev: 11 $
 */


/**
 * pmView class
 * 
 * Provides the "pmView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category pmView
 */
class pmView extends mvcView {

	/**
	 * Assigns some default values to template engine that are always needed
	 *
	 * @return void
	 */
	function setupInitialVars() {
		parent::setupInitialVars();

		$this->getEngine()->assign('readUri', $this->buildUriPath(pmController::ACTION_READ));
		$this->getEngine()->assign('readSentUri', $this->buildUriPath(pmController::ACTION_READ_SENT));
		$this->getEngine()->assign('deleteUri', $this->buildUriPath(pmController::ACTION_DELETE));
		$this->getEngine()->assign('deleteSentUri', $this->buildUriPath(pmController::ACTION_DELETE_SENT));
		
		$this->getEngine()->assign('inboxUri', $this->buildUriPath(pmController::ACTION_INBOX));
		$this->getEngine()->assign('sentUri', $this->buildUriPath(pmController::ACTION_SENT));
		
		$this->getEngine()->assign('newUri', $this->buildUriPath(pmController::ACTION_NEW));
		$this->getEngine()->assign('sendUri', $this->buildUriPath(pmController::ACTION_SEND));
		$this->getEngine()->assign('replyUri', $this->buildUriPath(pmController::ACTION_REPLY));
		$this->getEngine()->assign('forwardUri', $this->buildUriPath(pmController::ACTION_FORWARD));
		
		$this->getEngine()->assign('actions', utilityOutputWrapper::wrap($this->getController()->getMenuItems()));
	}
	
	/**
	 * Shows the pmView page
	 *
	 * @return void
	 */
	function showInbox() {
		$this->setCacheLevelNone();
		
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		$this->getEngine()->assign('offset', $this->getModel()->getOffset());
		$this->getEngine()->assign('limit', $this->getModel()->getLimit());
		
		$this->render($this->getTpl('messageInbox', '/account'));
	}

	/**
	 * Shows the pmView page
	 *
	 * @return void
	 */
	function showSentItems() {
		$this->setCacheLevelNone();
		
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		$this->getEngine()->assign('offset', $this->getModel()->getOffset());
		$this->getEngine()->assign('limit', $this->getModel()->getLimit());
		
		$this->render($this->getTpl('messageSentItems', '/account'));
	}
	
	/**
	 * Shows the message for reading
	 * 
	 * @return void
	 */
	function showMessage() {
		$this->setCacheLevelNone();
		
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		
		$this->render($this->getTpl('messageRead', '/account'));
	}

	/**
	 * Shows the message for reading
	 * 
	 * @return void
	 */
	function showSentMessage() {
		$this->setCacheLevelNone();
		
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		
		$this->render($this->getTpl('messageSent', '/account'));
	}
	
	/**
	 * Displays a response for ajax requests
	 *
	 * @param string $inMessage
	 * @param string $inStatus
	 * @return void
	 */
	function showMessageDeletedResponse($inMessage, $inStatus) {
		switch ( $inStatus ) {
			case mvcSession::MESSAGE_CRITICAL: $status = 'critical'; break;
			case mvcSession::MESSAGE_ERROR: $status = 'error'; break;
			case mvcSession::MESSAGE_INFO: $status = 'info'; break;
			case mvcSession::MESSAGE_OK: $status = 'success'; break;
			case mvcSession::MESSAGE_WARNING: $status = 'warning'; break;
		}
		
		$this->setCacheLevelNone();
		$response = json_encode(
			array(
				'status' => $status,
				'message' => $inMessage,
			)
		);
		echo $response;
	}
	
	/**
	 * Displays the search results JSON results
	 * 
	 * @param mofilmUserSearchResult $inResults
	 * @return void
	 */
	function showSearchResponse($inResults) {
		$this->setCacheLevelNone();
		
		$response = array(array('caption' => 'MOFILM Support', 'value' => 0));
		foreach ( $inResults as $oUser ) {
			$flag = substr($oUser->getEmail(), strpos($oUser->getEmail(), '@'));
			$response[] = array('caption' => $oUser->getFullname()." ($flag)", 'value' => $oUser->getID());
		}
		
		echo json_encode($response);
	}
	
	/**
	 * Shows the reply form
	 * 
	 * @return void
	 */
	function showNewMessageForm() {
		$this->setCacheLevelNone();
		
		$this->fcbkComplete();
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		
		$this->render($this->getTpl('messageNew', '/account'));
	}
	
	/**
	 * Shows the reply form
	 * 
	 * @return void
	 */
	function showReplyForm() {
		$this->setCacheLevelNone();
		
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		
		$this->render($this->getTpl('messageReply', '/account'));
	}
	
	/**
	 * Shows the forwarding form
	 * 
	 * @return void
	 */
	function showForwardForm() {
		$this->setCacheLevelNone();
		
		$this->fcbkComplete();
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		
		$this->render($this->getTpl('messageForward', '/account'));
	}
	
	/**
	 * Adds fcbkcomplete support
	 * 
	 * @return void
	 */
	function fcbkComplete() {
		$this->addJavascriptResource(new mvcViewJavascript('fcbkComplete', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/jquery.fcbkcomplete.min.js'));
		$this->addCssResource(new mvcViewCss('fcbkCompleteCss', mvcViewCss::TYPE_FILE, '/libraries/jquery-plugins/fcbkcomplete/style.css'));
	}
	
	/**
	 * Returns the message check view
	 * 
	 * @return string
	 */
	function getMessageCheckView() {
		$this->setCacheLevelNone();
		
		$this->getEngine()->assign('messageCount', mofilmUserPrivateMessage::getMessageCount($this->getModel()->getUser()->getID(), 'New'));
		
		return $this->compile($this->getTpl('messageCheck', 'account'));
	}
}