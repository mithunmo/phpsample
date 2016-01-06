<?php
/**
 * dbUpdateMofilmComms
 *
 * Stored in mofilmComms.class.php
 *
 * Holds updates to the comms database
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage db
 * @category dbUpdateMofilmComms
 * @version $Rev: 300 $
 */


/**
 * dbUpdateMofilmComms
 *
 * Holds updates to the comms database
 *
 * @package scorpio
 * @subpackage db
 * @category dbUpdateMofilmComms
 */
class dbUpdateMofilmComms extends dbUpdateDefinition {

	/**
	 * Creates a new system update utility
	 *
	 * @return dbUpdateLog
	 */
	function __construct() {
		parent::__construct(system::getConfig()->getDatabase('mofilm_comms')->getParamValue());
	}

	/**
	 * Initialise our updates for this database
	 */
	function initialiseUpdates() {

	}


	/**
	 * Migrates all tables and columns to UTF-8
	 *
	 * @return void
	 */
	function migrateTablesToUTF8() {
		$query = "SELECT TABLE_SCHEMA, TABLE_NAME FROM information_schema.TABLES WHERE table_schema = '" . $this->getDbName() . "' AND table_collation != 'utf8_general_ci'";
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->execute();

		foreach ( $oStmt as $row ) {
			$alter = 'ALTER TABLE ' . $this->getDbName() . '.' . $row['TABLE_NAME'] . ' CONVERT TO CHARACTER SET utf8';
			dbManager::getInstance()->exec($alter);
		}
	}
}