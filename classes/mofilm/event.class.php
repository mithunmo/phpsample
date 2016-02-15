<?php
/**
 * mofilmEvent
 * 
 * Stored in mofilmEvent.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmEvent
 * @category mofilmEvent
 * @version $Rev: 20 $
 */


/**
 * mofilmEvent Class
 * 
 * Provides access to records in mofilm_content.events
 * 
 * Creating a new record:
 * <code>
 * $oMofilmEvent = new mofilmEvent();
 * $oMofilmEvent->setID($inID);
 * $oMofilmEvent->setName($inName);
 * $oMofilmEvent->setWebpath($inWebpath);
 * $oMofilmEvent->setStartdate($inStartdate);
 * $oMofilmEvent->setEnddate($inEnddate);
 * $oMofilmEvent->setAwardStartdate($inAwardStartdate);
 * $oMofilmEvent->setAwardEnddate($inAwardEnddate);
 * $oMofilmEvent->setTermsID($inTermsID);
 * $oMofilmEvent->setInstructions($inInstructions);
 * $oMofilmEvent->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmEvent = new mofilmEvent($inID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oMofilmEvent = new mofilmEvent();
 * $oMofilmEvent->setID($inID);
 * $oMofilmEvent->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oMofilmEvent = mofilmEvent::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package mofilm
 * @subpackage mofilmEvent
 * @category mofilmEvent
 */
class mofilmEvent extends mofilmEventBase {
	
	const EVENT_MOFILM = 4;
	const EVENT_MINIMO = 11;
	
	const ORDERBY_STARTDATE = 1;
	const ORDERBY_NAME = 2;
	const ORDERBY_ENDDATE = 3;
	const ORDERBY_ENDDATE_ASC = 4;
	const ORDERBY_STARTDATE_ASC = 5;
	const ORDERBY_SET_ID = 6;
	
	/**
	 * Container for static instances of mofilmEvent
	 * 
	 * @var array
	 * @access protected
	 * @static 
	 */
	protected static $_Instances = array();

	/**
	 * Stores $_SourceSet
	 *
	 * @var mofilmEventSourceSet
	 * @access protected
	 */
	protected $_SourceSet;
	
	/**
	 * Stores $_EventDataSet
	 *
	 * @var mofilmEventDataSet
	 * @access protected
	 */
	protected $_EventDataSet;
	
	/**
	 * Stores the stats object
	 * 
	 * @var mofilmEventStats
	 * @access protected
	 */
	protected $_Stats;

	
	
