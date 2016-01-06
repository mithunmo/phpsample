<?php
/**
 * mofilmReportUserRegistrations
 *
 * Stored in mofilmReportUserRegistrations.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserRegistrations
 * @version $Rev: 736 $
 */


/**
 * mofilmReportUserRegistrations
 *
 * mofilmReportUserRegistrations report class.
 * 
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserRegistrations
 */
class mofilmReportUserRegistrations extends reportBase {

	const OPTION_YEAR = 'report.year';

	/**
	 * @see reportBase::isValid()
	 */
	function isValid() {
		
		return true;
	}

	/**
	 * @see reportBase::initialise()
	 */
	function initialise() {
		$this->addReportColumn(new reportColumn('displayDate', 'Date', 20));
		$this->addReportColumn(new reportColumn('userID', 'ID', 20));
		$this->addReportColumn(new reportColumn('firstname', 'Firstname', 30));
		$this->addReportColumn(new reportColumn('surname', 'Surname ', 30));
		$this->addReportColumn(new reportColumn('email', 'Email Address', 40));		
                $this->addReportColumn(new reportColumn('city', 'city', 40));		
		$this->addReportColumn(new reportColumn('country', 'Country', 20));
                $this->addReportColumn(new reportColumn('Role', 'Role', 40));
                $this->addReportColumn(new reportColumn('ProEnabled', 'ProEnabled', 40));
                $this->addReportColumn(new reportColumn('proOnly', 'proOnly', 40));
	}

	/**
	 * @see reportBase::_run()
	 *
	 * @return boolean
	 */
	function _run() {
		$query = " SELECT ProEnabled, proOnly, IFNULL(users.firstname,'Unknown') AS firstname, IFNULL(users.surname,'unknown') AS surname, users.email as email, users.ID AS userID,IFNULL( primaryRole.name, '') AS Role,
                        IFNULL((SELECT userData.paramValue FROM " . system::getConfig()->getDatabase('mofilm_content') . ".userData WHERE userID=users.ID AND paramName='City'),'Unknown') AS city,
			  DATE_FORMAT(users.registered, '%d/%m/%Y') AS displayDate,IFNULL(territories.country, 'Unknown') AS country
			  FROM ". system::getConfig()->getDatabase('mofilm_content').".users
			  LEFT JOIN ".system::getConfig()->getDatabase('mofilm_content').".territories ON (users.territoryID = territories.ID)  
LEFT JOIN " . system::getConfig()->getDatabase('mofilm_content') . ".primaryRole ON (users.userRole = primaryRole.ID)                               
			  WHERE users.enabled = :Enabled
			  AND users.registered LIKE :Date";
		
		
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->setFetchMode(PDO::FETCH_ASSOC);
		$oStmt->bindValue(':Enabled', mofilmUserBase::ENABLED_Y);
		$oStmt->bindValue(':Date', $this->getDateYear()."%");
		
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
		return 'User registrations by year';
	}

	/**
	 * @see reportBase::getReportDescription()
	 *
	 * @return string
	 */
	function getReportDescription() {
		return 'Report detailing the users in the year '.$this->getDateYear();
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
	function getDateYear() {
		
		$year = $this->getOption(self::OPTION_YEAR, '');
		return $year["Year"];
	}

	/**
	 * Sets the affiliate to report on
	 *
	 * @param string $inOption
	 * @return reportBase
	 */
	function setDateYear($inOption) {
		return $this->setOptions(array(self::OPTION_YEAR => $inOption));
	}
}