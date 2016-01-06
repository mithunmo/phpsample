<?php
/**
 * mofilmReportUserAffiliatesRegistrations
 *
 * Stored in mofilmReportUserAffiliatesRegistrations.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserAffiliatesRegistrations
 * @version $Rev: 736 $
 */


/**
 * mofilmReportUserAffiliatesRegistrations
 *
 * mofilmReportUserAffiliatesRegistrations report class.
 * 
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserAffiliatesRegistrations
 */
class mofilmReportUserAffiliatesRegistrations extends reportBase {

	const OPTION_AFFILIATE = 'report.affiliate';

	/**
	 * @see reportBase::isValid()
	 */
	function isValid() {
		if ( !$this->getAffiliate() || strlen($this->getAffiliate()) < 1 ) {
			throw new reportCentreException('Missing an affiliate to report on');
		}
		return true;
	}

	/**
	 * @see reportBase::initialise()
	 */
	function initialise() {
		$this->addReportColumn(new reportColumn('displayDate', 'Date', 20));
		$this->addReportColumn(new reportColumn('registrations', 'Registrations', 20));
	}

	/**
	 * @see reportBase::_run()
	 *
	 * @return boolean
	 */
	function _run() {
		$query = "
			SELECT DATE_FORMAT(users.registered, '%d/%m/%Y') AS displayDate, COUNT(users.ID) AS registrations
			  FROM ". system::getConfig()->getDatabase('mofilm_content').".users
			       INNER JOIN ". system::getConfig()->getDatabase('mofilm_content').".userAffiliates ON (userAffiliates.userID = users.ID)
			 WHERE users.enabled = :Enabled
			   AND users.registered IS NOT NULL
			   AND userAffiliates.affiliate = :Affiliate
			 GROUP BY DATE_FORMAT(users.registered, '%Y%m%d')
			 ORDER BY DATE_FORMAT(users.registered, '%Y%m%d') ASC";

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->setFetchMode(PDO::FETCH_ASSOC);
		$oStmt->bindValue(':Enabled', mofilmUserBase::ENABLED_Y);
		$oStmt->bindValue(':Affiliate', $this->getAffiliate());

		if ( $oStmt->execute() ) {
			$this->getReportData()->query()->beginTransaction();

			foreach ( $oStmt as $row ) {
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
		return 'User Affiliates Count';
	}

	/**
	 * @see reportBase::getReportDescription()
	 *
	 * @return string
	 */
	function getReportDescription() {
		return 'Report detailing the number of user registrations from affiliate: '.$this->getAffiliate().' by date.';
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
	 * Returns the affiliate
	 *
	 * @return string
	 */
	function getAffiliate() {
		return $this->getOption(self::OPTION_AFFILIATE, '');
	}

	/**
	 * Sets the affiliate to report on
	 *
	 * @param string $inOption
	 * @return reportBase
	 */
	function setAffiliate($inOption) {
		return $this->setOptions(array(self::OPTION_AFFILIATE => $inOption));
	}
}