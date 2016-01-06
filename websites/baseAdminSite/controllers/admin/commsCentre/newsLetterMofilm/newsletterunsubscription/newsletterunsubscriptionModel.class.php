<?php
/**
 * newsletterunsubscriptionModel.class.php
 * 
 * newsletterunsubscriptionModel class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category newsletterunsubscriptionModel
 * @version $Rev: 624 $
 */


/**
 * newsletterunsubscriptionModel class
 * 
 * Provides the "newsletterunsubscription" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category newsletterunsubscriptionModel
 */
class newsletterunsubscriptionModel extends mofilmCommsNewsletterunsubscription implements mvcDaoModelInterface {
	
	/**
	 * Returns a list of objects, optionally from $inOffset for $inLimit
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 */
	function getObjectList($inOffset = null, $inLimit = 30) {
		return mofilmCommsNewsletterunsubscription::listOfObjects($inOffset, $inLimit);
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
			  FROM '.system::getConfig()->getDatabase('mofilm_comms').'.newsletterUnsubscription';
		
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
		return new mofilmCommsNewsletterunsubscription();
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
	 * Returns the newsletter Name by ID
	 * @param integer $inNlId
	 * @return string $newsletterName
	 */
	function getNewsletterNameById($inNlId) {
	    $oNewsletter = mofilmCommsNewsletter::getInstance($inNlId);
	    $newsletterName = $oNewsletter->getName();
	    return $newsletterName;
	}

	/**
	 * Reuturs the emailAdrress by emailID
	 * @param intrger $inEmailId
	 * @return string $emailAddress
	 */
	function getEmailById($inEmailId) {
	    $oEmail = mofilmCommsEmail::getInstance($inEmailId);
	    $emailAddress = $oEmail->getEmail();
	    return $emailAddress;
	}
}