	/**
	 * Creates a new mofilmEvent containing non-unique properties
	 * 
	 * @param string $inName
	 * @param string $inWebpath
	 * @param datetime $inStartdate
	 * @param datetime $inEnddate
	 * @param integer $inTermsID
	 * @param string $inInstructions
	 * @return mofilmEvent
	 * @static 
	 */
	public static function factory($inName = null, $inWebpath = null, $inStartdate = null, $inEnddate = null, $inTermsID = null, $inInstructions = null) {
		$oObject = new mofilmEvent;
		if ( $inName !== null ) {
			$oObject->setName($inName);
		}
		if ( $inWebpath !== null ) {
			$oObject->setWebpath($inWebpath);
		}
		if ( $inStartdate !== null ) {
			$oObject->setStartdate($inStartdate);
		}
		if ( $inEnddate !== null ) {
			$oObject->setEnddate($inEnddate);
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
	 * Get an instance of mofilmEvent by primary key
	 * 
	 * @param integer $inID
	 * @return mofilmEvent
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
		$oObject = new mofilmEvent();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}
				
	/**
	 * Returns an array of objects of mofilmEvent, if $inActiveOnly is true, automatically orders by startdate
	 * 
	 * If an array of event IDs is supplied, only those events will be selected.
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param boolean $inActiveOnly (optional) true to return only active events
	 * @param integer $inOrderBy (optional) order by a field constant
	 * @param array $inEvents (optional) only fetch these events
	 * @param array $inExclude (optional) exclude these events
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30, $inActiveOnly = null, $inOrderBy = null, $inEvents = array(), $inExclude = array(),$params = array()      ) {
           
            if($params['CorporateID'] != '' || $params['BrandID'] != ''){
                       $query = '
                               SELECT DISTINCT(events.ID), events.name FROM '.system::getConfig()->getDatabase('mofilm_content').'.events
                               JOIN '.system::getConfig()->getDatabase('mofilm_content').'.sources
                               ON sources.eventID = events.ID   
                               JOIN '.system::getConfig()->getDatabase('mofilm_content').'.brands
                               ON sources.brandID = brands.ID
                               WHERE productID != 2 ';
                                if($params['CorporateID'] != '0' && $params['CorporateID'] != ''){
                                    $query .= ' AND brands.corporateID = '.$params['CorporateID'];
                                }
                                if($params['BrandID'] != '0' && $params['BrandID'] != ''){
                                    $query .= ' AND brands.ID = '.$params['BrandID'];
                                }
                }else{
                    $query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.events';

                    //$where = array("productID != 2");
                    
                    if($params['eventEndDateFilter'] != ''){
                        $where[]  = ' enddate > "'.$params['eventEndDateFilter'].'" ';
                    }else{
                        if ( $inActiveOnly === true ) {
                                $now = dbManager::getInstance()->quote(date(system::getConfig()->getDatabaseDatetimeFormat()));
                                $inOrderBy = self::ORDERBY_STARTDATE;
                                $where[] = '(startdate <= '.$now.' OR startdate = "0000-00-00 00:00:00") AND (enddate > '.$now.' OR enddate = "0000-00-00 00:00:00")';
                        }
                    }

                        if ( $inActiveOnly === true ) {
                                $now = dbManager::getInstance()->quote(date(system::getConfig()->getDatabaseDatetimeFormat()));
                                $inOrderBy = self::ORDERBY_STARTDATE;
                                $where[] = '(startdate <= '.$now.' OR startdate = "0000-00-00 00:00:00") AND (enddate > '.$now.' OR enddate = "0000-00-00 00:00:00")';
                        }
                        
                        if ( $params['ProductID'] ){
                            $where[] = ' productID = ' . $params["ProductID"];
                        }
                    
                    
                    if ( count($inEvents) > 0 ) {
                            $where[] = 'ID IN ('.implode(',', $inEvents).')';
                    }
                    if ( count($inExclude) > 0 ) {
                            $where[] = 'ID NOT IN ('.implode(',', $inExclude).')';
                    }

                    if ( count($where) > 0 ) {
                            $query .= ' WHERE '.implode(' AND ', $where);
                    }

                }
                    if(isset($params['OrderBy'])  && $params['OrderBy'] != '0'){
                     $inOrderBy = 0;
                 }
                   switch ( $inOrderBy ) {
                            case self::ORDERBY_STARTDATE:
                                    $orderBy = ' events.startdate DESC ';
                            break;

                            case self::ORDERBY_STARTDATE_ASC:
                                    $orderBy = ' events.startdate ASC ';
                            break;

                            case self::ORDERBY_ENDDATE:
                                    $orderBy = ' events.enddate DESC ';
                            break;

                            case self::ORDERBY_ENDDATE_ASC:
                                    $orderBy = ' events.enddate ASC ';
                            break;

                            case self::ORDERBY_SET_ID:
                                    if ( is_array($inEvents) && count($inEvents) > 0 ) {
                                            $orderBy = 'FIELD(ID, '.implode(',', $inEvents).')';
                                    }
                            break;

                            default:
                                    $orderBy = ' events.name ASC ';
                         }

                  $query .= ' ORDER BY '.$orderBy;

		if ( isset($inOffset) ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
                systemLog::message($query);
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmEvent();
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

			if ( $this->_SourceSet instanceof mofilmEventSourceSet ) {
				$this->_SourceSet->setEventID($this->getID());
				$return = $this->_SourceSet->save() && $return;
			}
			
			if ( $this->_EventDataSet instanceof mofilmEventDataSet ) {
				$this->_EventDataSet->setEventID($this->getID());
				$return = $this->_EventDataSet->save() && $return;
			}
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
			$this->getSourceSet()->delete();
			$return = parent::delete();
		}
		return $return;
	}
	
	/**
	 * Resets object properties to defaults
	 * 
	 * @return mofilmEvent
	 */
	function reset() {
		$this->_SourceSet = null;
		$this->_EventDataSet = null;
		$this->_Stats = null;
		return parent::reset();
	}
	


	/**
	 * Returns true if object or sub-objects have been modified
	 * 
	 * @return boolean
	 */
	function isModified() {
		$modified = $this->_Modified;
		if ( !$modified && $this->_SourceSet instanceof mofilmEventSourceSet ) {
			$modified = $this->_SourceSet->isModified() || $modified;
		}
		if ( !$modified && $this->_EventDataSet instanceof mofilmEventDataSet ) {
			$modified = $this->_EventDataSet->isModified() || $modified;
		}
		return $modified;
	}

	/**
	 * Return value of $_SourceSet
	 *
	 * @return mofilmEventSourceSet
	 * @access public
	 */
	function getSourceSet() {
		if ( !$this->_SourceSet instanceof mofilmEventSourceSet ) {
			$this->_SourceSet = new mofilmEventSourceSet($this->getID());
		}
		return $this->_SourceSet;
	}

	/**
	 * Set a pre-loaded source set to the event object
	 *
	 * @param mofilmEventSourceSet $inSourceSet
	 * @return mofilmEvent
	 * @access public
	 */
	function setSourceSet(mofilmEventSourceSet $inSourceSet) {
		if ( $inSourceSet !== $this->_SourceSet ) {
			$this->_SourceSet = $inSourceSet;
		}
		return $this;
	}
	
	/**
	 * Return value of $_EventDataSet
	 *
	 * @return mofilmEventDataSet
	 * @access public
	 */
	function getEventDataSet() {
		if ( !$this->_EventDataSet instanceof mofilmEventDataSet ) {
			$this->_EventDataSet = new mofilmEventDataSet($this->getID(), 'en');
		}
		return $this->_EventDataSet;
	}

	/**
	 * Set a pre-loaded event data set to the event object
	 *
	 * @param mofilmEventDataSet $inEventDataSet
	 * @return mofilmEvent
	 * @access public
	 */
	function setEventDataSet(mofilmEventDataSet $inEventDataSet) {
		if ( $inEventDataSet !== $this->_EventDataSet ) {
			$this->_EventDataSet = $inEventDataSet;
		}
		return $this;
	}
	
	/**
	 * Loads the event stats based on the current user profile
	 * 
	 * @param mofilmUser $inUser
	 * @return mofilmEventStats
	 */
	function getStats($inUser = null) {
		if ( !$this->_Stats instanceof mofilmEventStats ) {
			$this->_Stats = new mofilmEventStats($this, $inUser);
			if ( $this->getID() ) {
				$this->_Stats->load();
			}
		}
		return $this->_Stats;
	}
	
	/**
	 * Set a pre-loaded stats object
	 * 
	 * @param mofilmEventStats $inStats
	 * @return mofilmEvent
	 */
	function setStats(mofilmEventStats $inStats) {
		$this->_Stats = $inStats;
		return $this;
	}
	
	/**
	 * Returns an array of available event statuses
	 *
	 * @return array
	 * @static
	 */
	static function getAvailableEventStatuses() {                
		return array(
			mofilmEventBase::STATUS_DRAFT,
			mofilmEventBase::STATUS_PUBLISHED,
			mofilmEventBase::STATUS_CLOSED_CANCELED,
			mofilmEventBase::STATUS_JUDGING,
			mofilmEventBase::STATUS_WINNERS,
		);
	}
        
	static function getAvailableEventStatusNonMarquee() {                
		return array(
			mofilmEventBase::STATUS_DRAFT,
			mofilmEventBase::STATUS_PUBLISHED,
			mofilmEventBase::STATUS_CLOSED_COMPLETED,
            mofilmEventBase::STATUS_CLOSED_CANCELED,
		);
	}
        
}