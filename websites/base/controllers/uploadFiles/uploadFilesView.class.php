<?php
/**
 * uploadFilesView.class.php
 * 
 * uploadFilesView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_base
 * @subpackage controllers
 * @category uploadFilesView
 * @version $Rev: 1 $
 */


/**
 * uploadFilesView class
 * 
 * Provides the "uploadFilesView" page
 * 
 * @package websites_base
 * @subpackage controllers
 * @category uploadFilesView
 */
class uploadFilesView extends mvcView {

	/**
	 * @see mvcViewBase::setupInitialVars()
	 */
	function setupInitialVars() {
		parent::setupInitialVars();
		
		$this->getEngine()->assign('daoUriView', $this->buildUriPath(uploadFilesController::ACTION_SEARCH));
		$this->setCacheLevelNone();
	}
	
	/**
	 * 
	 */
	function showUploadedFilesList() {
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		$this->getEngine()->assign('oResults', utilityOutputWrapper::wrap($this->getModel()->doSearch()));
		$this->getEngine()->assign('pagingOffset', utilityOutputWrapper::wrap($this->getModel()->getUploadedFilesSearch()->getOffset()));
		$this->getEngine()->assign('daoSearchQuery', $this->getController()->getSearchQueryAsString());
		
		if ( $this->getController()->getAction() == uploadFilesController::ACTION_SEARCH ) {
			$this->getEngine()->assign('searchStatus', $this->getModel()->getUploadedFilesSearch()->getStatus());
			
			if ( $this->getModel()->getUploadedFilesSearch()->getEventCount() == 1 ) {
				$tmp = $this->getModel()->getUploadedFilesSearch()->getEvents();
				$inEventID = $tmp[0];
			} else {
				$inEventID = 0;
			}
			$this->getEngine()->assign('searchEventID', $inEventID);
			unset ($tmp);
			
			if ( $this->getModel()->getUploadedFilesSearch()->getSourceCount() > 1 ) {
				$tmp = $this->getModel()->getUploadedFilesSearch()->getSources();
				$inSourceID = mofilmSource::getInstance($tmp[0])->getName();
			} elseif ( $this->getModel()->getUploadedFilesSearch()->getSourceCount() == 1 ) {
				$tmp = $this->getModel()->getUploadedFilesSearch()->getSources(); 
				$inSourceID = $tmp[0];
			} else {
				$inSourceID = 0;
			}
			$this->getEngine()->assign('searchSourceID', $inSourceID);
			unset ($tmp);
		}
		
		$this->render($this->getTpl('uploadedFilesList'));
	}
	
	/**
	 * Shows the Upload Page
	 * 
	 * @return void
	 */
	function showUploadFilesPage() {
		$this->setCacheLevelNone();
                systemLog::message($this->getModel()->getSource()->isOpen());
                systemLog::message("Name". $this->getModel()->getSource()->getName());
		$this->getEngine()->assign('source', $this->getModel()->getSource());
		//$this->getEngine()->assign('eventsall', utilityOutputWrapper::wrap(mofilmEvent::listOfObjects(null, null, true)));
		$this->render($this->getTpl('uploadFile', '/uploadFiles'));
	}
	
	/**
	 * 
	 */
	function showUploadStatus($message) {
		$this->getEngine()->assign('message', $message);
		$this->render($this->getTpl('uploadFileStatus'));
	}
	
	/**
	 * Sends a JSON response for AJAX calls
	 * 
	 * @param string $inMessage Message to display
	 * @param mixed $inStatus Status of result, 0 = info, true = success, false = error, 
	 * @return void
	 */
	function sendJsonResult($inMessage, $inStatus) {
		$this->setCacheLevelNone();
		
		$response = json_encode(
			array(
				'status' => $inStatus === mvcSession::MESSAGE_INFO ? 'info' : ($inStatus === mvcSession::MESSAGE_OK ? 'success' : 'error'),
				'message' => $inMessage,
			)
		);
		echo $response;
	}
}