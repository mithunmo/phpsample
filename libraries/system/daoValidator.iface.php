<?php
/**
 * systemDaoValidator.class.php
 * 
 * System systemDaoValidator Interface
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemDaoValidatorInterface
 * @version $Rev: 650 $
 */


/**
 * systemDaoValidator
 * 
 * systemDaoValidator Interface
 * 
 * @package scorpio
 * @subpackage system
 * @category systemDaoValidatorInterface
 */
interface systemDaoValidatorInterface {
 	
	/**
	 * Returns true if the object is valid
	 *
	 * @param string $inMessage
	 * @return boolean
	 */
 	function isValid(&$inMessage = '');
}