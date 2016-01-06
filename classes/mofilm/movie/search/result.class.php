<?php
/**
 * mofilmMovieSearchResult
 *
 * Stored in result.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmMovieSearchResult
 * @category mofilmMovieSearchResult
 * @version $Rev: 10 $
 */


/**
 * mofilmMovieSearchResult Class
 *
 * The main movie search result container.
 *
 * @package mofilm
 * @subpackage mofilmMovieSearchResult
 * @category mofilmMovieSearchResult
 */
class mofilmMovieSearchResult extends baseResultSet {

	/**
	 * Returns the instance matching $inKeyId which may be a string or integer
	 *
	 * @param string $inKeyId
	 * @return mofilmMovie
	 */
	function getInstance($inKeyId) {
		if ( $this->getResultCount() > 0 ) {
			foreach ( $this as $oResult ) {
				if ( $oResult->getID() == $inKeyId ) {
					return $oResult;
				}
			}
		}
		return false;
	}
	
	/**
	 * Returns the array key matching $inMovieID, returns false if not found
	 * 
	 * @param integer $inMovieID
	 * @return integer
	 */
	function getInstanceIndex($inMovieID) {
		if ( $this->getResultCount() > 0 ) {
			foreach ( $this as $key => $oResult ) {
				if ( $oResult->getID() == $inMovieID ) {
					return $key;
				}
			}
		}
		return false;
	}
	
	/**
	 * Returns the first result from the result set, false if no result
	 * 
	 * @return mofilmMovie
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
	 * @return mofilmMovie
	 */
	function getLastResult() {
		if ( array_key_exists($this->getResultCount()-1, $this->_Results) ) {
			return $this->_Results[$this->getResultCount()-1];
		}
		return false;
	}
}