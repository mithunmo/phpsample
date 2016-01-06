<?php
/**
 * Smarty plugin
 *
 * @package mofilm
 * @subpackage websites_base
 * @category smarty_plugin
 * @version $Rev: 11 $
 */


/**
 * Converts date of birth to an age in years
 *
 * Type:     modifier<br>
 * Name:     convertDateOfBirthToYears<br>
 * Date:     Feb 11, 2010
 * Purpose:  Converts a date of birth to age in years
 * Input:    $dob
 * Example:  {$dob|convertDateOfBirthToYears}
 * @author   Dave Redfern
 * @version 1.0
 * @param string
 * @return string
 */
function smarty_modifier_convertDateOfBirthToYears($dob) {
	if ( strlen($dob) > 0 ) {
		$oNow = systemDateTime::getInstance();
		$oDate = systemDateTime::getInstance($dob);
		
		return $oDate->diff($oNow)->format('%Y');
	}
}