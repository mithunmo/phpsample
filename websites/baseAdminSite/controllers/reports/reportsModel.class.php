<?php
/**
 * reportsModel.class.php
 * 
 * reportsModel class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category reportsModel
 * @version $Rev: 11 $
 */


/**
 * reportsModel class
 * 
 * Provides the "reports" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category reportsModel
 */
class reportsModel extends mvcModelBase {
	
	/**
	 * Stores $_CurrentUser
	 *
	 * @var mofilmUser
	 * @access protected
	 */
	protected $_CurrentUser;
	
	/**
	 * Stores $_ReportSet
	 *
	 * @var reportCentreReportSet
	 * @access protected
	 */
	protected $_ReportSet;
	
	/**
	 * Stores $_SearchOffset
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_SearchOffset;
	
	/**
	 * Stores $_SearchLimit
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_SearchLimit;
	
	/**
	 * Stores $_ReportTypeID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_ReportTypeID;
	
	/**
	 * Stores $_ReportSchedule
	 *
	 * @var reportCentreReportSchedule
	 * @access protected
	 */
	protected $_ReportSchedule;
	
	
	
	/**
	 * @see mvcModelBase::__construct()
	 */
	function __construct(mofilmUser $inUser) {
		parent::__construct();
		
		$this->setCurrentUser($inUser);
		
		$this->_ReportSet = null;
		$this->_SearchOffset = 0;
		$this->_SearchLimit = 30;
		$this->_ReportTypeID = null;
		$this->_ReportSchedule = null;
	}
	
	/**
	 * Returns the current users report inbox
	 * 
	 * @return reportCentreReportSet
	 */
	function getInbox() {
		if ( !$this->_ReportSet instanceof reportCentreReportSet ) {
			$this->_ReportSet = reportCentreReport::getUserReportInboxItems(
				$this->getSearchOffset(), $this->getSearchLimit(), $this->getCurrentUser()->getID()
			);
		}
		return $this->_ReportSet;
	}
	
	/**
	 * Returns the current users schedule list
	 * 
	 * @return reportCentreReportSet
	 */
	function getScheduledReports() {
		if ( !$this->_ReportSet instanceof reportCentreReportSet ) {
			$this->_ReportSet = reportCentreReportSchedule::getUserScheduleInboxItems(
				$this->getSearchOffset(), $this->getSearchLimit(), $this->getCurrentUser()->getID()
			);
		}
		return $this->_ReportSet;
	}
	
	/**
	 * Returns an array of reports that the user can access
	 * 
	 * @return reportCentreReportSet
	 */
	function getAvailableUserReports() {
		$reports = array();
		if ( !$this->_ReportSet instanceof reportCentreReportSet ) {
			$allReports = reportCentreReportType::listOfObjects(null, null, true);
			foreach ( $allReports as $oReport ) {
				if ( $this->isAllowedToRunReport($oReport) ) {
					$reports[] = $oReport;
				}
			}
			
			$this->_ReportSet = new reportCentreReportSet($reports, count($reports), 0, count($reports));
		}
		return $this->_ReportSet;
	}
	
	/**
	 * Returns true if the current user can run the specified report
	 * 
	 * @param integer $inReportID
	 * @return boolean
	 */
	function isAllowedToRunReport($inReportType) {
		if ( $inReportType instanceof reportCentreReportType ) {
			$oReportType = $inReportType;
		} else {
			$oReportType = reportCentreReportType::getInstance($inReportType);
		}
		
		if ( $oReportType->getReportTypeID() > 0 && $oReportType->getVisible() ) {
			$permission = ($this->getCurrentUser()->getPermissionsNamespace() ? $this->getCurrentUser()->getPermissionsNamespace().'.' : '').'report.'.$oReportType->getClassName();
			$oPermission = mofilmPermission::getInstanceByPermission($permission);
			if ( !$oPermission->getID() ) {
				$oPermission->setName($permission);
				$oPermission->setDescription('Allows the user to run the '.$oReportType->getTypeName().' report');
				$oPermission->save();
			}
			
			return $this->getCurrentUser()->isAuthorised($oPermission);
		} else {
			return false;
		}
	}
	
	/**
	 * Returns true if reportd is running
	 * 
	 * @return boolean
	 */
	function isDaemonRunning() {
		$oDaemonInfo = new cliProcessInformation('reportd');
		return $oDaemonInfo->getProcessID() > 0;
	}
	
	

