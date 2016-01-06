<?php
/**
 * mofilmReportBase
 * 
 * Stored in mofilmReportBase.class.php
 * 
 * @author Dave Redfern
 * @copyright MOFILM Ltd (c) 2009-2010
 * @package mofilm
 * @subpackage report
 * @category mofilmReportBase
 * @version $Rev: 245 $
 */


/**
 * mofilmReportBase
 * 
 * Provides the MOFILM specific report extensions that are (mostly) used
 * by all reports within MOFILM.
 *
 * @package mofilm
 * @subpackage report
 * @category mofilmReportBase
 */
abstract class mofilmReportBase extends reportBase {

	const OPTION_USER_ID = 'report.user.id';
	const OPTION_EVENT_ID = 'report.event.id';
	const OPTION_SOURCE_ID = 'report.source.id';
	const OPTION_MOVIE_STATUS = 'report.movie.status';
	const OPTION_USER_MOVIE_GRANT_STATUS = 'report.grant.status';

	/**
	 * Holds an instance of mofilmEvent
	 * 
	 * @var mofilmEvent
	 * @access protected
	 */
	protected $_Event;
	
	/**
	 * Holds an instance of mofilmSource
	 * 
	 * @var mofilmSource
	 * @access protected
	 */
	protected $_Source;
	


	/**
	 * Returns the value of OPTION_USER_ID
	 *
	 * @return integer
	 */
	function getUserID() {
		return $this->getOption(self::OPTION_USER_ID, 0);
	}

	/**
	 * Sets the value of OPTION_USER_ID
	 *
	 * @param integer $inValue
	 * @return mofilmReportBase
	 */
	function setUserID($inValue) {
		$this->setOptions(self::OPTION_USER_ID, $inValue);
		return $this;
	}

	/**
	 * Returns the eventID
	 *
	 * @return integer
	 */
	function getEventID() {
		return $this->getOption(self::OPTION_EVENT_ID, 0);
	}
	
	/**
	 * Returns the mofilmEvent, loading it if not already loaded
	 * 
	 * @return mofilmEvent
	 */
	function getEvent() {
		if ( !$this->_Event instanceof mofilmEvent ) {
			$this->_Event = mofilmEvent::getInstance($this->getEventID());
		}
		return $this->_Event;
	}
	
	/**
	 * Sets the eventID to report on
	 *
	 * @param integer $inOption
	 * @return reportBase
	 */
	function setEventID($inOption) {
		return $this->setOptions(array(self::OPTION_EVENT_ID => $inOption));
	}
	
	/**
	 * Returns the sourceID
	 *
	 * @return integer
	 */
	function getSourceID() {
		return $this->getOption(self::OPTION_SOURCE_ID, 0);
	}
	
	/**
	 * Returns the mofilmSource, loading it if not already laoded
	 * 
	 * @return mofilmSource
	 */
	function getSource() {
		if ( !$this->_Source instanceof mofilmSource ) {
			$this->_Source = mofilmSource::getInstance($this->getSourceID());
		}
		return $this->_Source;
	}
	
	/**
	 * Sets the sourceID to report on
	 *
	 * @param integer $inOption
	 * @return reportBase
	 */
	function setSourceID($inOption) {
		return $this->setOptions(array(self::OPTION_SOURCE_ID => $inOption));
	}
	
	/**
	 * Returns the movie status to report on
	 *
	 * @return string
	 */
	function getMovieStatus() {
		return $this->getOption(self::OPTION_MOVIE_STATUS, false);
	}
	
	/**
	 * Sets the movie status to report on
	 *
	 * @param string $inOption
	 * @return reportBase
	 */
	function setMovieStatus($inOption) {
		return $this->setOptions(array(self::OPTION_MOVIE_STATUS => $inOption));
	}
	
	/**
	 * Returns the user movie grant status to report on
	 *
	 * @return string
	 */
	function getUserMovieGrantStatus() {
		return $this->getOption(self::OPTION_USER_MOVIE_GRANT_STATUS, false);
	}
	
	/**
	 * Sets the user movie grant status to report on
	 *
	 * @param string $inOption
	 * @return reportBase
	 */
	function setUserMovieGrantStatus($inOption) {
		return $this->setOptions(array(self::OPTION_USER_MOVIE_GRANT_STATUS => $inOption));
	}
}