<?php
/**
 * CoverImageModel.class.php
 * 
 * CoverImageModel class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category CoverImageModel
 * @version $Rev: 624 $
 */


/**
 * CoverImageModel class
 * 
 * Provides the "CoverImage" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category CoverImageModel
 */
class CoverImageModel extends momusicCoverimage implements mvcDaoModelInterface {
	
	/**
	 * Returns a list of objects, optionally from $inOffset for $inLimit
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 */
	function getObjectList($inOffset = null, $inLimit = 30) {
		return momusicCoverimage::listOfObjects($inOffset, $inLimit);
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
		 * @todo change database and table to the ones required for CoverImageModel
		 */
		$query = '
			SELECT COUNT(*) AS Count
			  FROM '.system::getConfig()->getDatabase('momusic_content').'.coverImage';
		
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
		return new momusicCoverimage();
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