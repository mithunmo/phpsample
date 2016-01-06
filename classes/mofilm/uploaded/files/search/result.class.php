<?php
/**
 * mofilmUploadedFilesSearchResult
 *
 * Stored in result.class.php
 *
 * @author Pavan Kumar P G
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmUploadedFilesSearchResult
 * @category mofilmUploadedFilesSearchResult
 * @version $Rev: 1 $
 */


/**
 * mofilmUploadedFilesSearchResult Class
 *
 *
 * @package mofilm
 * @subpackage mofilmUploadedFilesSearchResult
 * @category mofilmUploadedFilesSearchResult
 */
class mofilmUploadedFilesSearchResult extends baseResultSet {

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