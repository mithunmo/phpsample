<?php
/**
 * systemCommandReporter Class
 * 
 * Stored in systemCommandReporter.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern 2010
 * @package scorpio
 * @subpackage cli
 * @category systemCommandReporter
 * @version $Rev: 747 $
 */


/**
 * systemCommandReporter class
 * 
 * Reporter aggregates a collection of systemReporterInterface classes and allows
 * various data to be aggregated and emailed or displayed. This allows log files
 * to be aggregated for specific errors, or for custom components to record stats
 * for a period of time or highlight problem areas that have been stored to a
 * database etc.
 * 
 * As this uses other classes to do the heavy lifting, each of those classes must
 * implement the {@link systemReporterInterface} and be available to the autoload
 * system.
 *
 * @package scorpio
 * @subpackage cli
 * @category systemCommandReporter
 */
class systemCommandReporter extends cliCommand {
	
	const COMMAND = 'report';
	const COMMAND_DAYS = 'days';
	const COMMAND_MODULES = 'modules';
	const COMMAND_RECIPIENTS = 'recipients';
	const COMMAND_DATE_FORMAT = 'date-format';
	const COMMAND_LOG_LEVELS = 'log-levels';
	
	/**
	 * Creates a new command
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct($inRequest, self::COMMAND, new cliCommandChain(
				array(
					new cliCommandNull($inRequest, self::COMMAND_MODULES, 'The modules to include in the report. This is a comma separated list of class names e.g. systemLogSummary', true, true, false),
					new cliCommandNull($inRequest, self::COMMAND_DATE_FORMAT, 'Format for the dates for use in the reporter modules. Any valid date() format can be used e.g. d/m/Y. If using systemLogSummary, this format should be the same as that used in the log files.', true, true, false),
					new cliCommandNull($inRequest, self::COMMAND_DAYS, 'Number of days to report on e.g. 7 would cover the last week. If not specified uses 1 i.e. yesterday', false, true, true),
					new cliCommandNull($inRequest, self::COMMAND_RECIPIENTS, 'The email addresses to send reports to. This is a comma separated list of addresses e.g. person@domain.com,person2@domain.org', true, true, true),
					new cliCommandNull($inRequest, self::COMMAND_LOG_LEVELS, 'Comma separated list of log level integers to report on for the systemLogSummary module e.g. 2,4,6', false, true, true),
				)
			)
		);
		
		$this->setCommandHelp(
			'Reporter aggregates a collection of systemReporterInterface classes and allows'.
			' various data to be aggregated and emailed or displayed. If no recipients are set'.
			' report will be output to the console.'
		);
		
		$this->setCommandRequiresValue(false);
		$this->setCommandIsSwitch(false);
	}
	
	/**
	 * Executes the command
	 *
	 * @return void
	 */
	function execute() {
		$days = $this->getRequest()->getParam(self::COMMAND_DAYS);
		$modules = $this->getRequest()->getParam(self::COMMAND_MODULES);
		$recipients = $this->getRequest()->getParam(self::COMMAND_RECIPIENTS);
		$dateFormat = $this->getRequest()->getParam(self::COMMAND_DATE_FORMAT);
		$logLevels = $this->getRequest()->getParam(self::COMMAND_LOG_LEVELS);
		
		if ( !$days || !is_numeric($days) ) {
			$days = 1;
		}
		if ( !$modules || strlen($modules) < 3 ) {
			throw new cliApplicationCommandException($this, 'No modules were specified, please specify at least one.');
		}
		if ( !$dateFormat || strlen($dateFormat) < 2 ) {
			throw new cliApplicationCommandException($this, 'Please specify the date-format');
		}
		if ( !$logLevels || strlen($logLevels) < 1 ) {
			$logLevels = systemLogLevel::CRITICAL.','.systemLogLevel::ERROR.','.systemLogLevel::EXCEPTION;
		}
		if ( !$recipients || strlen($recipients) < 3 ) {
			$recipients = '';
		}
		
		$recipients = $this->_buildRecipients($recipients);		
		$logLevels = $this->_buildLogLevels($logLevels);
		$dates = $this->_buildDateArray($days, $dateFormat);
		
		$this->getRequest()->getApplication()->notify(
			new cliApplicationEvent(
				cliApplicationEvent::EVENT_INFORMATIONAL,
				'Running report for modules: '.$modules
			)
		);
		
		$report = $this->_buildReport($modules, $dates, $logLevels);
		
		if ( count($recipients) == 0 ) {
			$this->getRequest()->getApplication()->getResponse()->addResponse($report);
		} else {
			$this->getRequest()->getApplication()->notify(
				new cliApplicationEvent(
					cliApplicationEvent::EVENT_INFORMATIONAL,
					'Sending report to recipients: '.implode(', ', $recipients)
				)
			);
			
			$oMail = new utilityMail();
			$oMail->SetFrom(system::getConfig()->getSystemFromAddress());
			$oMail->Subject = 'Report from '.system::getConfig()->getSystemHostname();
			$oMail->Body = $report;
			foreach ( $recipients as $recipient ) {
				$oMail->AddAddress($recipient);
			}
			if ( !$oMail->Send() ) {
				throw new cliApplicationCommandException($this, 'Failed to send email: '.$oMail->ErrorInfo);
			}
		}
	}
	
