<?php
/**
 * mofilmUserMovieGrantsSearchResult
 *
 * Stored in result.class.php
 *
 * @author Pavan Kumar P G
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmUserMovieGrantsSearchResult
 * @category mofilmUserMovieGrantsSearchResult
 * @version $Rev: 10 $
 */


/**
 * mofilmUserMovieGrantsSearchResult Class
 *
 *
 * @package mofilm
 * @subpackage mofilmUserMovieGrantsSearchResult
 * @category mofilmUserMovieGrantsSearchResult
 */
class mofilmUserMovieGrantsSearchResult extends baseResultSet {

	/**
	 * Returns the instance matching $inKeyId which may be a string or integer
	 *
	 * @param string $inKeyId
	 * @return mofilmUserMovieGrants
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
}