<?php
/**
 * momusicWorkSearchResult
 *
 * Stored in result.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage momusicWorkSearchResult
 * @category momusicWorkSearchResult
 * @version $Rev: 10 $
 */


/**
 * momusicWorkSearchResult Class
 *
 * The main user search result container.
 *
 * @package mofilm
 * @subpackage momusicWorkSearchResult
 * @category momusicWorkSearchResult
 */
class momusicWorkSearchResult extends baseResultSet {

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