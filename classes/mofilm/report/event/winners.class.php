<?php
/**
 * mofilmReportEventWinners
 * 
 * Stored in mofilmReportEventWinners.class.php
 * 
 * @author Dave Redfern
 * @copyright MOFILM Ltd (c) 2009-2010
 * @package mofilm
 * @subpackage report
 * @category mofilmReportEventWinners
 * @version $Rev: 10 $
 */


/**
 * mofilmReportEventWinners
 * 
 * Reports on all winners for passed events.
 *
 * @package mofilm
 * @subpackage report
 * @category mofilmReportEventWinners
 */
class mofilmReportEventWinners extends mofilmReportBase {

	const OPTION_AWARD_POSITION = 'report.award.position';
	
	/**
	 * @see reportBase::isValid()
	 */
	function isValid() {
		if ( $this->getAwardPosition() && $this->getAwardPosition() < 1 || $this->getAwardPosition() > 5 ) {
			throw new mofilmException('Award position must be between 1 and 5 only');
		}
		return true;
	}
	
	/**
	 * @see reportBase::initialise()
	 */
	function initialise() {
		$this->addReportColumn(new reportColumn('date', 'Date', 20, reportColumn::FIELD_DATE));
		$this->addReportColumn(new reportColumn('event', 'Event', 40));
		$this->addReportColumn(new reportColumn('source', 'Brand', 40));
		$this->addReportColumn(new reportColumn('position', 'Placed', 10));
		$this->addReportColumn(new reportColumn('fullname', 'Full Name', 40));
		$this->addReportColumn(new reportColumn('territory', 'territory', 40));
		$this->addReportColumn(new reportColumn('email', 'Email Address', 40));
		$this->addReportColumn(new reportColumn('Credits', 'Credits', 40));
		$this->addReportColumn(new reportColumn('phone', 'Phone Number', 40));
		$this->addReportColumn(new reportColumn('movieID', 'Movie #', 20, reportColumn::FIELD_INTEGER));
		$this->addReportColumn(new reportColumn('shortDesc', 'Movie Title', 50));
		$this->addReportColumn(new reportColumn('hash', 'Movie Link', 300));
	}
	
	/**
	 * @see reportBase::_run()
	 *
	 * @return boolean
	 */
	function _run() {
		$query = "
			SELECT DATE_FORMAT(events.enddate, '%Y-%m-%d') AS date, events.name AS event, users.email, sources.name AS source,
			       movieAwards.position, CONCAT(users.firstname, ' ', users.surname) AS fullname, movieAwards.movieID, movies.shortDesc,
			       IFNULL(territories.country, 'Unknown') AS territory,
			       (
			           SELECT userData.paramValue FROM ".system::getConfig()->getDatabase('mofilm_content').".userData
			            WHERE userID=users.ID AND paramName='Phone'
			       ) AS phone,movies.credits AS Credits
			  FROM ".system::getConfig()->getDatabase('mofilm_content').".events
			       INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".movieAwards ON (events.ID = movieAwards.eventID)
			       INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".sources ON (movieAwards.sourceID = sources.ID)
			       INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".movies ON (movieAwards.movieID = movies.ID)
			       INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".users ON (movies.userID = users.ID)
			       LEFT JOIN ".system::getConfig()->getDatabase('mofilm_content').".territories ON (users.territoryID = territories.ID)
			 WHERE events.enddate < :TsDate
			   AND movieAwards.type = 'Finalist'
			   AND movieAwards.position <= :Position
			   AND movieAwards.position != 0
			 ORDER BY events.enddate ASC, events.name ASC, sources.name ASC, movieAwards.position ASC";
		
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->setFetchMode(PDO::FETCH_ASSOC);
		$oStmt->bindValue(':TsDate', date(system::getConfig()->getDatabaseDatetimeFormat()));
		$oStmt->bindValue(':Position', $this->getAwardPosition(), PDO::PARAM_INT);
		if ( $oStmt->execute() ) {
			
			$this->getReportData()->query()->beginTransaction();
			
			foreach ( $oStmt as $row ) {
				if ( is_null($row['phone']) ) {
					$row['phone'] = '';
				}
				$row['territory'] = utilityStringFunction::capitaliseEncodedString($row['territory']);
				$row['hash'] = mofilmMovieManager::getInstanceByID($row['movieID'])->getShortUri(0,TRUE);
				
				$this->getReportData()->addRow($row);
			}
			
			$this->getReportData()->query()->commit();
		}
		$oStmt->closeCursor();
		return true;
	}

	/**
	 * @see reportBase::getReportName()
	 *
	 * @return string
	 */
	function getReportName() {
		return 'Event Winners';
	}

	/**
	 * @see reportBase::getReportDescription()
	 *
	 * @return string
	 */
	function getReportDescription() {
		$return = 'Report detailing all previous winners including name, email address and phone number';
		
		if ( $this->getAwardPosition() ) {
			switch ( $this->getAwardPosition() ) {
				case 1:  $ord = 'st'; break;
				case 2:  $ord = 'nd'; break;
				case 3:  $ord = 'rd'; break;
				default: $ord = 'th';
			}
			$return .= ' up to '.$this->getAwardPosition().$ord.' place winnders';
		}

		return $return;
	}
	
	/**
	 * @see reportBase::getValidGroupByOptions() 
	 */
	function getValidGroupByOptions() {
		return array();
	}

	/**
	 * @see reportBase::getValidOrderByOptions()
	 */
	function getValidOrderByOptions() {
		return array();
	}

	/**
	 * Returns the value of OPTION_AWARD_POSITION
	 *
	 * @return integer
	 */
	function getAwardPosition() {
		return $this->getOption(self::OPTION_AWARD_POSITION, 1);
	}

	/**
	 * Sets the value of OPTION_AWARD_POSITION
	 *
	 * @param integer $inValue
	 * @return mofilmReportEventWinners
	 */
	function setAwardPosition($inValue) {
		$this->setOptions(self::OPTION_AWARD_POSITION, $inValue);
		return $this;
	}
}
