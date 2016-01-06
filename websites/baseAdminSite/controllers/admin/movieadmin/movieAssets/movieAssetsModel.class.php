<?php
/**
 * movieAssetsModel.class.php
 * 
 * movieAssetsModel class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category movieAssetsModel
 * @version $Rev: 11 $
 */


/**
 * movieAssetsModel class
 * 
 * Provides the "movieAssets" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category movieAssetsModel
 */
class movieAssetsModel extends mofilmMovieAsset implements mvcDaoModelInterface {
	
	/**
	 * Returns a list of objects, optionally from $inOffset for $inLimit
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param integer $inMovieID
	 * @param string $inType
	 * @param string $inFilename
	 * @return array
	 */
	function getObjectList($inOffset = null, $inLimit = 30, $inMovieID = null, $inType = null, $inFilename = null) {
		if ( strtolower($inFilename) == 'search by filename' || strlen($inFilename) < 3) {
			$inFilename = null;
		}
		if ( $inMovieID && is_numeric($inMovieID) ) {
			$this->setMovieID($inMovieID);
		} else {
			$inMovieID = null;
		}
		if ( in_array($inType, self::getTypes()) ) {
			$this->setType($inType);
		} else {
			$inType = null;
		}
		
		if ( $inFilename !== null ) {
			$this->setFilename('%'.str_replace(' ', '%', $inFilename).'%');
		}
		
		return mofilmMovieAsset::listOfObjects($inOffset, $inLimit, $inMovieID, $inType, $inFilename);
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
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieAssets
			 WHERE 1';
		
		if ( $this->getMovieID() ) {
			$query .= ' AND movieID = '.dbManager::getInstance()->quote($this->getMovieID());
		}
		if ( $this->getType() ) {
			$query .= ' AND type = '.dbManager::getInstance()->quote($this->getType());
		}
		if ( $this->getFilename() ) {
			$query .= ' AND filename LIKE '.dbManager::getInstance()->quote($this->getFilename());
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
		return new mofilmMovieAsset();
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
}