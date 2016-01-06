<?php
/**
 * mofilmLeaderboardResult
 *
 * Stored in result.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmLeaderboardResult
 * @category mofilmLeaderboardResult
 * @version $Rev: 10 $
 */


/**
 * mofilmLeaderboardResult Class
 *
 * The leaderboard search result container.
 *
 * @package mofilm
 * @subpackage mofilmLeaderboardResult
 * @category mofilmLeaderboardResult
 */
class mofilmLeaderboardResult extends baseResultSet {

	/**
	 * Returns the instance matching $inKeyId which may be a string or integer
	 *
	 * @param string $inKeyId
	 * @return mofilmUser
	 */
	function getInstance($inKeyId) {
		if ( $this->getResultCount() > 0 ) {
			/* @var mofilmUser $oResult */
			foreach ( $this as $oResult ) {
				if ( $oResult->getID() == $inKeyId ) {
					return $oResult;
				}
			}
		}
		return false;
	}
	
	/**
	 * Returns the array key matching $inUserID, returns false if not found
	 * 
	 * @param integer $inUserID
	 * @return integer
	 */
	function getInstanceIndex($inUserID) {
		if ( $this->getResultCount() > 0 ) {
			/* @var mofilmUser $oResult */
			foreach ( $this as $key => $oResult ) {
				if ( $oResult->getID() == $inUserID ) {
					return $key;
				}
			}
		}
		return false;
	}
	
	/**
	 * Returns the first result from the result set, false if no result
	 * 
	 * @return mofilmUser
	 */
	function getFirstResult() {
		if ( array_key_exists(0, $this->_Results) ) {
			return $this->_Results[0];
		}
		return false;
	}
	
	/**
	 * Gets the last result from the result set, if available
	 * 
	 * @return mofilmUser
	 */
	function getLastResult() {
		if ( array_key_exists($this->getResultCount()-1, $this->_Results) ) {
			return $this->_Results[$this->getResultCount()-1];
		}
		return false;
	}
}