	/**
	 * Creates an array of dates to use in the reporters
	 * 
	 * @param integer $inDays
	 * @param string $inDateFormat
	 * @return array
	 */
	private function _buildDateArray($inDays, $inDateFormat) {
		$return = array();
		for ( $i=$inDays; $i>=1; $i-- ) {
			$return[] = date($inDateFormat, strtotime("-$i days"));
		}
		return $return;
	}
	
	/**
	 * Converts the string of recipients to an array and validates that each address is useable
	 * 
	 * @param string $inRecipients
	 * @return array
	 */
	private function _buildRecipients($inRecipients) {
		$return = array();
		$recipients = explode(',', $inRecipients);
		$oValEmail = new utilityValidateEmailAddress();
		
		foreach ( $recipients as $recipient ) {
			$recipient = trim($recipient);
			if ( strlen($recipient) > 0 ) {
				if ( $oValEmail->isValid($recipient) && !in_array($recipient, $return) ) {
					$return[] = $recipient;
				}
			}
		}
		return $return;
	}
	
	/**
	 * Converts the log levels string to an array of log levels
	 * 
	 * @param string $inLogLevels
	 * @return array
	 */
	private function _buildLogLevels($inLogLevels) {
		$return = array();
		$logLevels = explode(',', $inLogLevels);
		if ( !is_array($logLevels) ) {
			$logLevels = array();
		}
		
		foreach ( $logLevels as $logLevel ) {
			if ( systemLogLevel::convertLogLevelToString($logLevel) != 'CUSTOM' ) {
				$return[] = $logLevel;
			}
		}
		
		return $return;
	}
	
	/**
	 * Builds the report based on the modules and options
	 * @param string $inModules
	 * @param array $inDates
	 * @param array $inLogLevels
	 * @return string
	 */
	private function _buildReport($inModules, array $inDates, array $inLogLevels) {
		$return = '';
		$modules = explode(',', $inModules);
		if ( !is_array($modules) ) {
			$modules = array();
		}
		
		foreach ( $modules as $module ) {
			$oModule = new $module();
			if ( !$oModule instanceof systemReporterInterface ) {
				throw new cliApplicationCommandException($this, "Module $module cannot be used with the system reporter command");
			}
			$oModule->setDates($inDates);
			if ( method_exists($oModule, 'setLogLevels') ) {
				$oModule->setLogLevels($inLogLevels);
			}
			$return .= $oModule->getData();
		}
		
		return $return;
	}
}