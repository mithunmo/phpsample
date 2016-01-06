<?php
/**
 * dbUpdateMofilmContent
 * 
 * Stored in mofilmContent.class.php
 * 
 * Holds updates to the content database
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2009
 * @package scorpio
 * @subpackage db
 * @category dbUpdateMofilmContent
 * @version $Rev: 371 $
 */


/**
 * dbUpdateMofilmContent
 * 
 * Holds updates to the content database
 *
 * @package scorpio
 * @subpackage db
 * @category dbUpdateMofilmContent
 */
class dbUpdateMofilmContent extends dbUpdateDefinition {
	
	/**
	 * Creates a new system update utility
	 *
	 * @return dbUpdateLog
	 */
	function __construct() {
		parent::__construct(system::getConfig()->getDatabase('mofilm_content')->getParamValue());
	}
	
	/**
	 * Initialise our updates for this database
	 */
	function initialiseUpdates() {

		/**
		 * Add auto-increment to various tables
		 * @author MM
		 * @date 2014-09-07
		 */
		$this->addUpdate("ALTER TABLE {$this->getDbName()}. `uploadQueue` CHANGE `status` `status` ENUM( 'Hold',
                'Queued', 'Processing', 'Sent', 'Failed', 'Archived', 'Legacy' )
                CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Queued'");   
                
                $this->addUpdate("CREATE TABLE {$this->getDbName()}.`system_logs` (
                `ID` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                `application` VARCHAR( 100 ) NOT NULL ,
                `process` VARCHAR( 100 ) NOT NULL ,
                `message` TEXT NOT NULL ,
                `criticality` INT( 1 ) NOT NULL ,
                `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP 
                ) ENGINE = MYISAM COMMENT = 'To store system error and access logs'");
                
		$this->addUpdate("UPDATE {$this->getDbName()}.`uploadQueue` set status='Legacy' where movieID < 23810 and
                status ='sent' ");

	}



	/**
	 * Migrates all tables and columns to UTF-8
	 *
	 * @return void
	 */
	function migrateTablesToUTF8() {
		$query = "SELECT TABLE_SCHEMA, TABLE_NAME FROM information_schema.TABLES WHERE table_schema = '{$this->getDbName()}' AND table_collation != 'utf8_general_ci'";
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->execute();

		foreach ( $oStmt as $row ) {
			$alter = 'ALTER TABLE '.$this->getDbName().'.'.$row['TABLE_NAME'].' CONVERT TO CHARACTER SET utf8';
			dbManager::getInstance()->exec($alter);
		}
	}	
}