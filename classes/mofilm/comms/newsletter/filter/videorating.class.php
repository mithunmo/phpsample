<?php
/**
 * mofilmCommsNewsletterFilterVideorating
 *
 * Stored in mofilmCommsNewsletterFilterVideorating.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmCommsNewsletterFilterVideorating
 * @category mofilmCommsNewsletterFilterVideorating
 * @version $Rev: 73 $
 */
class mofilmCommsNewsletterFilterVideorating extends mofilmCommsNewsletterFilterlist {

	/**
	 * Constructs a new object 
	 *
	 * @param object mofilmCommsNewsletterdata
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

		$query = '
			SELECT DISTINCT users.ID
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.users
			       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movies ON (users.ID = movies.userID)
			       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movieSources ON (movies.ID = movieSources.movieID)
			       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.sources ON (movieSources.sourceID = sources.ID)
			 WHERE movies.avgRating >= :AvgRating
			   AND sources.eventID = :EventID';

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':EventID', $this->getNewsletterData()->getParamSet()->getParam(mofilmCommsNewsletterdata::PARAM_NL_EVENTID));
		$oStmt->bindValue(':AvgRating', $this->getNewsletterData()->getParamSet()->getParam(mofilmCommsNewsletterdata::PARAM_NL_VIDEO_RATING));
		if ( $oStmt->execute() ) {
			foreach ( $oStmt as $row ) {
				if ( $this->getNewsletterData()->getMessageType() == mofilmCommsNewsletterdata::NEWSLETTER_MKT_MESSAGE ) {

					if ( mofilmCommsEmail::getInstanceByUser($row['ID'])->getSubscribedStatus() ) {
						$resultList[] = $row;
					}

				} else {
					$resultList[] = $row;
				}
			}
		}
		$oStmt->closeCursor();

		return $resultList;
	}

}