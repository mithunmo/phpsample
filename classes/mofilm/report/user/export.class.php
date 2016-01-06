<?php
/**
 * mofilmReportUserExport
 * 
 * Stored in mofilmReportUserExport.class.php
 * 
 * @author Dave Redfern
 * @copyright MOFILM Ltd (c) 2009-2010
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserExport
 * @version $Rev: 245 $
 */


/**
 * mofilmReportUserExport
 * 
 * Exports user details who are subscribed to an email list
 *
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserExport
 */
class mofilmReportUserExport extends mofilmReportBase {
	
	const OPTION_LIST_ID = 'report.list.id';
	
	/**
	 * Stores the list type instance
	 * 
	 * @var mofilmCommsListType
	 * @access protected
	 */
	protected $_List;
	
	/**
	 * @see reportBase::isValid()
	 */
	function isValid() {
		if ( !$this->getListID() || !is_numeric($this->getListID()) ) {
			throw new reportCentreException('Invalid value supplied for ListID');
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
	}
	
	/**
	 * @see reportBase::_run()
	 *
	 * @return boolean
	 */
	function _run() {
		$query = "
			SELECT 
				users.firstname,
				users.surname, 
				users.email, 
				(SELECT userData.paramValue FROM ".system::getConfig()->getDatabase('mofilm_content').".userData WHERE userID=users.ID AND paramName='City') AS city,
				territories.country AS country
			
			FROM 
			        ".system::getConfig()->getDatabase('mofilm_comms').".emails 
			        LEFT JOIN ".system::getConfig()->getDatabase('mofilm_content').".users ON ( users.ID = emails.userID )
			        LEFT JOIN ".system::getConfig()->getDatabase('mofilm_content').".territories ON ( territories.ID = users.territoryID )
			
			WHERE 
			        users.`enabled` = :Enabled
			        AND users.territoryID IS NOT NULL
			        AND users.clientID = 0";
		
                
                
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->setFetchMode(PDO::FETCH_ASSOC);
		$oStmt->bindValue(':Enabled', mofilmUserBase::ENABLED_Y);
		
		if ( $oStmt->execute() ) {
			
			$this->getReportData()->query()->beginTransaction();
			
			foreach ( $oStmt as $row ) {
				$row['country'] = utilityStringFunction::capitaliseEncodedString($row['country']);
				$row['firstname'] = utf8_decode($row['firstname']);
				$row['surname'] = utf8_decode($row['surname']);
				$row['city'] = utf8_decode($row['city']);
				
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
		return 'User Mail List Export '.$this->getListID();
	}

	/**
	 * @see reportBase::getReportDescription()
	 *
	 * @return string
	 */
	function getReportDescription() {
		return 'Details of users who are subscribed to the email list '.$this->getList()->getDescription();
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
	 * Returns the listID
	 *
	 * @return integer
	 */
	function getListID() {
		return $this->getOption(self::OPTION_LIST_ID, 0);
	}
	
	/**
	 * Returns the mofilmCommsListType, loading it if not already laoded
	 * 
	 * @return mofilmCommsListType
	 */
	function getList() {
		if ( !$this->_List instanceof mofilmCommsListType ) {
			$this->_List = mofilmCommsListType::getInstance($this->getListID());
		}
		return $this->_List;
	}
	
	/**
	 * Sets the listID to report on
	 *
	 * @param integer $inOption
	 * @return reportBase
	 */
	function setListID($inOption) {
		return $this->setOptions(array(self::OPTION_LIST_ID => $inOption));
	}
}