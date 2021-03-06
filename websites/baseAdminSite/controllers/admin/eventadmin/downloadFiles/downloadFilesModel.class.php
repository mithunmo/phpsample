<?php
/**
 * downloadFilesModel.class.php
 * 
 * downloadFilesModel class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category downloadFilesModel
 * @version $Rev: 11 $
 */


/**
 * downloadFilesModel class
 * 
 * Provides the "downloadFiles" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category downloadFilesModel
 */
class downloadFilesModel extends mofilmDownloadFile implements mvcDaoModelInterface {
	
	/**
	 * Stores $_SourceID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_SourceID;
	
	/**
	 * Returns $_SourceID
	 *
	 * @return integer
	 */
	function getSourceID() {
		return $this->_SourceID;
	}
	
	/**
	 * Set $_SourceID to $inSourceID
	 *
	 * @param integer $inSourceID
	 * @return downloadFilesModel
	 */
	function setSourceID($inSourceID) {
		if ( $inSourceID !== $this->_SourceID ) {
			$this->_SourceID = $inSourceID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns a list of objects, optionally from $inOffset for $inLimit
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 */
	function getObjectList($inOffset = null, $inLimit = 30, $inSourceID = null, $inFileType = null) {
		$this->setSourceID($inSourceID);
		$this->setFiletype($inFileType);
		
		return mofilmDownloadFile::listOfObjects($inOffset, $inLimit, $inSourceID, $inFileType);
	}
	
	/**
	 * Returns the object primary key value
	 *
	 * @return string
	 */
	function getPrimaryKey() {
		return parent::getPrimaryKey();
	}
	
	/**
	 * Returns total object count for this table
	 *
	 * @return integer
	 */
	function getTotalObjects() {
		$query = '
			SELECT COUNT(*) AS Count
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.downloadFiles';
		
		$where = array();
		if ( $this->getSourceID() ) {
			$query .= ' INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.downloadSources ON (downloadFiles.ID = downloadSources.downloadID)';
			$where[] = 'downloadSources.sourceID = '.dbManager::getInstance()->quote($this->getSourceID());
		}
		if ( $this->getFiletype() !== null && strlen($this->getFiletype()) > 0 ) {
			$where[] = 'downloadFiles.filetype = '.dbManager::getInstance()->quote($this->getFiletype());
		}
		
		if ( count($where) > 0 ) {
			$query .= ' WHERE '.implode(' AND ', $where);
		}
		
		$oRes = dbManager::getInstance()->query($query);
		$res = $oRes->fetch();
		if ( is_array($res) && count($res) > 0 ) {
			return $res['Count'];
		} else {
			return 0;
		}
	}
	
	/**
	 * Returns the limit needed to get to the last page of results
	 *
	 * @param integer $inLimit
	 * @return integer
	 */
	function getLastPageOffset($inLimit) {
		$total = $this->getTotalObjects();
		
		if ( $inLimit > 0 ) {
			return $inLimit*floor($total/$inLimit);
		} else {
			return 0;
		}
	}

	/**
	 * Returns a new blank object
	 *
	 * @return systemDaoInterface
	 */
	function getNewObject() {
		return new mofilmDownloadFile();
	}
	
	/**
	 * Loads an existing object with $inPrimaryKey
	 *
	 * @param string $inPrimaryKey
	 * @return systemDaoInterface
	 */
	function getExistingObject($inPrimaryKey) {
		$this->setID($inPrimaryKey);
		$this->load();
		return $this;
	}
}