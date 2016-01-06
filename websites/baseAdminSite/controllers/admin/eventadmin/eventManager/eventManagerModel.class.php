<?php
/**
 * eventManagerModel.class.php
 * 
 * eventManagerModel class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category eventManagerModel
 * @version $Rev: 11 $
 */


/**
 * eventManagerModel class
 * 
 * Provides the "eventManager" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category eventManagerModel
 */
class eventManagerModel extends mofilmEvent implements mvcDaoModelInterface {
	
	/**
	 * Stores $_CurrentUser
	 *
	 * @var mofilmUser
	 * @access protected
	 */
	protected $_CurrentUser;
	
	/**
	 * Stores the source stats
	 * 
	 * @var array
	 * @access protected
	 */
	protected $_SourceStats = array();
	
	/**
	 * Stores the grant stats
	 * 
	 * @var array
	 * @access protected
	 */
	protected $_GrantStats = array();

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
	 * @return eventManagerModel
	 */
	function setCurrentUser($inCurrentUser) {
		if ( $inCurrentUser !== $this->_CurrentUser ) {
			$this->_CurrentUser = $inCurrentUser;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the source stats based on $inData
	 * 
	 * @return array
	 */
	function getSourceStats() {
		if ( count($this->_SourceStats) == 0 ) {
			if ( !$this->getID() ) {
				throw new mvcModelException('Missing eventID');
			}
		
			$this->_SourceStats = array();
			if ( $this->getCurrentUser()->hasEvent($this->getID()) ) {
				foreach ( $this->getSourceSet() as $oSource ) {
					if (
						$this->getCurrentUser()->getClientID() == mofilmClient::MOFILM ||
						$this->getCurrentUser()->getPermissions()->isRoot() ||
						$this->getCurrentUser()->getSourceSet()->getObjectByID($oSource->getID())
					) {
						$oStats = new mofilmSourceStats($oSource, $this->getCurrentUser());
						$oStats->load();
						$this->_SourceStats[] = $oStats;
					}
				}
			}
		}
		return $this->_SourceStats;
	}
	
	/**
	 * Returns the grants stats based on $inData
	 * 
	 * @return array
	 */
	function getGrantStats() {
		if ( count($this->_GrantStats) == 0 ) {
			if ( !$this->getID() ) {
				throw new mvcModelException('Missing eventID');
			}
		
			$this->_GrantStats = array();
			if ( $this->getCurrentUser()->hasEvent($this->getID()) ) {
				foreach ( $this->getSourceSet() as $oSource ) {
					if (
						$this->getCurrentUser()->getClientID() == mofilmClient::MOFILM ||
						$this->getCurrentUser()->getPermissions()->isRoot() ||
						$this->getCurrentUser()->getSourceSet()->getObjectByID($oSource->getID())
					) {
						//$oStats = new mofilmSourceStats($oSource, $this->getCurrentUser());
						$oStats = new mofilmGrantsStats($oSource, $this->getCurrentUser());
						$oStats->load();
						$this->_GrantStats[] = $oStats;
					}
				}
			}
		}
		return $this->_GrantStats;
	}

	/**
	 * Returns a list of objects, optionally from $inOffset for $inLimit
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param boolean $inActiveOnly
	 * @return array
	 */
	function getObjectList($inOffset = null, $inLimit = 30, $inActiveOnly = null,$productID = null,$corporateID = null,$brandID = null,$orderBy = null) {
                $params = array();
                $params['CorporateID'] = $corporateID;
                $params['BrandID'] = $brandID;
                $params['ProductID'] = $productID;
                if($orderBy != null){
                    $params['OrderBy'] = $orderBy;
                }
                
                $events = mofilmEvent::listOfObjects($inOffset, $inLimit, ($inActiveOnly == 'Y' ? true : false), mofilmEvent::ORDERBY_STARTDATE,null,null,$params);
		return $events;
	}
	
	/**
	 * Returns the object primary key value
	 *
	 * @return string
	 */
	function getPrimaryKey() {
		return parent::getPrimaryKey();
	}
	
	/**
	 * Returns total object count for this table
	 *
	 * @return integer
	 */
	function getTotalObjects($active,$productid) {
            	$now = dbManager::getInstance()->quote(date(system::getConfig()->getDatabaseDatetimeFormat()));
		$query = '
			SELECT COUNT(*) AS Count
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.events ' ;
                        
                        if($productid){
                            $where[] = "productID = ".$productid;
                        }
                        
                        if ($active == "Y") {                            
                           $where[]=  ' (startdate <= '.$now.' OR startdate = "0000-00-00 00:00:00") AND (enddate > '.$now.' OR enddate = "0000-00-00 00:00:00")';
                        } 
                    if ( count($where) > 0 ) {
                            $query .= ' WHERE '.implode(' AND ', $where);
                    }

		
		$oRes = dbManager::getInstance()->query($query);
		$res = $oRes->fetch();
		if ( is_array($res) && count($res) > 0 ) {
			return $res['Count'];
		} else {
			return 0;
		}
	}
	
	/**
	 * Returns the limit needed to get to the last page of results
	 *
	 * @param integer $inLimit
	 * @return integer
	 */
	function getLastPageOffset($inLimit) {
		$total = $this->getTotalObjects(null,null);
		
		if ( $inLimit > 0 ) {
			return $inLimit*floor($total/$inLimit);
		} else {
			return 0;
		}
	}

	/**
	 * Returns a new blank object
	 *
	 * @return systemDaoInterface
	 */
	function getNewObject() {
		return new mofilmEvent();
	}
	
	/**
	 * Loads an existing object with $inPrimaryKey
	 *
	 * @param string $inPrimaryKey
	 * @return systemDaoInterface
	 */
	function getExistingObject($inPrimaryKey) {
		$this->setID($inPrimaryKey);
		$this->load();
		return $this;
	}

	/**
	 * Creates a tag with Event Name at the time of creating Event
	 */
	function addEventTag() {
		$oTag = mofilmTag::getInstanceByTagAndType($this->getName(), mofilmTag::TYPE_CATEGORY);

		if ( $oTag->getID() == 0 ) {
			$oMofilmTag = new mofilmTag();
			$oMofilmTag->setName($this->getName());
			$oMofilmTag->setType(mofilmTag::TYPE_CATEGORY);
			$oMofilmTag->save();
		}
		
		unset ($oMofilmTag);
		unset ($oTag);
		
		$year = date("Y", strtotime($this->getEndDate()));
		$oTag = mofilmTag::getInstanceByTagAndType($year, mofilmTag::TYPE_CATEGORY);
		if ( $oTag->getID() == 0 ) {
			$oMofilmTag = new mofilmTag();
			$oMofilmTag->setName($year);
			$oMofilmTag->setType(mofilmTag::TYPE_CATEGORY);
			$oMofilmTag->save();
		}
		
		unset ($oMofilmTag);
		unset ($oTag);
		
		return true;
	}
	
	/**
	 * Edit tag whenever Event Name is edited
	 */
	function editEventTag() {
		$oEvent = mofilmEvent::getInstance($this->getID());
		$oTag = mofilmTag::getInstanceByTagAndType($oEvent->getName(),  mofilmTag::TYPE_CATEGORY);
		
		if ( $oTag->getID() == 0 ) {
		    	$oMofilmTag = new mofilmTag();
			$oMofilmTag->setName($this->getName());
			$oMofilmTag->setType(mofilmTag::TYPE_CATEGORY);
			$oMofilmTag->save();
		} else {
			$oTag->setName($this->getName());
			$oTag->save();
		}
		return true;
	}
}