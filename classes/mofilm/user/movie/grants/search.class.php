<?php
/**
 * mofilmUserMovieGrantsSearch
 *
 * Stored in search.class.php
 *
 * @author Pavan Kumar P G
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmUserMovieGrantsSearch
 * @category mofilmUserMovieGrantsSearch
 * @version $Rev: 371 $
 */

class mofilmUserMovieGrantsSearch extends baseSearch {

        const ORDERBY_TITLE = 'userMovieGrants.filmTitle';
	const ORDERBY_RAMT = 'userMovieGrants.requestedAmount';
        const ORDERBY_GAMT = 'userMovieGrants.grantedAmount';
        const ORDERBY_STATUS = 'userMovieGrants.status';
        const ORDERBY_NAME = 'users.firstname';
        const ORDERBY_RATING = 'userMovieGrants.rating';
                
        const ORDER_ASC = 1;
	const ORDER_DESC = 2;
        
	/**
	 * Stores $_Events
	 *
	 * @var array
	 * @access protected
	 */
	protected $_Events;

	/**
	 * Stores $_Sources
	 *
	 * @var array
	 * @access protected
	 */
	protected $_Sources;
	
	/**
	 * Stores $_UserID
	 *
	 * @var int
	 * @access protected
	 */
	protected $_UserID;
	
	/**
	 * Stores $_Offset
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_Offset;
	
	/**
	 * Stores $_Limit
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_Limit;

	/**
	 * Stores $_Status
	 *
	 * @var string (STATUS_PENDING,STATUS_APPROVED,STATUS_REJECTED,)
	 * @access protected
	 */
	protected $_Status;
	
        protected $_Rating;

        const STATUS_PENDING = 'Pending';
	const STATUS_APPROVED = 'Approved';
	const STATUS_REJECTED = 'Rejected';
	
	/**
	 * Resets object properties to defaults
	 *
	 * @return mofilmUserMovieGrants
	 */
	function reset() {
		$this->_Status = null;
                $this->_Rating = null;
		$this->_Events = array();
		$this->_Sources = array();
		$this->_UserID = 0;
                $this->_AllowedOrderBy = array(self::ORDERBY_TITLE, self::ORDERBY_RAMT, self::ORDERBY_GAMT, self::ORDERBY_STATUS, self::ORDERBY_NAME, self::ORDERBY_RATING);
		return $this;
	}
	
	/**
	 * @see baseSearch::initialise()
	 */
	function initialise() {
		parent::initialise();

		$this->addAllowedOrderBy('');
	}
	
	/**
	 * Returns an array of objects of mofilmUserMovieGrants
	 *
	 * @return array
	 */
	function search() { 
		/*
		 * Holds values to be assigned during query execution. Values do not need
		 * to be escaped because they are injected into named place-holders in the
		 * prepared query. Add items using $values[':PlaceHolder'] = $value;
  		 */
		if ( $this->canSearchRun() ) {
			$query = '';
			$this->buildSelect($query);
			$this->buildWhere($query);

			$values = array();
			$this->buildOrderBy($query);
			$this->buildLimit($query);
			
			$list = array();
			$count = 0;

			$oStmt = dbManager::getInstance()->prepare($query);

			if ( $oStmt->execute($values) ) {
				$tmp = array();
				foreach ( $oStmt as $row ) {
					$tmp[] = $row['ID'];
				}

				$count = dbManager::getInstance()->query('SELECT FOUND_ROWS() AS Results')->fetchColumn();
				if ( count($tmp) > 0 ) {
					foreach ( $tmp as $row ) {
						$list[] = mofilmUserMovieGrants::getInstance($row);
					}
				}
			}
			$oStmt->closeCursor();
			return new mofilmUserMovieGrantsSearchResult($list, $count, $this);
		}
		/*
		 * Always return empty result set
		 */
		return new mofilmUserMovieGrantsSearchResult(array(), 0, $this);
	}
	
	/**
	 * Returns an integer of total grants disbursed
	 *
	 * @return integer
	 */
	function totalGrantsDisbursed() {
		if ( $this->canSearchRun() ) {
			$query = '';
			$this->buildSelect($query);
			$this->buildWhere($query);
			
			$new_query = str_replace(
					array(
						'SQL_CALC_FOUND_ROWS userMovieGrants.id AS ID, userID, movieID, filmTitle, requestedAmount, status, grantedAmount,rating',
					),
					array(
						'sum(grantedAmount)',
					),
					$query
				);

			$oStmt = dbManager::getInstance()->prepare($new_query);

			if ( $oStmt->execute() ) {
				$row = $oStmt->fetch();
				if ( $row !== false && is_array($row) ) {
					return $row[0];
				}
			}
			$oStmt->closeCursor();
		}
		
		return 0;
	}
	
