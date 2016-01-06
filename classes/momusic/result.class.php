<?php
/**
 * momusicSearchResult
 *
 * Stored in result.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage momusicSearchResult
 * @category momusicSearchResult
 * @version $Rev: 10 $
 */


/**
 * momusicSearchResult Class
 *
 * The main user search result container.
 *
 * @package mofilm
 * @subpackage momusicSearchResult
 * @category momusicSearchResult
 */
class momusicSearchResult extends momusicResultSet {

	/**
	 * Returns the instance matching $inKeyId which may be a string or integer
	 *
	 * @param string $inKeyId
	 * @return mofilmUser
	 */
	function getInstance($inKeyId) {
		if ( $this->getResultCount() > 0 ) {
			foreach ( $this as $oUser ) {
				if ( $oUser->getID() == $inKeyId ) {
					return $oUser;
				}
			}
		}
		return false;
	}
}