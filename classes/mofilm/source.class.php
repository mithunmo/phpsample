<?php
/**
 * mofilmSource
 *
 * Stored in mofilmSource.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmSource
 * @category mofilmSource
 * @version $Rev: 241 $
 */


/**
 * mofilmSource Class
 *
 * Represents an event source and all the associated data.
 *
 * @package mofilm
 * @subpackage mofilmSource
 * @category mofilmSource
 */
class mofilmSource extends mofilmSourceBase {

	const MOFILM_PROMOTIONAL_VIDEOS = 29;
	const MOFILM_CREATIVE = 30;
	const MOFILM_GENERAL_SHORTS = 35;

	const ORDERBY_STARTDATE = 1;
	const ORDERBY_NAME = 2;
	const ORDERBY_ENDDATE = 3;
	const ORDERBY_ENDDATE_ASC = 4;
	const ORDERBY_STARTDATE_ASC = 5;
	const ORDERBY_SET_ID = 6;
	const ORDERBY_ID = 7;
	const ORDERBY_ID_ASC = 8;

	/**
	 * Container for static instances of mofilmSource
	 *
	 * @var array
	 * @access protected
	 * @static
	 */
	protected static $_Instances = array();

	/**
	 * Stores the mofilmSourceTrackSet instance
	 *
	 * @var mofilmSourceTrackSet
	 * @access protected
	 */
	protected $_TrackSet;
	
	/**
	 * Stores the mofilmSourcePrizeSet instance
	 *
	 * @var mofilmSourcePrizeSet
	 * @access protected
	 */
	protected $_PrizeSet;
	
	/**
	 * Stores the mofilmSourceDownloadFiles instance
	 *
	 * @var mofilmSourceDownloadFiles
	 * @access protected
	 */
	protected $_DownloadFiles;
	
	/**
	 * Stores the mofilmSourceDataSet instance
	 *
	 * @var mofilmSourceDataSet
	 * @access protected
	 */
	protected $_SourceDataSet;
	
	/**
	 * Stores the mofilmGrants instance
	 *
	 * @var mofilmGrants
	 * @access protected
	 */
	protected $_Grants;
	
	
	/**
	 * Creates a new mofilmSource containing non-unique properties
	 *
	 * @param integer $inEventID
	 * @param string $inName
	 * @param string $inHidden
	 * @param string $inStartDate
	 * @param string $inEndDate
	 * @param integer $inTermsID
	 * @param string $inInstructions
	 * @return mofilmSource
	 * @static
	 */
	public static function factory($inEventID = null, $inName = null, $inHidden = null, $inStartDate = null, $inEndDate = null, $inTermsID = null, $inInstructions = null) {
		$oObject = new mofilmSource;
		if ( $inEventID !== null ) {
			$oObject->setEventID($inEventID);
		}
		if ( $inName !== null ) {
			$oObject->setName($inName);
		}
		if ( $inHidden !== null ) {
			$oObject->setHidden($inHidden);
		}
		if ( $inStartDate !== null ) {
			$oObject->setStartDate($inStartDate);
		}
		if ( $inEndDate !== null ) {
			$oObject->setEndDate($inEndDate);
		}
		if ( $inTermsID !== null ) {
			$oObject->setTermsID($inTermsID);
		}
		if ( $inInstructions !== null ) {
			$oObject->setInstructions($inInstructions);
		}
		return $oObject;
	}

