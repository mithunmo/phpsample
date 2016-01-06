<?php
/**
 * mofilmSystemHelpPageSearchResult
 *
 * Stored in result.class.php
 *
 * @author Pavan Kumar P G
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmSystemHelpPages
 * @category mofilmSystemHelpPageSearchResult
 * @version $Rev: 806 $
 */


/**
 * mofilmSystemHelpPageSearchResult Class
 *
 * The main help page search result container.
 *
 * @package mofilm
 * @subpackage mofilmSystemHelpPages
 * @category mofilmSystemHelpPageSearchResult
 */
class mofilmSystemHelpPageSearchResult extends baseResultSet {

	/**
	 * Returns the instance matching $inKeyId which may be a string or integer
	 *
	 * @param string $inKeyId
	 * @return mofilmSystemHelpPages
	 */
	function getInstance($inKeyId) {
		if ( $this->getResultCount() > 0 ) {
			foreach ( $this as $oObject ) {
				/* @var mofilmSystemHelpPages $oObject */
				if ( $oObject->getID() == $inKeyId ) {
					return $oObject;
				}
			}
		}
		return false;
	}
}