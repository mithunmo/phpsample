<?php
/**
 * reportsController
 *
 * Stored in reportsController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category reportsController
 * @version $Rev: 11 $
 */


/**
 * reportsController
 *
 * reportsController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category reportsController
 */
class reportsController extends mvcController {
	
	const ACTION_VIEW = 'inbox';
	const ACTION_INBOX = 'inbox';
	const ACTION_NEW = 'new';
	const ACTION_SCHEDULE = 'schedule';
	const ACTION_SAVE = 'save';
	const ACTION_DELETE = 'delete';
	const ACTION_DELETE_SCHEDULE = 'deleteSchedule';
	const ACTION_VALIDATE = 'validate';
	const ACTION_DOWNLOAD = 'download';
	const ACTION_REFRESH = 'refresh';
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setDefaultAction(self::ACTION_VIEW);
		$this->getControllerActions()
			->addAction(self::ACTION_VIEW)
			->addAction(self::ACTION_DELETE)
			->addAction(self::ACTION_DELETE_SCHEDULE)
			->addAction(self::ACTION_INBOX)
			->addAction(self::ACTION_NEW)
			->addAction(self::ACTION_SAVE)
			->addAction(self::ACTION_SCHEDULE)
			->addAction(self::ACTION_VALIDATE)
			->addAction(self::ACTION_DOWNLOAD)
			->addAction(self::ACTION_REFRESH);
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		switch ( $this->getAction() ) {
			case self::ACTION_NEW:
				$this->actionNew();
			break;
			
			case self::ACTION_SAVE:
				$this->actionSave();
			break;
			
			case self::ACTION_VALIDATE:
				$this->actionValidate();
			break;
			
			case self::ACTION_SCHEDULE:
				$this->assignPaging();

				$oView = new reportsView($this);
				$oView->showSchedulePage();
			break;
			
			case self::ACTION_DELETE:
				$this->actionDeleteReport();
			break;
			
			case self::ACTION_DELETE_SCHEDULE:
				$this->actionDeleteSchedule();
			break;
			
			case self::ACTION_DOWNLOAD:
				$this->actionDownload();
			break;
			
			case self::ACTION_REFRESH:
				$this->actionRefresh();
			break;
			
			case self::ACTION_INBOX:
			default:
				$this->assignPaging();

				$oView = new reportsView($this);
				$oView->showInboxPage();
		}
	}

	/**
	 * Filters the GET params for Offset / Limit and assigns to model
	 *
	 * @return void
	 */
	protected function assignPaging() {
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$this->getInputManager()->addFilter('Offset', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Limit', utilityInputFilter::filterInt());
		$data = $this->getInputManager()->doFilter();

		$this->getModel()->setSearchOffset($data['Offset']);
		$this->getModel()->setSearchLimit($data['Limit']);
	}
	
	/**
	 * Handles the new action
	 * 
	 * @return void
	 */
	protected function actionNew() {
		$reportID = $this->getActionFromRequest(false, 1);
		if ( !$reportID || !is_numeric($reportID) ) {
			$oView = new reportsView($this);
			$oView->showNewReportPage();
			return;
		}
			
		if ( !$this->getModel()->isAllowedToRunReport($reportID) ) {
			$this->getRequest()->getSession()->setStatusMessage('You are not permitted to run the requested report', mvcSession::MESSAGE_WARNING);
			$this->redirect($this->buildUriPath(self::ACTION_INBOX));
			return;
		}
		
		$this->getModel()->setReportTypeID($reportID);
		
		$oView = new reportsView($this);
		$oView->showNewReportOptionsPage();
	}
	
	/**
	 * Handles the save action
	 * 
	 * @return void
	 */
	protected function actionSave() {
		$data = $this->getInputManager()->doFilter();
                if (count($_FILES) > 0 ){
                    $filename = basename($_FILES["csv"]["tmp_name"]).time();
                    $path_parts = pathinfo($_FILES["csv"]["name"]);
                    $ext = $path_parts['extension'];
                    $destination = mofilmConstants::getReportFolder() .$filename . "." . $ext;
                    move_uploaded_file($_FILES["csv"]["tmp_name"], $destination);
                    $data["params"]["report.csvlink"] = $destination;                
                }
                
		try {
                        $this->addInputToModel($data, $this->getModel());                                            
			$this->getModel()->getReportSchedule()->getReportInstance()->isValid();			
			
			$this->getModel()->getReportSchedule()->save();
                        
			reportCentreReportQueue::addSchedule($this->getModel()->getReportSchedule());
			systemLog::message('User queued report for '.$this->getModel()->getReportSchedule()->getScheduledDate());
			$status = mvcSession::MESSAGE_OK;
			$message = 'Report scheduled successfully';
		} catch ( Exception $e ) {
			systemLog::warning($e->getMessage());
			$status = mvcSession::MESSAGE_ERROR;
			$message = $e->getMessage();
		}
		
		$this->getRequest()->getSession()->setStatusMessage($message, $status);
		$this->redirect($this->buildUriPath(self::ACTION_INBOX));
	}
	
	/**
	 * Deletes a report
	 * 
	 * @return void
	 */
	protected function actionDeleteReport() {
		$reportID = $this->getActionFromRequest(false, 1);
		if ( $reportID && is_numeric($reportID) ) {
			try {
				$oReport = reportCentreReport::getInstance($reportID);
				if ( $oReport->getReportID() > 0 && $oReport->getUserID() == $this->getRequest()->getSession()->getUser()->getID() ) {
					$oReport->setIsHidden(true);
					$oReport->save();
					
					$status = mvcSession::MESSAGE_OK;
					$message = "Report removed successfully";
					systemLog::message($message);
				} else {
					throw new mvcControllerException("You are not authorised to remove the report");
				}
			} catch ( Exception $e ) {
				systemLog::error($e->getMessage());
				$status = mvcSession::MESSAGE_ERROR;
				$message = $e->getMessage();
			}
		} else {
			$status = mvcSession::MESSAGE_ERROR;
			$message = 'No reportID was specified for the action';
		}
		
		$this->getRequest()->getSession()->setStatusMessage($message, $status);
		$this->redirect($this->buildUriPath(self::ACTION_INBOX));
	}
	
	/**
	 * Deletes a report schedule
	 * 
	 * @return void
	 */
	protected function actionDeleteSchedule() {
		$reportID = $this->getActionFromRequest(false, 1);
		if ( $reportID && is_numeric($reportID) ) {
			try {
				$oReport = reportCentreReportSchedule::getInstance($reportID);
				if ( $oReport->getReportScheduleID() > 0 && $oReport->getUserID() == $this->getRequest()->getSession()->getUser()->getID() ) {
					$oReport->setReportScheduleStatus(reportCentreReportSchedule::REPORTSCHEDULESTATUS_REMOVED);
					$oReport->save();
					
					$status = mvcSession::MESSAGE_OK;
					$message = "Report schedule removed successfully";
					systemLog::message($message);
				} else {
					throw new mvcControllerException("You are not authorised to remove the report schedule");
				}
			} catch ( Exception $e ) {
				systemLog::error($e->getMessage());
				$status = mvcSession::MESSAGE_ERROR;
				$message = $e->getMessage();
			}
		} else {
			$status = mvcSession::MESSAGE_ERROR;
			$message = 'No scheduleID was specified for the action';
		}
		
		$this->getRequest()->getSession()->setStatusMessage($message, $status);
		$this->redirect($this->buildUriPath(self::ACTION_SCHEDULE));
	} 
	
	/**
	 * Handles the validate action
	 * 
	 * @return void
	 */
	protected function actionValidate() {
		$data = $this->getInputManager()->doFilter();
		try {
			$this->addInputToModel($data, $this->getModel());
			$this->getModel()->getReportSchedule()->getReportInstance()->isValid();
			
			$status = mvcSession::MESSAGE_OK;
			$message = 'ok';
		} catch ( Exception $e ) {
			systemLog::warning($e->getMessage());
			$status = mvcSession::MESSAGE_ERROR;
			$message = $e->getMessage();
		}
		
		if ( $this->getRequest()->isAjaxRequest() ) {
			$oView = new reportsView($this);
			$oView->sendAjaxResponse($status, $message);
		} else {
			$this->getRequest()->getSession()->setStatusMessage($message, $status);
			$this->redirect($this->buildUriPath(self::ACTION_NEW, $this->getModel()->getReportTypeID()));
		}
	}
	
	/**
	 * Handle report downloads
	 * 
	 * @return void
	 */
	protected function actionDownload() {
		$reportID = $this->getActionFromRequest(false, 1);
		$format = $this->getActionFromRequest(false, 3);
		
		try {
			if ( !$reportID || !is_numeric($reportID) ) {
				throw new mvcControllerException('Missing or invalid reportID provided');
			}
			$oReport = reportCentreReport::getInstance($reportID);
			if ( $oReport->getReportID() == 0 || $oReport->getUserID() != $this->getRequest()->getSession()->getUser()->getID() ) {
				throw new mvcControllerException('You are not authorised to access the report');
			}
			if ( $oReport->getIsHidden() ) {
				throw new mvcControllerException('The requested report has been removed');
			}
			if ( $oReport->getReportStatusID() != reportCentreReportStatus::S_COMPLETED ) {
				throw new mvcControllerException('The report is not yet ready for download');
			}
			$formats = $oReport->getReportSchedule()->getReportType()->getOutputTypes();
			if ( !in_array($format, $formats) ) {
				throw new mvcControllerException("The requested format ($format) is not supported by the report");
			}
			
			$oWriter = $oReport->getReportSchedule()->getReportInstance()->getReportWriter();
			if ( !file_exists($oWriter->getFullPathToOutputFile()) ) {
				throw new mvcControllerException("The requested report file has expired. Please refresh your report to download again.");
			}
			
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			$this->getRequest()->getSession()->setStatusMessage($e->getMessage(), mvcSession::MESSAGE_ERROR);
			$this->redirect($this->buildUriPath(self::ACTION_INBOX));
			return;
		}
		
		systemLog::message("User is downloading report ({$oReport->getReportID()}) as {$format}");
		header("HTTP/1.0 200 OK");
		header("Cache-Control: no-cache, must-revalidate");
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
		header("Content-Type: ".$oWriter->getMimeType());
		header('Content-Disposition: attachment; filename="'.utilityStringFunction::normaliseStringCharactersForUri($oReport->getReportSchedule()->getReportTitle(), '_').'.'.$oWriter->getExtension().'"');
		header("Content-Length: ".filesize($oWriter->getFullPathToOutputFile()));
		readfile($oWriter->getFullPathToOutputFile());
	}
	
	/**
	 * Handles the refresh action
	 * 
	 * @return void
	 */
	protected function actionRefresh() {
		$reportID = $this->getActionFromRequest(false, 1);
		
		try {
			if ( !$reportID || !is_numeric($reportID) ) {
				throw new mvcControllerException('Missing or invalid reportID provided');
			}
			$oReport = reportCentreReport::getInstance($reportID);
			if ( $oReport->getReportID() == 0 || $oReport->getUserID() != $this->getRequest()->getSession()->getUser()->getID() ) {
				throw new mvcControllerException('You are not authorised to access the report');
			}
			if ( $oReport->getIsHidden() ) {
				throw new mvcControllerException('The requested report has been removed');
			}
			
			reportCentreReportQueue::addReport($oReport);
			
			$oReport->setReportStatusID(reportCentreReportStatus::S_REFRESHING);
			$oReport->save();
			
			$this->getRequest()->getSession()->setStatusMessage('The report has been successfully scheduled for a refresh', mvcSession::MESSAGE_OK);
			$this->redirect($this->buildUriPath(self::ACTION_INBOX));
			return;
			
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			$this->getRequest()->getSession()->setStatusMessage($e->getMessage(), mvcSession::MESSAGE_ERROR);
			$this->redirect($this->buildUriPath(self::ACTION_INBOX));
			return;
		}
	}
	
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('ReportTypeID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('ReportTitle', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ScheduledDate', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ReportScheduleTypeID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('DeliveryTypeID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('ScheduleTime', utilityInputFilter::filterStringArray());
		$this->getInputManager()->addFilter('params', utilityInputFilter::filterStringArray());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 * 
	 * @param array $inData
	 * @param reportsModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		if ( strlen($inData['ReportTitle']) < 3 ) {
			throw new mvcControllerException('ReportTitle is too short, please specify a report title');
		}
		
		/*
		 * Configure default params for all reports
		 */
		$inData['params'][reportBase::OPTION_USE_CACHE] = system::getConfig()->getParam('reports', 'useCache', true)->getParamValue();
		$inData['params'][reportBase::OPTION_CACHE_LIFETIME] = system::getConfig()->getParam('reports', 'cacheTTL', 3600*12)->getParamValue();
		$inData['params'][reportBase::OPTION_CACHE_FILE_PERMISSIONS] = octdec(system::getConfig()->getParam('reports', 'cacheFilePermissions', 0666)->getParamValue());
		$inData['params'][reportBase::OPTION_CACHE_FOLDER_PERMISSIONS] = octdec(system::getConfig()->getParam('reports', 'cacheFolderPermissions', 0777)->getParamValue());
		
		$inModel->getReportSchedule()->setDeliveryTypeID($inData['DeliveryTypeID']);
		$inModel->getReportSchedule()->setReportScheduleTypeID($inData['ReportScheduleTypeID']);
		$inModel->getReportSchedule()->setReportScheduleStatus(reportCentreReportSchedule::REPORTSCHEDULESTATUS_ACTIVE);
		$inModel->getReportSchedule()->setReportTitle($inData['ReportTitle']);
		$inModel->getReportSchedule()->setReportTypeID($inData['ReportTypeID']);
		$inModel->getReportSchedule()->setScheduledDate($inData['ScheduledDate'].' '.implode(':', $inData['ScheduleTime']));
		$inModel->getReportSchedule()->getParamSet()->setParam($inData['params'], null);
	}
	
	/**
	 * Fetches the model
	 *
	 * @return reportsModel
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
		$oModel = new reportsModel($this->getRequest()->getSession()->getUser());
		$this->setModel($oModel);
	}
}