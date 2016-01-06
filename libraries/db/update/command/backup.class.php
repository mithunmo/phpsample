<?php
/**
 * dbUpdateCommandBackup Class
 * 
 * Stored in dbUpdateCommandBackup.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category dbUpdateCommandBackup
 * @version $Rev: 650 $
 */


/**
 * dbUpdateCommandBackup class
 * 
 * Helps create a backup by calling the registered databases utility method to
 * backup the system. Requires the databases to be specified for backup.
 *
 * @package scorpio
 * @subpackage cli
 * @category dbUpdateCommandBackup
 */
class dbUpdateCommandBackup extends cliCommand {
	
	const COMMAND = 'backup';
	const COMMAND_DBDIR = 'dbdir';
	const COMMAND_DBEXTN = 'dbext';
	const COMMAND_OUTPUT_DIR = 'output-dir';
	const COMMAND_OUTPUT_SUBFOLDERS = 'output-subfolders';
	const COMMAND_OUTPUT_USEDATE = 'output-usedate';
	const COMMAND_OUTPUT_COMPRESS = 'output-compress';
	const COMMAND_OUTPUT_COMPRESS_BG = 'output-compress-bg';
	const COMMAND_MYSQL_COMBINE = 'mysql-combine';
	const COMMAND_MYSQL_OPTIONS = 'mysql-options';
	
	/**
	 * Creates a new command
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct($inRequest, self::COMMAND,
			new cliCommandChain(
				array(
					new cliCommandNull($inRequest, '<db_name1,db_name2...>', 'The list of databases to process separated by a comma', false, false, false),
					new cliCommandNull($inRequest, self::COMMAND_DBDIR, 'Location of database files when backing up SQLite files'),
					new cliCommandNull($inRequest, self::COMMAND_DBEXTN, 'Database file extension of files in dbdir'),
					new cliCommandNull($inRequest, self::COMMAND_OUTPUT_DIR, 'Alternative output directory, defaults to %data%/backup/db'),
					new cliCommandNull($inRequest, self::COMMAND_OUTPUT_SUBFOLDERS, 'Use sub-folders when creating backup file(s)'),
					new cliCommandNull($inRequest, self::COMMAND_OUTPUT_USEDATE, 'Appends the current date and time to backup files'),
					new cliCommandNull($inRequest, self::COMMAND_OUTPUT_COMPRESS, 'Compress files using either gzip, zip or 7za. Only available on *nix platforms'),
					new cliCommandNull($inRequest, self::COMMAND_OUTPUT_COMPRESS_BG, 'Run compression as background processes NOT recommended for large databases'),
					new cliCommandNull($inRequest, self::COMMAND_MYSQL_COMBINE, 'Aggregates all databases into a single SQL dump file'),
					new cliCommandNull($inRequest, self::COMMAND_MYSQL_OPTIONS, 'Alternative mysqldump options to be passed to mysqldump, default is --opt'),
				)
			)
		);
		
		$this->setCommandHelp(
			'Creates backup files of the specified databases using the system configured database connection. '.
			'Note that this utility can only backup databases accessible via the system DSN - it is not possible '.
			'to connect to other databases. The backup uses the defined database - userScript parameter to connect '.
			'to the database (except SQLite). Please ensure that this user has sufficient access permissions to '.
			'use the driver backup options.'
		);
		$this->setCommandRequiresValue(true);
	}
	
	/**
	 * Executes the command
	 *
	 * @return void
	 */
	function execute() {
		$dbs = $this->getRequest()->getParam(self::COMMAND);
		if ( strlen($dbs) < 3 ) {
			throw new cliApplicationCommandException($this, "Please specify the database to backup");
		}
		$dbs = explode(',', $dbs);
		
		$options = array(
			dbBackup::OPTION_OUTPUT_COMPRESS_FILES => $this->getRequest()->getParam(self::COMMAND_OUTPUT_COMPRESS),
			dbBackup::OPTION_OUTPUT_COMPRESS_IN_BG => $this->getRequest()->getParam(self::COMMAND_OUTPUT_COMPRESS_BG),
			dbBackup::OPTION_OUTPUT_USE_DATE => $this->getRequest()->getParam(self::COMMAND_OUTPUT_USEDATE),
			dbBackup::OPTION_OUTPUT_USE_SUBFOLDERS => $this->getRequest()->getParam(self::COMMAND_OUTPUT_SUBFOLDERS),
			dbDriverMySqlBackup::OPTION_MYSQL_AGGREGATE_DATABASES => $this->getRequest()->getParam(self::COMMAND_MYSQL_COMBINE),
		);
		if ( strlen($this->getRequest()->getParam(self::COMMAND_DBDIR)) > 1 ) {
			$options[dbBackup::OPTION_DB_FILE_LOCATION] = $this->getRequest()->getParam(self::COMMAND_DBDIR);
		}
		if ( strlen($this->getRequest()->getParam(self::COMMAND_DBEXTN)) > 1 ) {
			$options[dbBackup::OPTION_DB_FILE_EXTENSION] = $this->getRequest()->getParam(self::COMMAND_DBEXTN);
		}
		if ( strlen($this->getRequest()->getParam(self::COMMAND_OUTPUT_DIR)) > 1 ) {
			$options[dbBackup::OPTION_OUTPUT_LOCATION] = $this->getRequest()->getParam(self::COMMAND_OUTPUT_DIR);
		}
		if ( strlen($this->getRequest()->getParam(self::COMMAND_MYSQL_OPTIONS)) > 1 ) {
			$options[dbDriverMySqlBackup::OPTION_MYSQL_OPTIONS] = $this->getRequest()->getParam(self::COMMAND_MYSQL_OPTIONS);
		}
		
		try {
			$oBackup = dbManager::getInstance()->getDbUtilities()->getDbBackup();
			$oBackup->setDatabases((array) $dbs);
			$oBackup->setOptions($options);
			$oBackup->backup();
		} catch ( Exception $e ) {
			throw new cliApplicationCommandException($this, $e->getMessage());
		}
	}
}