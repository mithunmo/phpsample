<?php
/**
 * downloadsModel.class.php
 * 
 * downloadsModel class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category downloadsModel
 * @version $Rev: 624 $
 */


/**
 * downloadsModel class
 * 
 * Provides the "downloads" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category downloadsModel
 */
class downloadsModel extends mofilmUserMusicLicense implements mvcDaoModelInterface {
	
	/**
	 * Returns a list of objects, optionally from $inOffset for $inLimit
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 */
	function getObjectList($inOffset = null, $inLimit = 30) {
		
		return mofilmUserMusicLicense::listOfTotalDownloads($inOffset,$inLimit);
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
		/**
		 * @todo change database and table to the ones required for downloadsModel
		 */
		
		$query = '
			SELECT count(*) as Count
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userLicenses
			 INNER JOIN '.system::getConfig()->getDatabase('momusic_content').'.work 
		     ON work.ID = userLicenses.trackID where userLicenses.trackID != 0 group by userLicenses.trackID
		';
				
		$oRes = dbManager::getInstance()->query($query);
		$res = $oRes->fetch();
		$resCnt = $oRes->rowCount();
		
		if ( $resCnt > 0 ) {
			return $resCnt;
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
		return new mofilmUserMusicLicense();
	}
	
	/**
	 * Loads an existing object with $inPrimaryKey
	 *
	 * @param string $inPrimaryKey
	 * @return systemDaoInterface
	 */
	function getExistingObject($inPrimaryKey) {
		/**
		 * @todo set primary key for this object
		 */
		$this->setID($inPrimaryKey);
		$this->load();
		return $this;
	}
}