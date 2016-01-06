<?php
/**
 * reportCentreGenerator
 *
 * Stored in reportCentreGenerator.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package reportCentre
 * @subpackage reportCentreGenerator
 * @category reportCentreGenerator
 * @version $Rev: 16 $
 */

/**
 * reportCentreGenerator
 * 
 * This is the CLI command that interfaces the report queue to the actual
 * reports that build and the data. It will compile reports and cache them
 * and general acts as the middle man.
 * 
 * It is used by the ReportGenerator CLI app.
 * 
 * @package reportCentre
 * @subpackage reportCentreGenerator
 * @category reportCentreGenerator
 */
class reportCentreGenerator extends cliCommand {
	
	/**
	 * @see cliCommand::__construct()
	 * 
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct(
			$inRequest, 'report', new cliCommandChain(
				array(
					new cliCommandNull($inRequest, '<reportID>', 'The reportID to run. Must exist in the reporting.reports table.', false, false, false)
				)
			)
		);
		
		$this->setCommandHelp('Runs a report');
		$this->setCommandRequiresValue(true);
		$this->setHaltAppAfterExecute(true);
	}
	
	/**
	 * Executes the command
	 */
	function execute() {
		$oMemUsage = new utilityMemUsage();
		$oMemUsage->takeReading();
		
		$oTimer = new utilityStopWatch();
		$oTimer->start();
		
		$reportID = trim($this->getRequest()->getParam('report'));
		if ( !$reportID || !is_numeric($reportID) || $reportID == 0 ) {
			throw new cliApplicationCommandException($this, 'Invalid or missing report ID specified for generation');
		}
		
		$oReport = reportCentreReport::getInstance($reportID);
		if ( $oReport->getReportID() == 0 ) {
			throw new cliApplicationCommandException($this, 'Failed to load report with ID '.$reportID);
		}
		
		$this->getRequest()->getApplication()->notify(
			new cliApplicationEvent(
				cliApplicationEvent::EVENT_INFORMATIONAL,
				'Starting report run',
				null,
				array(
					'log.source' => array(
						'RepID' => $oReport->getReportID(),
						'RepSchID' => $oReport->getReportScheduleID(),
						'For' => $oReport->getUserID(),
					)
				)
			)
		);
		
		$oReport->setReportStatusID(reportCentreReportStatus::S_PROCESSING);
		$oReport->save();
		
		/*
		 * Run report inside a catch so we can convert errors to cli exceptions
		 */
		try {
			$oReportBuilder = $oReport->getReportSchedule()->getReportInstance();
			$oReportBuilder->run();
			
			$oWriter = $oReportBuilder->getReportWriter();
			$oWriter->compile();
			
			@chmod(system::getConfig()->getPathTemp().'/reports', 0777);
			@chmod($oWriter->getFileStore(), octdec(system::getConfig()->getParam('reports', 'cacheFolderPermissions', 0777)->getParamValue()));
			@chmod($oWriter->getFullPathToOutputFile(), octdec(system::getConfig()->getParam('reports', 'cacheFilePermissions', 0666)->getParamValue()));
			
			$oReport->setReportStatusID(reportCentreReportStatus::S_COMPLETED);
			$oReport->save();
			
			if ( $oReport->getReportSchedule()->getReportScheduleTypeID() != reportCentreReportScheduleType::T_ONCE ) {
				reportCentreReportQueue::addSchedule($oReport->getReportSchedule());
			} else {
				$oReport
					->getReportSchedule()
						->setReportScheduleStatus(reportCentreReportSchedule::REPORTSCHEDULESTATUS_COMPLETE)
						->save();
			}
			
			$this->getRequest()->getApplication()->notify(
				new cliApplicationEvent(
					cliApplicationEvent::EVENT_INFORMATIONAL,
					'Report processed successfully',
					null
				)
			);
			$this->getRequest()->getApplication()->getResponse()->addResponse('0:Report OK');
			
			if ( $oReport->getReportSchedule()->getDeliveryType()->getSendToUserEmail() && $oReport->getReportSchedule()->getParamSet()->getParam('report.email.address', false) ) {
				try {
					$this->getRequest()->getApplication()->notify(
						new cliApplicationEvent(
							cliApplicationEvent::EVENT_INFORMATIONAL,
							'Emailing report to user',
							null
						)
					);
					$oMailer = new utilityMail();
					$oMailer->SetFrom(system::getConfig()->getSystemFromAddress());
					$oMailer->AddAddress($oReport->getReportSchedule()->getParamSet()->getParam('report.email.address'));
					$oMailer->Subject = 'MOFILM: '.$oReport->getReportSchedule()->getReportTitle();
					$oMailer->Body = "Your report titled ".$oReport->getReportSchedule()->getReportTitle().", has been run and is attached to this message.\r\n\r\nThis is an automated message, please do not reply.\r\n\r\nMOFILM Ltd.";
					$oMailer->AddAttachment($oWriter->getFullPathToOutputFile(), $oWriter->getFilename(), 'base64', $oWriter->getMimeType());
					$oMailer->Send();
					
				} catch ( Exception $e ) {
					// log but don't propagate
					$this->getRequest()->getApplication()->notify(
						new cliApplicationEvent(
							cliApplicationEvent::EVENT_EXCEPTION,
							'Failed to send email to user: '.$e->getMessage(),
							null
						)
					);
				}
			}
			
			$oTimer->stop();
			systemLog::message("Report compiled in: ".utilityStringFunction::humanReadableTime($oTimer->elapsed()));
			systemLog::message("Report required: ".utilityStringFunction::humanReadableSize($oMemUsage->takeReading()));
		} catch ( Exception $e ) {
			$oReport->setReportStatusID(reportCentreReportStatus::S_FAILED_UNKNOWN);
			$oReport->save();
			
			throw new cliApplicationCommandException($this, 'Error in class: '.get_class($oReportBuilder).' with message '.$e->getMessage());
		}
	}
}