	/**
	 * @see baseSearchInterface::canSearchRun()
	 */
	function canSearchRun() {
		return true;
	}
	
	/**
	 * @see baseSearchInterface::buildSelect()
	 */
	function buildSelect(&$inQuery) {
		$inQuery = 'SELECT SQL_CALC_FOUND_ROWS userMovieGrants.id AS ID, userID, firstname, surname, movieID, filmTitle, requestedAmount, status, grantedAmount,rating
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userMovieGrants inner JOIN '.system::getConfig()->getDatabase('mofilm_content').'.users ON (users.ID = userMovieGrants.userID) ';

		if ( $this->getEventCount() > 0 || $this->getSourceCount() > 0 ) {
			$inQuery .= ' inner JOIN '.system::getConfig()->getDatabase('mofilm_content').'.grants ON (grantID = grants.ID)';
		}
	}

	/**
	 * @see baseSearchInterface::buildWhere()
	 */
	function buildWhere(&$inQuery) {
		$where = array();
	    	if ( $this->getSourceCount() > 0 || $this->getEventCount() > 0 ) {
			if ( $this->getSourceCount() == 0 && $this->getEventCount() > 0 ) {
				foreach ( $this->getEvents() as $eventID ) {
					$oEvent = mofilmEvent::getInstance($eventID);
					foreach ( $oEvent->getSourceSet() as $oSource ) {
						$this->addSource($oSource);
					}
				}
			}

			if ( $this->getSourceCount() > 0 ) {
				$where[] = ' grants.sourceID IN ('.implode(',', $this->getSources()).')';
			}
		}

		$where[]= ' private = 0 ';
		
		if ( $this->getStatusCount() > 0 ) {
			$where[] = ' status = '.dbManager::getInstance()->quote($this->getStatus());
		}
		
		if ( $this->getUserID() > 0 ) {
			$where[] = ' userID = '.dbManager::getInstance()->quote($this->getUserID());
		}

		if ( count($where) > 1 ) {
			$inQuery .= ' WHERE '.implode(' AND ', $where);
		} elseif ( count($where) == 1 ) {
			$inQuery .= ' WHERE '.$where[0];
		}
	}

	/**
	 * Adds the order by clause to the query
	 *
	 * @param string &$inQuery
	 */
	function buildOrderBy(&$inQuery) {
                $dir = $this->getOrderDirection() == self::ORDER_ASC ? 'ASC' : 'DESC';
                if ( !$this->getKeywords() && $this->getOrderBy() ) {
			$inQuery .= ' ORDER BY '.$this->getOrderBy().' '.$dir;
		} elseif ( $this->getKeywords() ) { 
			/*
			 * DR 2010-09-15: we have to strip 'movies.' from the orderby because the keyword
			 * search uses a UNION ALL and movies.uploaded even though selected, apparently
			 * does not exist. uploaded does though.
			 * 
			 * Oh yeah and here we do have to order by score first, because we are basically
			 * lumping 2 completely different full text searches together and those results are
			 * not in a sensible order. Blame CN for the insanity ;)
			 * 
			 * DR 2010-09-16: accuracy is being dropped in favour of date ordering. Hmm, not
			 * sure on that one. Anyway, to restore technically accurate results, order by: 
			 * SCORE DESC first.
			 */
			$inQuery .= ' ORDER BY '.str_replace('userMovieGrants.', '', $this->getOrderBy()).' '.$dir;
		}
                else
	    	$inQuery .= ' ORDER BY userMovieGrants.created DESC';
	}
	
	/**
	 * Adds the limit to the query
	 *
	 * @param string $inQuery
	 */
	function buildLimit(&$inQuery) {
		$inQuery .= ' LIMIT '.$this->getOffset().','.$this->getLimit();
	}

	/**
	 * Return value of $_Events
	 *
	 * @return array
	 * @access public
	 */
	function getEvents() {
		return $this->_Events;
	}

	/**
	 * Returns the number of events in search criteria
	 *
	 * @return integer
	 */
	function getEventCount() {
		return count($this->_Events);
	}

