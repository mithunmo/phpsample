<?php
/**
 * sourceManagerModel.class.php
 * 
 * sourceManagerModel class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category sourceManagerModel
 * @version $Rev: 241 $
 */


/**
 * sourceManagerModel class
 * 
 * Provides the "sourceManager" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category sourceManagerModel
 */
class sourceManagerModel extends mofilmSource implements mvcDaoModelInterface {
	
	/**
	 * Stores $_CurrentUser
	 *
	 * @var mofilmUser
	 * @access protected
	 */
	protected $_CurrentUser;

	

	/**
	 * Returns a list of objects, optionally from $inOffset for $inLimit
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param integer $inEventID
	 * @param boolean $inVisibleOnly
	 * @return array
	 */
	function getObjectList($inOffset = null, $inLimit = 30, $inEventID = null, $inVisibleOnly = true) {
		$this->setEventID($inEventID);
		if ( $this->getCurrentUser() instanceof mofilmUser && $this->getCurrentUser()->getClientID() != mofilmClient::MOFILM ) {
			$sources = $this->getCurrentUser()->getSourceSet()->getObjectIDs();
		} else {
			$sources = array();
		}
		
		if ( $inEventID == 0 ) {
			return mofilmSource::listOfDistinctSourceNameObjects();
		} else {
			return mofilmSource::listOfObjects($inOffset, $inLimit, $inEventID, $sources, $inVisibleOnly, mofilmSource::ORDERBY_ID);
		}
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
	function getTotalObjects() {
		$query = '
			SELECT COUNT(*) AS Count
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.sources';
		if ( $this->getEventID() ) {
			$query .= ' WHERE eventID = '.dbManager::getInstance()->quote($this->getEventID());
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
		$total = $this->getTotalObjects();
		
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
		return new mofilmSource();
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
	 * Returns $_CurrentUser
	 *
	 * @return mofilmUser
	 */
	function getCurrentUser() {
		return $this->_CurrentUser;
	}
	
	/*
	 * 
	 */
	function deletePrizeByID($inSourcePrizeID = null) {
		if ($inSourcePrizeID) {
			$oSourcePrizeSet = new mofilmSourcePrizeSet();
			$oSourcePrizeSet->setID($inSourcePrizeID);
			$res = $oSourcePrizeSet->delete();
		}
		return false;
	}
	
	/**
	 * Set $_CurrentUser to $inCurrentUser
	 *
	 * @param mofilmUser $inCurrentUser
	 * @return sourceManagerModel
	 */
	function setCurrentUser($inCurrentUser) {
		if ( $inCurrentUser !== $this->_CurrentUser ) {
			$this->_CurrentUser = $inCurrentUser;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Creates new tag at the time of Source Name creation
	 */
	function addSourceTag() {
		$oTag = mofilmTag::getInstanceByTagAndType($this->getName(), mofilmTag::TYPE_CATEGORY);

		if ( $oTag->getID() == 0 ) {
			$oMofilmTag = new mofilmTag();
			$oMofilmTag->setName($this->getName());
			$oMofilmTag->setType(mofilmTag::TYPE_CATEGORY);
			$oMofilmTag->save();
		}
		return true;
	}
	
	/**
	 * Edit tag whenever Source Name is edited
	 */
	function editSourceTag() {
		$oSource = mofilmSource::getInstance($this->getID());
		$oTag = mofilmTag::getInstanceByTagAndType($oSource->getName(),  mofilmTag::TYPE_CATEGORY);
		
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
        
        function getCorporateModel($brandID){
            
            $getCorporateQuery  =  'SELECT corporate.ID,corporate.name
                                    FROM '.system::getConfig()->getDatabase('mofilm_content').'.corporate
                                    JOIN '.system::getConfig()->getDatabase('mofilm_content').'.brands
                                    ON corporate.ID = brands.corporateID
                                    WHERE brands.ID ='.$brandID;
            $cRes = dbManager::getInstance()->query($getCorporateQuery);  
            $corporate = $cRes->fetch(); 
            return $corporate['ID'];
        }
        
        function getApprovedAmount($sourceID,$eventID){
           $getAmount   =  'SELECT SUM(payableAmount) as TOTAL FROM '.system::getConfig()->getDatabase('mofilm_content').'.paymentDetails 
                            WHERE paymentDetails.sourceID= '.$sourceID." AND paymentDetails.eventID = ".$eventID." 
                            AND paymentDetails.hasMultipart = 0  AND paymentDetails.status != 'Draft'
                            AND paymentDetails.paymentType IN ('Edits','Production Fee','Fee') GROUP BY paymentDetails.eventID,paymentDetails.sourceID";
            $aRes = dbManager::getInstance()->query($getAmount);  
            $approvedAmt = $aRes->fetch(); 
            return $approvedAmt['TOTAL'];
        }
        
        function getUser($inUserID){
            if ($inUserID == 0 ){
                return "MOFILM";
            } else {
                return mofilmUserManager::getInstanceByID($inUserID)->getFullname();
            }
        }
        
        function getlink($sourceID){
            $oSource = mofilmSource::getInstance($sourceID);
            $eventName = str_replace(" ","-",$oSource->getEvent()->getName());
            $sourceName = str_replace(" ","-",$oSource->getName());
            $link = "https://www.mofilm.com/competitions/brand/".$eventName."/".$sourceName;
            return $link;
        }
        
    }