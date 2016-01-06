<?php
/**
 * mofilmReportUserSignups
 * 
 * Stored in mofilmReportUserSignups.class.php
 * 
 * @author Dave Redfern
 * @copyright MOFILM Ltd (c) 2009-2010
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserSignups
 * @version $Rev: 272 $
 */


/**
 * mofilmReportUserSignups
 * 
 * Details user sign-up information and allows filtering by a specific signup code
 * and/or territory.
 *
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserSignups
 */
class mofilmReportUserSignups extends mofilmReportBase {
	
	const OPTION_SIGNUP_CODE = 'report.signup.code';
	const OPTION_TERRITORY_ID = 'report.territory.id';
	
	/**
	 * @see reportBase::isValid()
	 */
	function isValid() {
		if ( $this->getTerritoryID() && !is_numeric($this->getTerritoryID()) ) {
			throw new reportCentreException('Invalid territory selected for report');
		}
		if ( $this->getSignupCode() ) {
			$oObject = $this->getSignupObject();
			if ( !is_object($oObject) || $oObject->getID() < 1 ) {
				throw new reportCentreException('Invalid signup code specified for report');
			}
		}
		return true;
	}
	
	/**
	 * @see reportBase::initialise()
	 */
	function initialise() {
		$this->addReportColumn(new reportColumn('firstname', 'Firstname', 30));
		$this->addReportColumn(new reportColumn('surname', 'Surname ', 30));
		$this->addReportColumn(new reportColumn('email', 'Email Address', 40));
		$this->addReportColumn(new reportColumn('city', 'City', 40));
		$this->addReportColumn(new reportColumn('country', 'Country', 20));
		$this->addReportColumn(new reportColumn('active', 'Active', 20));
		$this->addReportColumn(new reportColumn('signupCode', 'Signup Code', 20));
		$this->addReportColumn(new reportColumn('registrationDate', 'Registration Date', 20, reportColumn::FIELD_DATETIME));
	}
	
	/**
	 * @see reportBase::_run()
	 *
	 * @return boolean
	 */
	function _run() {
		$query = "
			SELECT users.firstname, users.surname, users.email, users.registered AS registrationDate, IF(users.enabled='Y', 'Yes', 'No') AS active, 
			       (SELECT userData.paramValue FROM ".system::getConfig()->getDatabase('mofilm_content').".userData WHERE userID=users.ID AND paramName='City') AS city,
			       (SELECT IFNULL(userSignupCodes.description,'Unknown') 
				    FROM ".system::getConfig()->getDatabase('mofilm_content').".userData 
				    LEFT JOIN ".system::getConfig()->getDatabase('mofilm_content').".userSignupCodes ON (userSignupCodes.ID=paramValue) 
				    WHERE userID=users.ID AND paramName='SignupCode') AS signupCode,
			       IFNULL(territories.country, 'Unknown') AS country
			FROM ".system::getConfig()->getDatabase('mofilm_content').".users 
				 LEFT JOIN ".system::getConfig()->getDatabase('mofilm_content').".territories ON ( territories.ID = users.territoryID ) ";
		
		$where = "WHERE users.clientID = 0";

		if ( $this->getSignupCode() ) {
			$query .= '
				INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').".userData
				   ON (
				       users.ID = userData.userID
				       AND userData.paramName = 'SignupCode'
				       AND userData.paramValue = ".dbManager::getInstance()->quote($this->getSignupCode())."
				   )";
		}
		if ( $this->getTerritoryID() ) {
			$where .= ' AND territoryID = '.dbManager::getInstance()->quote($this->getTerritoryID());
		}
		
		$oStmt = dbManager::getInstance()->prepare($query.$where);
		$oStmt->setFetchMode(PDO::FETCH_ASSOC);
		
		if ( $oStmt->execute() ) {
			
			$this->getReportData()->query()->beginTransaction();
			
			foreach ( $oStmt as $row ) {
				$row['country'] = utilityStringFunction::capitaliseEncodedString($row['country']);
				$row['firstname'] = utf8_decode($row['firstname']);
				$row['surname'] = utf8_decode($row['surname']);
				$row['city'] = utf8_decode($row['city']);
				$row['signupCode'] = utf8_decode($row['signupCode']);
				
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
		return 'User Signups'.($this->getSignupCode() ? ' '.$this->getSignupCode() : '').($this->getTerritoryID() ? ' '.$this->getTerritoryID() : '');
	}

	/**
	 * @see reportBase::getReportDescription()
	 *
	 * @return string
	 */
	function getReportDescription() {
		$return = 'Report detailing user signup information';
		
		if ( $this->getSignupCode() ) {
			$return .= ' from '.mofilmUserSignupCode::getInstance($this->getSignupCode())->getDescription();
		}
		if ( $this->getTerritoryID() ) {
			$return .= ' from territory '.utilityStringFunction::capitaliseEncodedString($this->getTerritory()->getCountry());
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
	 * Returns the signup code to search on
	 *
	 * @return string
	 */
	function getSignupCode() {
		return $this->getOption(self::OPTION_SIGNUP_CODE);
	}
	
	/**
	 * Returns the user signup object from the current code
	 * 
	 * @return mofilmUserSignupCode
	 */
	function getSignupObject() {
		return mofilmUserSignupCode::getInstance($this->getSignupCode());
	}
	
	/**
	 * Sets the signup code to search on
	 *
	 * @param string $inOption
	 * @return reportBase
	 */
	function setSignupCode($inOption) {
		return $this->setOptions(array(self::OPTION_SIGNUP_CODE => $inOption));
	}

	/**
	 * Returns the territory to search on
	 *
	 * @return integer
	 */
	function getTerritoryID() {
		return $this->getOption(self::OPTION_TERRITORY_ID);
	}
	
	/**
	 * Returns the territory object
	 * 
	 * @return mofilmTerritory
	 */
	function getTerritory() {
		return mofilmTerritory::getInstance($this->getTerritoryID());
	}
	
	/**
	 * Sets the territory id to search on
	 *
	 * @param integer $inOption
	 * @return reportBase
	 */
	function setTerritoryID($inOption) {
		return $this->setOptions(array(self::OPTION_TERRITORY_ID => $inOption));
	}
}