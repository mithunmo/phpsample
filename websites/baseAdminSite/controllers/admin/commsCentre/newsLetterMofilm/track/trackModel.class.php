<?php
/**
 * trackModel.class.php
 *
 * trackModel class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category trackModel
 * @version $Rev: 624 $
 */


/**
 * trackModel class
 *
 * Provides the "track" page
 *
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category trackModel
 */
class trackModel extends mofilmCommsNewsletterhistory implements mvcDaoModelInterface {

	/**
	 * Returns a list of objects, optionally from $inOffset for $inLimit
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 */
	function getObjectList($inOffset = null, $inLimit = 30, $inParam = null) {
		return mofilmCommsNewsletterhistory::listOfObjects($inOffset, $inLimit);
	}

	/**
	 * Returns a list of objects
	 *
	 * @param integer $inId
	 * @return array
	 */
	function getObjectListByNlId($inId,$inOffset = null, $inLimit = 30) {
		return mofilmCommsNewsletterhistory::getNlById($inId,$inOffset,$inLimit);
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
			  FROM ' . system::getConfig()->getDatabase('mofilm_comms') . '.newsletterHistory';

		$oRes = dbManager::getInstance()->query($query);
		$res = $oRes->fetch();
		if ( is_array($res) && count($res) > 0 ) {
			return $res['Count'];
		} else {
			return 0;
		}
	}


	/**
	 * Returns total object count for this table
	 * 
	 * @param integer inNlId
	 * @return Array
	 */
	function getTotalObjectsOfNl($inNlId) {

		$query = 'SELECT COUNT(*) AS Count FROM ' . system::getConfig()->getDatabase('mofilm_comms') . '.newsletterHistory';
		$query .= ' WHERE newsletterID =' . $inNlId;

		$oRes = dbManager::getInstance()->query($query);
		$res = $oRes->fetch();
		if ( is_array($res) && count($res) > 0 ) {
			return $res['Count'];
		} else {
			return 0;
		}

	}

	/**
	 * Returns total object count for this table
	 *
	 * @return integer
	 */
	function getTotalObjectsReadOfNl($inNlId) {

		$query = 'SELECT COUNT(*) AS Count FROM ' . system::getConfig()->getDatabase('mofilm_comms') . '.newsletterHistory';
		$query .= ' WHERE newsletterID = ' . $inNlId . ' AND status = 1';
		$oRes = dbManager::getInstance()->query($query);
		$res = $oRes->fetch();
		if ( is_array($res) && count($res) > 0 ) {
			return $res['Count'];
		} else {
			return 0;
		}

	}

	/**
	 * Returns total object count for this table
	 * 
	 * @param integer NlId
	 * @return Array
	 */
	function getReadArray($inNl) {
		$list = mofilmCommsNewsletterhistory::getReadCountByDate($inNl);
		$resultList = array();
		for ( $i = 0; $i < count($list); $i++ ) {
			$oNlHis = $list[$i];
			$key = $oNlHis->getUpdatedate();
			if ( !array_key_exists($key, $resultList) ) {
				$resultList[$key] = 1;
			} else {
				$value = $resultList[$key];
				$value++;
				$resultList[$key] = $value;

			}

		}
		$resArray = array(array());
		$i = 0;
		$j = 0;
		foreach ( $resultList as $key => $value ) {
			$resArray[$i][$j] = $key;
			$j++;
			$resArray[$i][$j] = $value;
			$i++;
			$j--;
		}
		return $resArray;
	}

	/**
	 * Returns the total number of read records
	 *
	 * @param integer $inNl
	 * @return integer
	 */
	function getTickInterval($inNl) {
		return count(mofilmCommsNewsletterhistory::getReadCountByDate($inNl));

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
		return new mofilmCommsNewsletterhistory();
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
	 * Gets the email address from the userID
	 *
	 * @param integer $inUid
	 */
	function getEmailById($inUid){
		$oUser = mofilmUserManager::getInstanceByID($inUid);
		
		if ( isset($oUser) && $oUser instanceof mofilmUser && $oUser->getID() > 0 ) {
			return $oUser->getEmail();
		} else {
			return mofilmCommsEmail::getInstanceByUser($inUid)->getEmail();
		}
	}
}