<?php
/**
 * mofilmCommsNewsletterFilterNonwinners
 *
 * Stored in mofilmCommsNewsletterFilterNonwinners.class.php
 *
 * @author Pavan Kumar P G
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmCommsNewsletterFilterNonwinners
 * @category mofilmCommsNewsletterFilterNonwinners
 * @version $Rev: 1 $
 */

class mofilmCommsNewsletterFilterNonwinners extends mofilmCommsNewsletterFilterlist {
	
	const CCA_MODERATOR = "cca@mofilm.com";
	
	
	
	/**
	 * Constructs a new object 
	 *
	 * @param mofilmCommsNewsletterdata $inNewsletterData
	 */
	function __construct($inNewsletterData) {
		parent::__construct($inNewsletterData);
	}
 	
	/**
	 * Gets the list of userID who have uploaded a video for a event and whose rating is above certain criteria
	 *
	 * @return array
	 */
	function apply() {
		$resultList = array();

		$sourceID = $this->getNewsletterData()->getParamSet()->getParam(mofilmCommsNewsletterdata::PARAM_NL_SOURCEID);
		
		$query = '
			SELECT DISTINCTROW users.ID,movies.ID AS movieID,movies.shortDesc AS Title,users.email AS emailID,sources.name AS Brand,events.name AS Event,events.ID AS eventID,sources.ID AS sourceID
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.users
			       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movies ON (users.ID = movies.userID)
			       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movieAwards ON (movies.ID = movieAwards.movieID)
			       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movieSources ON (movies.ID = movieSources.movieID)
			       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.sources ON (movieSources.sourceID = sources.ID)
			       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.events ON (events.ID = sources.eventID)
			 WHERE movieAwards.type = "Shortlisted"
			       AND movieAwards.movieID not in ( 
				    SELECT movieAwards.movieID FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieAwards 
					WHERE movieAwards.type = "Finalist" AND movieAwards.eventID = :EventID ';
		
		if ( $sourceID != "" || $sourceID != 0 ) {
			$query .= ' AND movieAwards.sourceID IN ('.$sourceID. ') ) ';
		} else {
			$query .= ' ) ';
		}
		
		$query .= ' AND sources.eventID = :EventID
			       AND movies.status IN ( "Approved", "Pending" ) ';
		
		if ( $sourceID != "" || $sourceID != 0 ) {
			$query.= ' AND sources.ID IN ('.$sourceID. ')';
		}
		systemLog::message($query);
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':EventID', $this->getNewsletterData()->getParamSet()->getParam(mofilmCommsNewsletterdata::PARAM_NL_EVENTID));
		$oStmt->bindValue(':AvgRating', $this->getNewsletterData()->getParamSet()->getParam(mofilmCommsNewsletterdata::PARAM_NL_VIDEO_RATING));
		
		if ( $oStmt->execute() ) {
			foreach ( $oStmt as $row ) {
				if ( mofilmCommsCcaEmails::getCCARecordByMovieandEventID($row["movieID"], $this->getNewsletterData()->getParamSet()->getParam(mofilmCommsNewsletterdata::PARAM_NL_EVENTID)) ) {
					$resultList[] = $row;
					$oMofilmCommsCCA = new mofilmCommsCcaEmails();
					$oMofilmCommsCCA->setEventID($this->getNewsletterData()->getParamSet()->getParam(mofilmCommsNewsletterdata::PARAM_NL_EVENTID));
					$oMofilmCommsCCA->setEmail($row["emailID"]);
					$oMofilmCommsCCA->setMovieID($row["movieID"]);
					$oMofilmCommsCCA->save();
				} else {
					systemLog::info('Email has been alredy sent to '.$row["emailID"]." for the movie ".$row["Title"]);
				}
			}
		}
		$oStmt->closeCursor();
		
		//$resultList[] = $this->addCCAEmail();
		
		return $resultList;
	}
	
	/**
	 * It adds the CCA email address to monitor the campaign
	 * 
	 * @return array
	 */
	function addCCAEmail() {
		$oUser = mofilmUserManager::getInstanceByUsername(self::CCA_MODERATOR);
		return array("ID" => $oUser->getID(), 4 => mofilmSource::getInstance($this->getNewsletterData()->getParamSet()->getParam(mofilmCommsNewsletterdata::PARAM_NL_SOURCEID))->getName(), 5 => mofilmEvent::getInstance($this->getNewsletterData()->getParamSet()->getParam(mofilmCommsNewsletterdata::PARAM_NL_EVENTID))->getName() , 3 => self::CCA_MODERATOR);
	}
}