	/**
	 * Returns $_CurrentUser
	 *
	 * @return mofilmUser
	 */
	function getCurrentUser() {
		return $this->_CurrentUser;
	}
	
	/**
	 * Set $_CurrentUser to $inCurrentUser
	 *
	 * @param mofilmUser $inCurrentUser
	 * @return reportsModel
	 */
	function setCurrentUser($inCurrentUser) {
		if ( $inCurrentUser !== $this->_CurrentUser ) {
			$this->_CurrentUser = $inCurrentUser;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_ReportSet
	 *
	 * @return reportCentreReportSet
	 */
	function getReportSet() {
		return $this->_ReportSet;
	}
	
	/**
	 * Set $_ReportSet to $inReportSet
	 *
	 * @param reportCentreReportSet $inReportSet
	 * @return reportsModel
	 */
	function setReportSet($inReportSet) {
		if ( $inReportSet !== $this->_ReportSet ) {
			$this->_ReportSet = $inReportSet;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_SearchOffset
	 *
	 * @return integer
	 */
	function getSearchOffset() {
		return $this->_SearchOffset;
	}
	
	/**
	 * Set $_SearchOffset to $inSearchOffset
	 *
	 * @param integer $inSearchOffset
	 * @return reportsModel
	 */
	function setSearchOffset($inSearchOffset) {
		if ( is_numeric($inSearchOffset) && $inSearchOffset >= 0 && $inSearchOffset !== $this->_SearchOffset ) {
			$this->_SearchOffset = $inSearchOffset;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_SearchLimit
	 *
	 * @return integer
	 */
	function getSearchLimit() {
		return $this->_SearchLimit;
	}
	
	/**
	 * Set $_SearchLimit to $inSearchLimit
	 *
	 * @param integer $inSearchLimit
	 * @return reportsModel
	 */
	function setSearchLimit($inSearchLimit) {
		if ( is_numeric($inSearchLimit) && $inSearchLimit > 0 && $inSearchLimit < 50 && $inSearchLimit !== $this->_SearchLimit ) {
			$this->_SearchLimit = $inSearchLimit;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_ReportTypeID
	 *
	 * @return integer
	 */
	function getReportTypeID() {
		return $this->_ReportTypeID;
	}
	
	/**
	 * Returns the report type object
	 * 
	 * @return reportCentreReportType
	 */
	function getReportType() {
		return reportCentreReportType::getInstance($this->getReportTypeID());
	}
	
	/**
	 * Set $_ReportTypeID to $inReportTypeID
	 *
	 * @param integer $inReportTypeID
	 * @return reportsModel
	 */
	function setReportTypeID($inReportTypeID) {
		if ( $inReportTypeID !== $this->_ReportTypeID ) {
			$this->_ReportTypeID = $inReportTypeID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the reportschedule object, creating one if not set
	 *
	 * @return reportCentreReportSchedule
	 */
	function getReportSchedule() {
		if ( !$this->_ReportSchedule instanceof reportCentreReportSchedule ) {
			$this->_ReportSchedule = new reportCentreReportSchedule();
			$this->_ReportSchedule->setDeliveryTypeID(reportCentreReportDeliveryType::T_INBOX);
			$this->_ReportSchedule->setReportScheduleTypeID(reportCentreReportScheduleType::T_ONCE);
			$this->_ReportSchedule->setReportScheduleStatus(reportCentreReportSchedule::REPORTSCHEDULESTATUS_ACTIVE);
			$this->_ReportSchedule->setUserID($this->getCurrentUser()->getID());
		}
		return $this->_ReportSchedule;
	}
	
	/**
	 * Set $_ReportSchedule to $inReportSchedule
	 *
	 * @param reportCentreReportSchedule $inReportSchedule
	 * @return reportsModel
	 */
	function setReportSchedule(reportCentreReportSchedule $inReportSchedule) {
		$this->_ReportSchedule = $inReportSchedule;
		$this->setModified();
		return $this;
	}

	/**
	 * Returns the last page offset for the search result
	 *
	 * @param integer $inLimit
	 * @return integer
	 */
	function getLastPageOffset($inLimit) {
		return $this->getReportSet()->getLastPage();
	}
}