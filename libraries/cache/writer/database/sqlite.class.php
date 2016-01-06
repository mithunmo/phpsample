<?php
/**
 * cacheWriterDatabaseSqlite class
 * 
 * Stored in sqlite.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cache
 * @category cacheWriterDatabaseSqlite
 * @version $Rev: 650 $
 */


/**
 * cacheWriterDatabaseSqlite
 * 
 * This class requires an SQLite database file with a table with the following schema:
 * 
 * <code>
 * CREATE TABLE `dataCache` (
 *   `cacheId` varchar(255) NOT NULL,
 *   `data` mediumtext NOT NULL,
 *   `createDate` datetime NOT NULL,
 *   `updateDate` datetime NOT NULL,
 *   `lifetime` int(10) NOT NULL default '3600',
 *   UNIQUE KEY `cacheId` (`cacheId`)
 * ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 * </code>
 * 
 * If this does not exist, it will be automatically created in the specified data file.
 * 
 * Note: there are some small differences between this writer and the file
 * writer. Using isCached() and isExpired() will automatically trigger a load from
 * the database. If not, each would require a separate query; a select 1 from...,
 * select (now()-unix_timestamp(update_time))>lifetime from...) and a select.
 * 
 * @link cacheWriterDatabase
 * @package scorpio
 * @subpackage cache
 * @category cacheWriterDatabaseSqlite
 */
class cacheWriterDatabaseSqlite extends cacheWriterDatabase {
	
	/**
	 * Holds an instance of the SQLite database connection
	 *
	 * @var dbDriverSqlite
	 * @access private
	 */
	private $_DbConnection;
	
	/**
	 * Empty the cache table in the specified database by dropping and rebuilding the index
	 *
	 * @param string $inCacheDatabase
	 * @return integer
	 * @throws cacheWriterDatabaseCacheDatabaseNotSetException
	 * @static
	 */
	static function clearCache($inCacheDatabase) {
		if ( !$inCacheDatabase ) {
			throw new cacheWriterDatabaseCacheDatabaseNotSetException();
		}
		if ( !@file_exists($inCacheDatabase) ) {
			throw new cacheWriterFileStoreDoesNotExistException($inCacheDatabase);
		}
		
		$oDb = new dbDriverSqlite(new dbOptions('sqlite://localhost/'.$inCacheDatabase.'?version=3'));
		$res = $oDb->exec('DROP TABLE IF EXISTS dataCache');
		$oDb->exec('VACUUM');
		return $res;
	}
	
	
	
	/**
	 * Destroy the SQLite connection
	 *
	 * @return void
	 */
	function __destruct() {
		$this->_DbConnection = null;
	}
	
	/**
	 * @see cacheWriter::delete()
	 */
	function delete() {
		if ( $this->getCacheId() ) {
			$oStmt = $this->getDbInstance()->prepare("DELETE FROM dataCache WHERE cacheId = :CacheId");
			$oStmt->bindValue(':CacheId', $this->getCacheId());
			$res = $oStmt->execute();
			$oStmt->closeCursor();
			return $res;
		}
		return false;
	}
	
	/**
	 * @see cacheWriter::load()
	 */
	function load() {
		$this->_checkSqliteDbStructure();
		
		$return = false;
		if ( $this->getCacheId() ) {
			$oStmt = $this->getDbInstance()->prepare("SELECT cacheId, data, createDate, updateDate, lifetime FROM dataCache WHERE cacheId = :CacheId");
			$oStmt->bindValue(':CacheId', $this->getCacheId());
			if ( $oStmt->execute() ) {
				$row = $oStmt->fetchAll();
				if ( is_array($row) && isset($row[0]) ) {
					$this->setCacheId($row[0]['cacheId']);
					$this->setCreateDate($row[0]['createDate']);
					$this->setSerialiseData($row[0]['data']);
					$this->setLifetime($row[0]['lifetime']);
					$this->setUpdateDate($row[0]['updateDate']);
					$this->setLoaded(true);
					$this->setModified(false);
					$return = true;
				}
			}
			$oStmt->closeCursor();
		}
		return $return;
	}
	
	/**
	 * @see cacheWriter::save()
	 */
	function save() {
		$this->_checkSqliteDbStructure();
		
		$return = false;
		if ( $this->isModified() ) {
			if ( $this->_Modified ) {
				$this->setUpdateDate(date(system::getConfig()->getDatabaseDatetimeFormat()));
				
				$oStmt = $this->getDbInstance()->prepare("
					INSERT OR REPLACE INTO dataCache
						(cacheId, data, createDate, updateDate, lifetime)
					VALUES
						(:CacheId, :Data, :CreateDate, :UpdateDate, :Lifetime)");
				
				$oStmt->bindValue(':CacheId', $this->getCacheId());
				$oStmt->bindValue(':Data', $this->getSerialiseData());
				$oStmt->bindValue(':CreateDate', $this->getCreateDate());
				$oStmt->bindValue(':UpdateDate', $this->getUpdateDate());
				$oStmt->bindValue(':Lifetime', $this->getLifetime());
				if ( $oStmt->execute() ) {
					$this->setModified(false);
					$return = true;
				}
				$oStmt->closeCursor();
			}
		}
		return $return;
	}
	
	/**
	 * Runs garbage collection for the current cache table
	 * 
	 * @see cacheWriter->runGc()
	 */
	function runGc() {
		$this->_checkSqliteDbStructure();
		
		$res = $this->getDbInstance()->query("DELETE FROM dataCache WHERE ((UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(updateDate))>lifetime)");
		$this->getDbInstance()->query('VACUUM');
		return ($res>0);
	}
	
	
	
	/**
	 * Returns an instance of dbDriverSqlite
	 *
	 * @return dbDriverSqlite
	 * @access private
	 */
	private function getDbInstance() {
		if ( !$this->_DbConnection instanceof dbDriverSqlite ) {
			$this->_DbConnection = new dbDriverSqlite(
				new dbOptions('sqlite://localhost/'.$this->getCacheDatabase().'?version=3')
			);
		}
		return $this->_DbConnection;
	}
	
	/**
	 * Checks and builds as necessary the Sqlite DB structure
	 *
	 * @return void
	 * @access private
	 */
	private function _checkSqliteDbStructure() {
		try {
			$res = $this->getDbInstance()->query("SELECT cacheId FROM dataCache LIMIT 1");
		} catch ( Exception $e ) {
			systemLog::warning($e->getMessage());
			
			$this->getDbInstance()->exec("
			CREATE TABLE dataCache (
			 cacheId VARCHAR( 255 ) NOT NULL ,
			 data BLOB NOT NULL ,
			 createDate DATETIME NOT NULL ,
			 updateDate DATETIME NOT NULL ,
			 lifetime INTEGER NOT NULL ,
			 PRIMARY KEY ( cacheId ) 
			)");
		}
	}
}