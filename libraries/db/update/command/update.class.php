<?php
/**
 * dbUpdateCommandUpdate Class
 * 
 * Stored in dbUpdateCommandUpdate.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage db
 * @category dbUpdateCommandUpdate
 * @version $Rev: 707 $
 */


/**
 * dbUpdateCommandUpdate class
 * 
 * Attempts to apply outstanding database updates - but only if commit has been set,
 * otherwise runs a test execution of the updates.
 *
 * @package scorpio
 * @subpackage db
 * @category dbUpdateCommandUpdate
 */
class dbUpdateCommandUpdate extends cliCommand {
	
	/**
	 * Creates a new command
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct($inRequest, 'update');
		
		$this->setCommandHelp(
			'Attempts to apply outstanding updates but does not commit any changes unless '.
			'update is set to commit. e.g. requires --update runs test update, --update=commit '.
			'will apply outstanding updates'
		);
		$this->setCommandRequiresValue(true);
	}
	
	/**
	 * Executes the command
	 *
	 * @return void
	 */
	function execute() {
		if ( $this->getRequest()->getParam('update') === 'commit' ) {
			$this->redirect('status');
			$commit = true;
		} else {
			$commit = false;
		}
		
		$oDbManager = dbUpdateManager::getInstance();
		$oDbManager->update($commit);
		$oDbManager->setHaltOnError(($this->getRequest()->getParam('disable-halt-onerror')===true ? true : false));
		
		if ( false ) $oReport = new dbUpdateReport();
		foreach ( $oDbManager->getUpdateReport() as $oReport ) {
			$report = array();
			
			if ( false ) $oLog = new dbUpdateLog();
			if ( $oReport->getCount() > 0 ) {
				foreach ( $oReport as $oLog ) {
					$rowData = explode("\n", wordwrap($oLog->getUpdateCommand(), 60, "\n", true));
					$row2Data = explode("\n", wordwrap($oLog->getMessages(), 20, "\n", true));
					$max = max(array(count($rowData), count($row2Data)));
					
					for ( $i=0; $i<$max; $i++ ) {
						if ( $i == 0 ) {
							$report[] = array(
								'LogID' => $oLog->getDbUpdateID(),
								'UpdateType' => $oLog->getUpdateType(),
								'Update' => trim($rowData[$i]),
								'Result' => $oLog->getUpdateResult(),
								'Messages' => trim($row2Data[$i]),
							);
						} else {
							$report[] = array(
								'LogID' => '',
								'UpdateType' => '',
								'Update' => (isset($rowData[$i]) ? trim($rowData[$i]) : ''),
								'Result' => '',
								'Messages' => (isset($row2Data[$i]) ? trim($row2Data[$i]) : ''),
							);
						}
					}
					$report[] = array(
						'LogID' => '',
						'UpdateType' => '',
						'Update' => '',
						'Result' => '',
						'Messages' => '',
					);
				}
			} else {
				$report[] = array(
					'LogID' => '',
					'UpdateType' => '',
					'Update' => 'No updates applied to database',
					'Result' => '',
					'Messages' => '',
				);
			}
			$this->getRequest()->getApplication()->getResponse()
				->addResponse("Update report for '{$oReport->getDatabase()}'")
				->addResponse(cliConsoleTools::cliDataPrint($report, null, 110)."\n");
		}
	}
}