<?php
/**
 * utilityFilterInterface Class
 * 
 * Stored in utilityFilterInterface.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityFilterInterface
 * @version $Rev: 844 $
 */


/**
 * utilityFilterInterface Class
 * 
 * Interface for filtering classes so that additional filters can be added
 * to {@link utilityInputManager} with ease and extend the capabilities.
 * 
 * @package scorpio
 * @subpackage utility
 * @category utilityFilterInterface
 */
interface utilityFilterInterface {
	
	/**
	 * Filters supplied data and returns the filtered output
	 *
	 * @param mixed $inData The data to filter
	 * @return mixed
	 * @access public
	 */
	public function doFilter($inData = null);

	/**
	 * Returns filtered data from the specified global, usually GET or POST
	 *
	 * @see http://ca.php.net/filter_input
	 * @param integer $inGroup INPUT_ constant
	 * @param string $inField Name of the field to filter
	 * @return mixed
	 */
	public function doGetFilter($inGroup, $inField);
}