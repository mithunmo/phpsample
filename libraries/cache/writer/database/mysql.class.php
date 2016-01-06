<?php
/**
 * cacheWriterDatabaseMysql class
 * 
 * Stored in mysql.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cache
 * @category cacheWriterDatabaseMysql
 * @version $Rev: 650 $
 */


/**
 * cacheWriterDatabaseMysql
 * 
 * This class requires a table with the following schema:
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
 * Note: there are some small differences between this writer and the file
 * writer. Using isCached() and isExpired() will automatically trigger a load from
 * the database. If not, each would require a separate query; a select 1 from...,
 * select (now()-unix_timestamp(update_time))>lifetime from...) and a select.
 * 
 * @link cacheWriterDatabase
 * @package scorpio
 * @subpackage cache
 * @category cacheWriterDatabaseMysql
 */
class cacheWriterDatabaseMysql extends cacheWriterDatabase {
	
	/**
	 * Removes all cached records from $inCacheDatabase.$inCacheTable, returns number of rows affected
	 *
	 * @param string $inCacheDatabase
	 * @param string $inCacheTable
	 * @return integer
	 * @throws cacheWriterDatabaseCacheDatabaseNotSetException
	 * @throws cacheWriterDatabaseCacheTableNotSetException
	 * @throws cacheWriterDatabaseProtectedDatabaseException
	 * @static
	 */
	static function clearCache($inCacheDatabase, $inCacheTable) {
		if ( !$inCacheDatabase ) {
			throw new cacheWriterDatabaseCacheDatabaseNotSetException();
		}
		if ( !$inCacheTable ) {
			throw new cacheWriterDatabaseCacheTableNotSetException();
		}
		if ( in_array(strtolower($inCacheDatabase), array('mysql',' information_schema', strtolower(system::getConfig()->getDatabase('system')))) ) {
			throw new cacheWriterDatabaseProtectedDatabaseException($inCacheDatabase);
		}
		
		return dbManager::getInstance()->exec("TRUNCATE $inCacheDatabase.$inCacheTable");
	}
	
	
	
	/**
	 * @see cacheWriter::delete()
	 */
	function delete() {
		if ( $this->getCacheId() ) {
			$oStmt = dbManager::getInstance()->prepare("DELETE FROM {$this->getCacheDatabase()}.{$this->getCacheTable()} WHERE cacheId = :CacheId LIMIT 1");
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
		$return = false;
		if ( $this->getCacheId() ) {
			$oStmt = dbManager::getInstance()->prepare("SELECT cacheId, data, createDate, updateDate, lifetime FROM {$this->getCacheDatabase()}.{$this->getCacheTable()} WHERE cacheId = :CacheId");
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
		$return = false;
		if ( $this->isModified() ) {
			if ( $this->_Modified ) {
				$this->setUpdateDate(date(system::getConfig()->getDatabaseDatetimeFormat()));
				
				$oStmt = dbManager::getInstance()->prepare("
					INSERT INTO {$this->getCacheDatabase()}.{$this->getCacheTable()}
						(cacheId, data, createDate, updateDate, lifetime)
					VALUES
						(:CacheId, :Data, :CreateDate, :UpdateDate, :Lifetime)
					ON DUPLICATE KEY UPDATE
						data = VALUES(data),
						updateDate = VALUES(updateDate),
						lifetime = VALUES(lifetime)");
				
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
		$oStmt = dbManager::getInstance()->prepare("DELETE FROM {$this->getCacheDatabase()}.{$this->getCacheTable()} WHERE ((UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(updateDate))>lifetime)");
		$oStmt->execute();
		$res = $oStmt->rowCount();
		$oStmt->closeCursor();
		return ($res>0);
	}
}