	/**
	 * Get an instance of mofilmSource by primary key
	 *
	 * @param integer $inID
	 * @return mofilmSource
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
		$oObject = new mofilmSource();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmSource, optionally for $inEventID
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param integer $inEventID (optional)
	 * @param array $inSources (optional) An array of source ids to load
	 * @param boolean $inVisibleOnly (optional) Set to true to list only visible sources
	 * @param integer $inOrderBy (optional) One of the order by constants
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30, $inEventID = null, array $inSources = array(), $inVisibleOnly = false, $inOrderBy = null) {
		$query = 'SELECT * FROM ' . system::getConfig()->getDatabase('mofilm_content') . '.sources ';
		
		$where = array();
		if ( $inEventID !== null && $inEventID > 0 ) {
			$where[] = 'eventID = ' . dbManager::getInstance()->quote($inEventID);
		}
		if ( count($inSources) > 0 ) {
			$where[] = 'ID IN (' . implode(',', $inSources) . ')';
		}
		if ( $inVisibleOnly === true ) {
			// $where[] = 'hidden = ' . dbManager::getInstance()->quote(self::NO_N);
		}

		if ( count($where) > 0 ) {
			$query .= ' WHERE '.implode(' AND ', $where);
		}
		
		switch ( $inOrderBy ) {
			case self::ORDERBY_STARTDATE:
				$orderBy = 'startDate DESC ';
			break;

			case self::ORDERBY_STARTDATE_ASC:
				$orderBy = 'startDate ASC ';
			break;

			case self::ORDERBY_ENDDATE:
				$orderBy = 'endDate DESC ';
			break;

			case self::ORDERBY_ENDDATE_ASC:
				$orderBy = 'endDate ASC ';
			break;

			case self::ORDERBY_SET_ID:
				if ( is_array($inSources) && count($inSources) > 0 ) {
					$orderBy = 'FIELD(ID, '.implode(',', $inSources).')';
				}
			break;

			case self::ORDERBY_ID:
				$orderBy = 'ID DESC';
			break;

			case self::ORDERBY_ID_ASC:
				$orderBy = 'ID ASC';
			break;

			default:
				$orderBy = ' name ASC ';
		}
		$query .= ' ORDER BY '.$orderBy;

		if ( $inOffset !== null ) {
			$query .= ' LIMIT ' . $inOffset . ',' . $inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmSource();
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
	 * Returns an array of objects of mofilmSource, optionally for $inEventID
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param integer $inEventID (optional)
	 * @param array $inSources (optional) An array of source ids to load
	 * @param boolean $inVisibleOnly (optional) Set to true to list only visible sources
	 * @param integer $inOrderBy (optional) One of the order by constants
	 * @return array
	 * @static
	 */
	public static function listOfDistinctSourceNameObjects() {
		$query = 'SELECT DISTINCT name FROM ' . system::getConfig()->getDatabase('mofilm_content') . '.sources WHERE';
		$query .= ' hidden = ' . dbManager::getInstance()->quote(self::NO_N);
		$query .= ' ORDER BY name ASC ';

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmSource();
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
	 * Returns an array of objects of mofilmSource, optionally for $inEventID
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param integer $inEventID (optional)
	 * @param array $inSources (optional) An array of source ids to load
	 * @param boolean $inVisibleOnly (optional) Set to true to list only visible sources
	 * @param integer $inOrderBy (optional) One of the order by constants
	 * @return array
	 * @static
	 */
	public static function listOfDistinctSourceIDsByName($inName) {
		$query = 'SELECT ID FROM ' . system::getConfig()->getDatabase('mofilm_content') . '.sources WHERE';
		$query .= ' hidden = ' . dbManager::getInstance()->quote(self::NO_N);
		$query .= ' AND name = ' . dbManager::getInstance()->quote($inName);
		$query .= ' ORDER BY name ASC ';

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmSource();
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
	 * Saves changes to the object
	 *
	 * @return boolean
	 */
	function save() {
		$return = true;
		if ( $this->isModified() ) {
			$return = parent::save() && $return;

			if ( $this->_SourceDataSet instanceof mofilmSourceDataSet ) {
				$this->_SourceDataSet->setSourceID($this->getID());
				$return = $this->_SourceDataSet->save() && $return;
			}
			
			if ( $this->_PrizeSet instanceof mofilmSourcePrizeSet ) {
				$this->_PrizeSet->setSourceID($this->getID());
				$return = $this->_PrizeSet->save() && $return;
			}
			
			if ( $this->_TrackSet instanceof mofilmSourceTrackSet ) {
				$this->_TrackSet->setSourceID($this->getID());
				$return = $this->_TrackSet->save() && $return;
			}
			
			if ( $this->_DownloadFiles ) {
				$return = $this->saveDownloadFiles() && $return;
			}
			
			if ( $this->_Grants ) {
				$return = $this->saveGrants() && $return;
			}
			$return = true;
		}
		return $return;
	}

	/**
	 * Deletes the object and related objects
	 *
	 * @return boolean
	 */
	function delete() {
		$return = false;
		if ( $this->getID() ) {
			$this->getTrackSet()->delete();
			$return = parent::delete();
		}
		return $return;
	}

	/**
	 * Resets object properties to defaults
	 *
	 * @return mofilmSource
	 */
	function reset() {
		$this->_TrackSet = null;
		$this->_SourceDataSet = null;

		parent::reset();
		return $this;
	}



	/**
	 * Returns true if object or sub-objects have been modified
	 *
	 * @return boolean
	 */
	function isModified() {
		$modified = $this->_Modified;
		if ( $this->_TrackSet instanceof mofilmSourceTrackSet ) {
			$modified = $this->_TrackSet->isModified() || $modified;
		}
		if ( !$modified && $this->_SourceDataSet instanceof mofilmSourceDataSet ) {
			$modified = $this->_SourceDataSet->isModified() || $modified;
		}
		return $modified;
	}

	/**
	 * Returns an instance of mofilmSourceTrackSet, which is lazy loaded upon request
	 *
	 * @return mofilmSourceTrackSet
	 */
	function getTrackSet() {
		if ( !$this->_TrackSet instanceof mofilmSourceTrackSet ) {
			$this->_TrackSet = new mofilmSourceTrackSet($this->getID());
		}
		return $this->_TrackSet;
	}

	/**
	 * Sets the track set object to the movie
	 *
	 * @param mofilmSourceTrackSet $inObject
	 * @return mofilmSource
	 */
	function setTrackSet(mofilmSourceTrackSet $inObject) {
		$this->_TrackSet = $inObject;
		return $this;
	}
	
	/**
	 * Returns an instance of mofilmSourcePrizeSet, which is lazy loaded upon request
	 *
	 * @return mofilmSourcePrizeSet
	 */
	function getPrizeSet() {
		if ( !$this->_PrizeSet instanceof mofilmSourcePrizeSet ) {
			$this->_PrizeSet = new mofilmSourcePrizeSet($this->getID());
		}
		return $this->_PrizeSet;
	}

	/**
	 * Sets the prize set object to the movie
	 *
	 * @param mofilmSourcePrizeSet $inObject
	 * @return mofilmSource
	 */
	function setPrizeSet(mofilmSourcePrizeSet $inObject) {
		$this->_PrizeSet = $inObject;
		return $this;
	}
	
	/**
	 * Return value of $_SourceDataSet
	 *
	 * @return mofilmSourceDataSet
	 * @access public
	 */
	function getSourceDataSet() {
		if ( !$this->_SourceDataSet instanceof mofilmSourceDataSet ) {
			$this->_SourceDataSet = new mofilmSourceDataSet($this->getID(), 'en');
		}
		return $this->_SourceDataSet;
	}

	/**
	 * Set a pre-loaded event data set to the event object
	 *
	 * @param mofilmSourceDataSet $inSourceDataSet
	 * @return mofilmSource
	 * @access public
	 */
	function setSourceDataSet(mofilmSourceDataSet $inSourceDataSet) {
		if ( $inSourceDataSet !== $this->_SourceDataSet ) {
			$this->_SourceDataSet = $inSourceDataSet;
		}
		return $this;
	}
	
	/**
	 * Return value of $_SourceDownloadFiles
	 *
	 * @return mofilmSourcDownloadFiles
	 * @access public
	 */
	function getSourceDownloadFiles() {
		if ( !$this->_DownloadFiles instanceof mofilmDownloadFile && $this->_ID > 0 ) {
			$this->_DownloadFiles = mofilmDownloadFile::listOfObjects(null, null, $this->_ID);
		}
		return $this->_DownloadFiles;
	}

	/**
	 * Sets the sourcedownloadfiles set object
	 *
	 * @param mofilmSourceDownloadFiles $inObject
	 * @return mofilmSource
	 */
	function setSourceDownloadFileSet($inObject = null) {
		$this->_DownloadFiles = $inObject;
		$this->setModified(true);
		return $this;
	}

	/**
	 * Sets the grants set object
	 *
	 * @param mofilmGrants $inObject
	 * @return mofilmSource
	 */
	function setGrantsSet($inObject = null) {
		$this->_Grants = $inObject;
		$this->setModified(true);
		return $this;
	}

	/**
	 * Returns an instance of mofilmGrants, which is lazy loaded upon request
	 *
	 * @return mofilmGrants
	 */
	function saveGrants() {
		if ( $this->_Grants['ID'] > 0 ) {
			$oGrants = new mofilmGrants($this->_Grants['ID']);
		} else {
			$oGrants = new mofilmGrants();
		}
		
		$oGrants->setSourceID($this->getID());
		$oGrants->setEndDate($this->_Grants['EndDate']);
		$oGrants->setCurrencySymbol($this->_Grants['CurrencySymbol']);
		$oGrants->setTotalGrants($this->_Grants['TotalGrants']);
		$oGrants->setDescription($this->_Grants['Description']);
		$oGrants->save();
		return true;
	}
	
	/**
	 * Saves 
	 *
	 * @return Boolean
	 */
	function saveDownloadFiles() {
            foreach ( $this->_DownloadFiles as $oDownloadFile ){
		$oDownloadFile->getSourceSet()->setObject(mofilmSource::getInstance($this->_ID));
		$oDownloadFile->save();
            }
		return true;
	}
	
	/**
	 * Returns an array of available source statuses
	 *
	 * @return array
	 * @static
	 */
	static function getAvailableSourceStatuses() {
		return array(
			mofilmSourceBase::SOURCE_STATUS_DRAFT,
			mofilmSourceBase::SOURCE_STATUS_PUBLISHED,
			mofilmSourceBase::SOURCE_STATUS_CLOSED,
			mofilmSourceBase::SOURCE_STATUS_JUDGING,
			mofilmSourceBase::SOURCE_STATUS_WINNERS,
		);
	}
}