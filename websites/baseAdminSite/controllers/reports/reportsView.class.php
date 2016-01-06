<?php
/**
 * reportsView.class.php
 *
 * reportsView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category reportsView
 * @version $Rev: 11 $
 */


/**
 * reportsView class
 *
 * Provides the "reportsView" page
 *
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category reportsView
 */
class reportsView extends mvcView {

	/**
	 * @see mvcViewBase::setupInitialVars()
	 */
	function setupInitialVars() {
		parent::setupInitialVars();

		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		$this->getEngine()->assign('reportDownloadUri', $this->buildUriPath(reportsController::ACTION_DOWNLOAD));
		$this->getEngine()->assign('reportInboxUri', $this->buildUriPath(reportsController::ACTION_INBOX));
		$this->getEngine()->assign('reportDeleteUri', $this->buildUriPath(reportsController::ACTION_DELETE));
		$this->getEngine()->assign('reportDeleteScheduleUri', $this->buildUriPath(reportsController::ACTION_DELETE_SCHEDULE));
		$this->getEngine()->assign('reportNewUri', $this->buildUriPath(reportsController::ACTION_NEW));
		$this->getEngine()->assign('reportSaveUri', $this->buildUriPath(reportsController::ACTION_SAVE));
		$this->getEngine()->assign('reportScheduleUri', $this->buildUriPath(reportsController::ACTION_SCHEDULE));
		$this->getEngine()->assign('reportRefreshUri', $this->buildUriPath(reportsController::ACTION_REFRESH));
		$this->getEngine()->assign('cacheTTL', utilityStringFunction::humanReadableTime(system::getConfig()->getParam('reports', 'cacheTTL', 3600*12)->getParamValue()));
	}

	/**
	 * Shows the reportsView page
	 *
	 * @return void
	 */
	function showInboxPage() {
		$this->setCacheLevelNone();
		
		$this->getEngine()->assign('oReports', utilityOutputWrapper::wrap($this->getModel()->getInbox()));
		$this->getEngine()->assign('view', 'inbox');
		
		$this->addJavascriptResource(
			new mvcViewJavascript('ajaxRefresh', mvcViewJavascript::TYPE_INLINE, '
				function refreshInbox() {
					ajaxLoader(document.getElementById("reportInbox"));
					$.get(
						"'.$this->buildUriPath(reportsController::ACTION_INBOX).'/as.fragment",
						null,
						function(data, textStatus, XMLHttpRequest) {
							$("#reportInbox").replaceWith(data);
						},
						"html"
					);
				}
				setInterval("refreshInbox()", 20000);'
			)
		);

		$this->render($this->getTpl('reports'));
	}

	/**
	 * Shows the reportsView page
	 *
	 * @return void
	 */
	function showSchedulePage() {
		$this->setCacheLevelNone();
		
		$this->getEngine()->assign('oReports', utilityOutputWrapper::wrap($this->getModel()->getScheduledReports()));
		$this->getEngine()->assign('view', 'schedule');

		$this->render($this->getTpl('reports'));
	}
	
	/**
	 * Shows the available reports a user can select to run
	 * 
	 * @return void
	 */
	function showNewReportPage() {
		$this->setCacheLevelNone();
		
		$this->getEngine()->assign('oReports', utilityOutputWrapper::wrap($this->getModel()->getAvailableUserReports()));

		$this->render($this->getTpl('newReport'));
	}
	
	/**
	 * Shows the options for the report
	 * 
	 * @return void
	 */
	function showNewReportOptionsPage() {
		$this->setCacheLevelNone();
		
		$this->getEngine()->assign('reportOptions', 'reportOptionsForReport'.$this->getModel()->getReportTypeID());
		
		$this->render($this->getTpl('newReportOptions'));
	}
	
	/**
	 * Sends a JSON encoded ajax response
	 * 
	 * @param integer $inStatus
	 * @param string $inMessage
	 * @return void
	 */
	function sendAjaxResponse($inStatus, $inMessage) {
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
}