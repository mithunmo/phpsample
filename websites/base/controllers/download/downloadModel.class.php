<?php
/**
 * downloadModel.class.php
 * 
 * downloadModel class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_base
 * @subpackage controllers
 * @category downloadModel
 * @version $Rev: 11 $
 */


/**
 * downloadModel class
 * 
 * Provides the "download" page
 * 
 * @package websites_base
 * @subpackage controllers
 * @category downloadModel
 */
class downloadModel extends mvcModelBase {
	
	/**
	 * Stores $_File
	 *
	 * @var mofilmDownloadFile
	 * @access protected
	 */
	protected $_File;
	
	
	
	/**
	 * @see mvcModelBase::__construct()
	 */
	function __construct() {
		parent::__construct();
	}
	
	/**
	 * Records the stats for this download
	 * 
	 * @param mofilmDownloadFile $inFile
	 * @param integer $inUserID
	 * @return void
	 */
	function logStats(mofilmDownloadFile $inFile, $inUserID) {
		try {
			$oStat = new mofilmUserDownload();
			$oStat->setCountry(mofilmUtilities::getCountryFromIpAddress());
			$oStat->setDownloadID($inFile->getID());
			$oStat->setIp($_SERVER['REMOTE_ADDR']);
			$oStat->setUserID($inUserID);
			
			if ( $inFile->getDownloadHash() ) {
				$oSource = $inFile->getSourceSet()->getObjectByHash($inFile->getDownloadHash());
				if ( $oSource instanceof mofilmSource ) {
					$oStat->setSourceID($oSource->getID());
					$oStat->setEventID($oSource->getEventID());
				}
			}
			
			$oStat->save();
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
		}
	}

	/**
	 * Returns the file, fetching it if $inAction is supplied
	 *
	 * @param string $inFileID
	 * @return mofilmDownloadFile
	 */
	function getFile($inFileID = null) {
		if ( !$this->_File instanceof mofilmDownloadFile || $inFileID !== null ) {
			if ( $inFileID !== null ) {
				if ( is_numeric($inFileID) && strlen($inFileID) < 5 ) {
					$oFile = mofilmDownloadFile::getInstance($inFileID);
				} else {
					$oFile = mofilmDownloadFile::getInstanceByHash($inFileID);
				}
				
				if ( $oFile->getID() > 0 ) {
					$this->_File = $oFile;
				}
			}
		}
		return $this->_File;
	}
	
	/**
	 * Set $_File to $inFile
	 *
	 * @param mofilmDownloadFile $inFile
	 * @return downloadModel
	 */
	function setFile(mofilmDownloadFile $inFile) {
		if ( $inFile !== $this->_File ) {
			$this->_File = $inFile;
			$this->setModified();
		}
		return $this;
	}
}