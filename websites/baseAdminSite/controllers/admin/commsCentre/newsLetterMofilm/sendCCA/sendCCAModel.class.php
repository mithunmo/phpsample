<?php
/**
 * sendCCAModel.class.php
 * 
 * sendCCAModel class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category sendCCAModel
 * @version $Rev: 624 $
 */


/**
 * sendCCAModel class
 * 
 * Provides the "sendCCA" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category sendCCAModel
 */
class sendCCAModel extends mofilmCommsNewsletterdata implements mvcDaoModelInterface {
	
	/**
	 * Returns a list of objects, optionally from $inOffset for $inLimit
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 */
	function getObjectList($inOffset = null, $inLimit = 30) {
		return mofilmCommsNewsletterdata::listOfObjects($inOffset, $inLimit);
	}
	
	/**
	 * Get the list of newsletterData objects where newsletterType is CCA
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param integer $inNewsletterType
	 * @return array 
	 */
	function getCCANLObjectList($inOffset = null, $inLimit = 30, $inNewsletterType) {
		return mofilmCommsNewsletterdata::listOfObjects($inOffset, $inLimit,$inNewsletterType);
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
			  FROM '.system::getConfig()->getDatabase('mofilm_comms').'.newsletterData
			       INNER JOIN '.system::getConfig()->getDatabase('mofilm_comms').'.newsLetter ON (newsletterData.newsletterID = newsLetter.ID)
			WHERE newsLetter.newsletterType = :Type';
		
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':Type', mofilmCommsNewsletterttype::T_CCA_REQUEST);
		$oStmt->execute();

		$res = $oStmt->fetch();
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
		return new mofilmCommsNewsletterdata();
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
	 * Gets the Newsletter name based on the ID
	 * @param integer $inNlId
	 * @return string
	 */
	function getNlNameById($inNlId){
		$oNewsletter = mofilmCommsNewsletter::getInstance($inNlId);
		$inName = $oNewsletter->getName();
		return $inName;

	}

	/**
	 * Returns the email address by ID
	 * @param integer $inID
	 * @return string email
	 */
	function getEmailById($inID){
		$oMofilmCommsSenderEmail = mofilmCommsSenderemail::getInstance($inID);
		return $oMofilmCommsSenderEmail->getSenderEmail();
	}
	
	/**
	 * Returns the details of Brand,newsletter name 
	 * 
	 * @param integer $inID
	 * @return integer 
	 */
	function getNewsletterDetails($inID) {
		$eventName =  mofilmEvent::getInstance(mofilmCommsNewsletterdata::getInstance($inID)->getParamSet()->getParam(mofilmCommsNewsletterdata::PARAM_NL_EVENTID))->getName();
		$brandID = preg_split("/,/",mofilmCommsNewsletterdata::getInstance($inID)->getParamSet()->getParam(mofilmCommsNewsletterdata::PARAM_NL_SOURCEID));
		$brandName = "";
		foreach ( $brandID as $inSourceID ) {
			$brandName.= mofilmSource::getInstance($inSourceID)->getName() . ", ";
		}
		
		if ( $brandName != "" ) {
			return " CCA for " . $brandName . "@ ".$eventName;
		}
	}
	
	/**
	 * Saves the Newsletter Object
	 * 
	 * @param array $inData
	 * @param string $cond
	 * 
	 * @return bool
	 */
	function saveObject($inData, $cond) {
		$this->reset();
		$this->setStatus(0);
		$this->setClassname("events");
		$this->setEmailName($inData['EmailName']);
		$this->setScheduledDate($inData['ScheduledDate']);
		$this->setMessageType(1);

		if ( $inData["EventID"] != "" ) {
			$this->getParamSet()->setParam(mofilmCommsNewsletterdata::PARAM_NL_EVENTID, $inData["EventID"]);				
		}

		if ( $inData["videoRating"] !=  "" ) {
			$this->getParamSet()->setParam(mofilmCommsNewsletterdata::PARAM_NL_VIDEO_RATING, $inData["videoRating"]);
		}

		if ( $inData["SourceID"] !=  "" ) {
			$this->getParamSet()->setParam(mofilmCommsNewsletterdata::PARAM_NL_SOURCEID, implode(",",$inData["SourceID"]));
		}

		if ( $cond == 'NlidS' ) {
			$this->setNewsletterID($inData['NlidS']);
			$this->setParams("4");
		} elseif ( $cond == 'NlidNs' ) {
			$this->setNewsletterID($inData['NlidNs']);
			$this->setParams("5");
		} elseif ( $cond == 'NlidNw' ) {
			$this->setNewsletterID($inData['NlidNw']);
			$this->setParams("6");
		}
		$this->save();
		
		return TRUE;
	}
}