<?php
/**
 * commsOutboundSearchResult
 *
 * Stored in result.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage outbound
 * @category commsOutboundSearchResult
 * @version $Rev: 10 $
 */


/**
 * commsOutboundSearchResult Class
 *
 * The main outbound message search result container.
 *
 * @package comms
 * @subpackage outbound
 * @category commsOutboundSearchResult
 */
class commsOutboundSearchResult extends baseResultSet {

	/**
	 * Returns the instance matching $inKeyId which may be a string or integer
	 *
	 * @param string $inKeyId
	 * @return commsOutboundMessage
	 */
	function getInstance($inKeyId) {
		if ( $this->getResultCount() > 0 ) {
			foreach ( $this as $oObject ) {
				if ( $oObject->getMessageID() == $inKeyId ) {
					return $oObject;
				}
			}
		}
		return false;
	}
}