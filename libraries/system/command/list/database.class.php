<?php
/**
 * systemCommandListDatabase Class
 * 
 * Stored in systemCommandListDatabase.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category systemCommandListDatabase
 * @version $Rev: 650 $
 */


/**
 * systemCommandListDatabase class
 * 
 * Lists database properties and information, either for all databases or
 * for a specific database.
 *
 * @package scorpio
 * @subpackage cli
 * @category systemCommandListDatabase
 */
class systemCommandListDatabase extends cliCommand {
	
	const COMMAND = 'database';
	const COMMAND_ALL = 'all';
	
	/**
	 * Creates a new command
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct($inRequest, self::COMMAND,
			new cliCommandChain(
				array(
					new cliCommandNull($inRequest, self::COMMAND_ALL, 'Lists all databases available in the current config', false, false),
					new cliCommandNull($inRequest, '<dbname>', 'A specific database name to view properties of', false, false),
				)
			)
		);
		
		$this->setCommandHelp('Lists available databases using the current database config.');
		$this->setCommandRequiresValue(true);
	}
	
	/**
	 * Executes the command
	 *
	 * @return void
	 */
	function execute() {
		$db = $this->getRequest()->getParam(self::COMMAND);
		if ( strlen($db) > 1 ) {
			$oDbOptions = dbManager::getInstance()->getDbOptions();
			
			$dbs = array();
			if ( $db == self::COMMAND_ALL ) {
				$dbs = dbManager::getInstance()->getDbUtilities()->showDatabases();
			} else {
				$dbs[] = $db;
			}
			
			$this->getRequest()->getApplication()->getResponse()
				->addResponse("\nThe '".$oDbOptions->getDbType()."' server located at '".$oDbOptions->getHost()."' contains the following databases:")
				->addResponse(cliConsoleTools::drawSeparator());
			
			if ( count($dbs) > 0 ) {
				foreach ( $dbs as $dbName ) {
					$this->getRequest()->getApplication()->getResponse()->addResponse("\t$dbName");
					$array2 = dbManager::getInstance()->getDbUtilities()->showTables($dbName);
					if ( count($array2) > 0 ) {
						foreach ( $array2 as $tablename ) {
							$this->getRequest()->getApplication()->getResponse()->addResponse("\t\t|-- $tablename");
						}
					}
				}
			}
		} else {
			throw new cliApplicationCommandException($this, 'Error, no action to perform');
		}
	}
}