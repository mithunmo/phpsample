<?php
/**
 * sendnewsletterModel.class.php
 *
 * sendnewsletterModel class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category sendnewsletterModel
 * @version $Rev: 624 $
 */


/**
 * sendnewsletterModel class
 *
 * Provides the "sendnewsletter" page
 *
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category sendnewsletterModel
 */
class sendnewsletterModel extends mofilmCommsNewsletterdata implements mvcDaoModelInterface {

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
	 * Gets all the newsletter which are of type marketing
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param integer $inNewsletterType
	 * @return array 
	 */
	function getMarketingNLObjectList($inOffset = null, $inLimit = 30, $inNewsletterType) {
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
			INNER JOIN '.system::getConfig()->getDatabase('mofilm_comms').'.newsLetter	
			ON newsletterData.newsletterID = newsLetter.ID	
			WHERE newsletterType = 1 ';

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
			return $inLimit * floor($total / $inLimit);
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
	 * Update the status based on the id
	 *
	 * @param integer $inId
	 * @param integer $inStatus
	 * @return void
	 */
	function updateNewsLetterStatus($inId, $inStatus) {
		mofilmCommsNewsletterdata::updateStatus($inId, $inStatus);
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
	 * Gets the evenID from the params
	 *
	 * @return integer
	 */
	function getEventID() {
		return $this->getParamSet()->getParam(mofilmCommsNewsletterdata::PARAM_NL_EVENTID);
	}

	/**
	 * Gets the FilterID from the params
	 *
	 * @return string
	 */
	function getFilterID() {
		return $this->getParams();
	}

	/**
	 * Gets the ratingID from the params
	 *
	 * @return integer
	 */
	function getRatingID() {
		return $this->getParamSet()->getParam(mofilmCommsNewsletterdata::PARAM_NL_VIDEO_RATING);
	}	
}