	/**
	 * Adds an event to the search criteria
	 *
	 * @param mixed $inEvent Either id or mofilmEvent object
	 * @return mofilmMovieSearch
	 */
	function addEvent($inEvent) {
		$event = $inEvent;
		if ( $inEvent instanceof mofilmEvent ) {
			$event = $inEvent->getID();
		}

		if ( is_numeric($event) && $event > 0 && !in_array($event, $this->_Events) ) {
			$this->_Events[] = $event;
		}
		return $this;
	}

	/**
	 * Removes the event from the search criteria
	 *
	 * @param mixed $inEvent Either id or mofilmEvent object
	 * @return mofilmMovieSearch
	 */
	function removeEvent($inEvent) {
		$event = $inEvent;
		if ( $inEvent instanceof mofilmEvent ) {
			$event = $inEvent->getID();
		}

		$key = array_search($event, $this->_Events);
		if ( $key !== false ) {
			unset($this->_Events[$key]);
		}
		return $this;
	}

	/**
	 * Set an array of event ids to the search criteria
	 *
	 * @param array $inEvents
	 * @return mofilmMovieSearch
	 * @access public
	 */
	function setEvents(array $inEvents = array()) {
		if ( $inEvents !== $this->_Events ) {
			$this->_Events = $inEvents;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Sources
	 *
	 * @return array
	 * @access public
	 */
	function getSources() {
		return $this->_Sources;
	}

	/**
	 * Returns the number of sources in search criteria
	 *
	 * @return integer
	 */
	function getSourceCount() {
		return count($this->_Sources);
	}

	/**
	 * Adds a source to the search criteria
	 *
	 * @param mixed $inSource Either id or mofilmSource object
	 * @return mofilmMovieSearch
	 */
	function addSource($inSource) {
		$source = $inSource;
		if ( $inSource instanceof mofilmSourceBase ) {
			$source = $inSource->getID();
		}

		if ( is_numeric($source) && $source > 0 && !in_array($source, $this->_Sources) ) {
			$this->_Sources[] = $source;
		}
		return $this;
	}

	/**
	 * Removes the source from the search criteria
	 *
	 * @param mixed $inSource Either id or mofilmSource object
	 * @return mofilmMovieSearch
	 */
	function removeSource($inSource) {
		$source = $inSource;
		if ( $inSource instanceof mofilmSourceBase ) {
			$source = $inSource->getID();
		}

		$key = array_search($source, $this->_Sources);
		if ( $key !== false ) {
			unset($this->_Sources[$key]);
		}
		return $this;
	}

	/**
	 * Set $_Sources to $inSources
	 *
	 * @param array $inSources
	 * @return mofilmMovieSearch
	 * @access public
	 */
	function setSources($inSources) {
		if ( $inSources !== $this->_Sources ) {
			$this->_Sources = $inSources;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Status
	 *
	 * @return string
 	 */
	function getStatus() {
		return $this->_Status;
	}

	function getRating() {
		return $this->_Rating;
	}
        
	/**
	 * Returns the number of statuses in the search criteria
	 * 
	 * @return integer
	 */
	function getStatusCount() {
		return count($this->_Status);
	}

	/**
	 * Set the object property _Status to $inStatus
	 *
	 * @param string $inStatus
	 * @return mofilmUserMovieGrants
	 */
	function setStatus($inStatus) {
		if ( $inStatus !== $this->_Status ) {
			$this->_Status = $inStatus;
			$this->setModified();
		}
		return $this;
	}

	function setRating($inRating) {
		if ( $inRating !== $this->_Rating ) {
			$this->_Rating = $inRating;
			$this->setModified();
		}
		return $this;
	}
        
	/**
	 * Return the current value of the property $_UserID
	 *
	 * @return integer
 	 */
	function getUserID() {
		return $this->_UserID;
	}

	/**
	 * Set the object property _UserID to $inUserID
	 *
	 * @param integer $inUserID
	 * @return mofilmUserMovieGrants
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_Offset
	 *
	 * @return integer
	 */
	function getOffset() {
		return $this->_Offset;
	}
	
	/**
	 * Set $_Offset to $inOffset
	 *
	 * @param integer $inOffset
	 * @return baseSearch
	 */
	function setOffset($inOffset) {
		if ( $inOffset !== $this->_Offset ) {
			$this->_Offset = $inOffset;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_Limit
	 *
	 * @return integer
	 */
	function getLimit() {
		return $this->_Limit;
	}
	
	/**
	 * Set $_Limit to $inLimit
	 *
	 * @param integer $inLimit
	 * @return baseSearch
	 */
	function setLimit($inLimit) {
		if ( $inLimit !== $this->_Limit ) {
			$this->_Limit = $inLimit;
			$this->setModified();
		}
		return $this;
	}
}
