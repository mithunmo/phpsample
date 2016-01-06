<?php
/**
 * dbUpdateCommandStatus Class
 * 
 * Stored in dbUpdateCommandStatus.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage db
 * @category dbUpdateCommandStatus
 * @version $Rev: 650 $
 */


/**
 * dbUpdateCommandStatus class
 * 
 * Shows the current database update status.
 *
 * @package scorpio
 * @subpackage db
 * @category dbUpdateCommandStatus
 */
class dbUpdateCommandStatus extends cliCommand {
	
	/**
	 * Creates a new command
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct($inRequest, 'status', null, 's');
		
		$this->setCommandHelp('Shows the current database status');
		$this->setCommandRequiresValue(false);
		$this->setHaltAppAfterExecute(false);
	}
	
	/**
	 * Executes the command
	 *
	 * @return void
	 */
	function execute() {
		$oDbManager = dbUpdateManager::getInstance();
		$arrStatus = $oDbManager->getUpdateStatus();
		
		$this->getRequest()->getApplication()->getResponse()
			->addResponse("\n".$this->getRequest()->getApplication()->getApplicationName())
			->addResponse("Running status check across all registered database update components.")
			->addResponse("This report has been formatted for a character width of 120 characters.\n")
			->addResponse(cliConsoleTools::cliDataPrint($arrStatus, null, 110));
	}
}