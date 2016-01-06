<?php
/**
 * mofilmTrack
 * 
 * Stored in mofilmTrack.class.php
 * 
 * @author Dave Redfern
 * @copyright Momusic (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmTrack
 * @category mofilmTrack
 * @version $Rev: 10 $
 */


/**
 * mofilmTrack Class
 * 
 * Provides a wrapper around the mofilmDownloadFile to make it look like
 * a track with direct calls into the parameters.
 * 
 * @package mofilm
 * @subpackage mofilmTrack
 * @category mofilmTrack
 */
class mofilmTrack extends mofilmDownloadFile {
	
	const PARAM_SUPPLIER_ID = 'SupplierID';
	const PARAM_EXTERNAL_REF = 'ExternalReference';
	const PARAM_ARTIST = 'Artist';
	const PARAM_TITLE = 'Title';
	const PARAM_DIGITAL_ISRC = 'DigitalISRC';
	const PARAM_CREATE_DATE = 'CreateDate';
	

	/**
	 * Creates a new mofilmTrack containing non-unique properties
	 * 
	 * @param datetime $inModified
	 * @param string $inDescription
	 * @param string $inFiletype
	 * @param string $inFilename
	 * @return mofilmTrack
	 * @static 
	 */
	public static function factory($inModified = null, $inDescription = null, $inFiletype = null, $inFilename = null) {
		$oObject = new self();
		if ( $inModified !== null ) {
			$oObject->setDateModified($inModified);
		}
		if ( $inDescription !== null ) {
			$oObject->setDescription($inDescription);
		}
		if ( $inFiletype !== null ) {
			$oObject->setFiletype($inFiletype);
		}
		if ( $inFilename !== null ) {
			$oObject->setFilename($inFilename);
		}
		return $oObject;
	}
	
	/**
	 * Get an instance of mofilmTrack by primary key
	 * 
	 * @param integer $inID
	 * @return mofilmTrack
	 * @static 
	 */
	public static function getInstance($inID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inID]) ) {
			return self::$_Instances[$inID];
		}
		
		/**
		 * No instance, create one
		 */
		$oObject = new self();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}
			
	/**
	 * Returns an array of objects of mofilmTrack
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param integer $inSourceID
	 * @param integer $inSupplierID
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30, $inSourceID = null, $inSupplierID = null) {
		$query = 'SELECT downloadFiles.* FROM '.system::getConfig()->getDatabase('mofilm_content').'.downloadFiles';
		$where = array('downloadFiles.filetype = "music"');
		
		if ( $inSourceID !== null && $inSourceID > 0 ) {
			$query .= ' INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.downloadSources ON (downloadFiles.ID = downloadSources.downloadID)';
			$where[] = 'downloadSources.sourceID = '.dbManager::getInstance()->quote($inSourceID);
		}
		if ( $inSupplierID !== null && $inSupplierID > 0 ) {
			$query .= ' INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.downloadData ON (downloadFiles.ID = downloadData.downloadID)';
			$where[] = 'downloadData.dataName = "SupplierID" AND downloadData.dataValue = '.dbManager::getInstance()->quote($inSupplierID);
		}
		
		if ( count($where) > 0 ) {
			$query .= ' WHERE '.implode(' AND ', $where);
		}
		
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmTrack();
					$oObject->loadFromArray($row);
					$list[] = $oObject;
				}
			}
			$oStmt->closeCursor();
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			throw $e;
		}
		return $list;
	}
	
	
	
	/**
	 * Return value of $_SupplierID
	 * 
	 * @return integer
	 * @access public
	 */
	function getSupplierID() {
		return $this->getParamSet()->getParam(self::PARAM_SUPPLIER_ID);
	}
	
	/**
	 * Returns the supplier object
	 * 
	 * @return mofilmSupplier
	 */
	function getSupplier() {
		return mofilmSupplier::getInstance($this->getSupplierID());
	}
	
	/**
	 * Set $_SupplierID to SupplierID
	 * 
	 * @param integer $inSupplierID
	 * @return mofilmTrack
	 * @access public
	 */
	function setSupplierID($inSupplierID) {
		$this->getParamSet()->setParam(self::PARAM_SUPPLIER_ID, $inSupplierID);
		return $this;
	}
	
	/**
	 * Return value of $_ExternalReference
	 * 
	 * @return string
	 * @access public
	 */
	function getExternalReference() {
		return $this->getParamSet()->getParam(self::PARAM_EXTERNAL_REF);
	}
	
	/**
	 * Set $_ExternalReference to ExternalReference
	 * 
	 * @param string $inExternalReference
	 * @return mofilmTrack
	 * @access public
	 */
	function setExternalReference($inExternalReference) {
		$this->getParamSet()->setParam(self::PARAM_EXTERNAL_REF, $inExternalReference);
		return $this;
	}
	
	/**
	 * Return value of $_Artist
	 * 
	 * @return string
	 * @access public
	 */
	function getArtist() {
		return $this->getParamSet()->getParam(self::PARAM_ARTIST);
	}
	
	/**
	 * Set $_Artist to Artist
	 * 
	 * @param string $inArtist
	 * @return mofilmTrack
	 * @access public
	 */
	function setArtist($inArtist) {
		$this->getParamSet()->setParam(self::PARAM_ARTIST, $inArtist);
		return $this;
	}
	
	/**
	 * Return value of $_Title
	 * 
	 * @return string
	 * @access public
	 */
	function getTitle() {
		if ( $this->getParamSet()->getParam(self::PARAM_TITLE) ) {
			return $this->getParamSet()->getParam(self::PARAM_TITLE);
		} else {
			return $this->getDescription();
		}
	}
	
	/**
	 * Set $_Title to Title
	 * 
	 * @param string $inTitle
	 * @return mofilmTrack
	 * @access public
	 */
	function setTitle($inTitle) {
		$this->getParamSet()->setParam(self::PARAM_TITLE, $inTitle);
		return $this;
	}
	
	/**
	 * Return value of $_DigitalISRC
	 * 
	 * @return string
	 * @access public
	 */
	function getDigitalISRC() {
		return $this->getParamSet()->getParam(self::PARAM_DIGITAL_ISRC);
	}
	
	/**
	 * Set $_DigitalISRC to DigitalISRC
	 * 
	 * @param string $inDigitalISRC
	 * @return mofilmTrack
	 * @access public
	 */
	function setDigitalISRC($inDigitalISRC) {
		$this->getParamSet()->setParam(self::PARAM_DIGITAL_ISRC, $inDigitalISRC);
		return $this;
	}
	
	/**
	 * Return value of $_CreateDate
	 * 
	 * @return datetime
	 * @access public
	 */
	function getCreateDate() {
		return $this->getParamSet()->getParam(self::PARAM_CREATE_DATE);
	}
	
	/**
	 * Set $_CreateDate to CreateDate
	 * 
	 * @param datetime $inCreateDate
	 * @return mofilmTrack
	 * @access public
	 */
	function setCreateDate($inCreateDate) {
		$this->getParamSet()->setParam(self::PARAM_CREATE_DATE, $inCreateDate);
		return $this;
	}
	
	/**
	 * Return value of $_UpdateDate
	 * 
	 * @return datetime
	 * @access public
	 */
	function getUpdateDate() {
		return $this->getDateModified();
	}
	
	/**
	 * Set $_UpdateDate to UpdateDate
	 * 
	 * @param datetime $inUpdateDate
	 * @return mofilmTrack
	 * @access public
	 */
	function setUpdateDate($inUpdateDate) {
		return $this->setDateModified($inUpdateDate);
	}
}