<?php
/**
 * wurflResultSet class
 * 
 * Holds the results from a search for devices
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage wurfl
 * @category wurflResultSet
 * @version $Rev: 650 $
 */


/**
 * wurflResultSet
 * 
 * Holds the search results from a search for devices
 * 
 * @package scorpio
 * @subpackage wurfl
 * @category wurflResultSet
 */
class wurflResultSet extends baseResultSet {
	
	/**
	 * Returns device record
	 *
	 * @param integer $inKeyId
	 * @return wurflDevice
	 */
	function getInstance($inKeyId) {
		if ( $this->getResultCount() > 0 ) {
			foreach ( $this->getResults() as $oDevice ) {
				if ( $oDevice->getDeviceID() == $inKeyId ) {
					return $oDevice; 
				}
			}
		}
		return new wurflDevice();
	}
}