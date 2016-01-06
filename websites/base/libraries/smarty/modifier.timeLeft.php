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
 * Returns a human friendly time between now and the provided end date
 *
 * Type:     modifier<br>
 * Name:     timeLeft<br>
 * Date:     Feb 11, 2010
 * Purpose:
 * Input:    datetime in valid strtotime format
 * Example:  {$var|timeLeft}
 * @author   Chris Noden
 * @version 1.0
 * @param datetime
 * @return string
 */
function smarty_modifier_timeLeft($enddate) {
	// seconds
	$seconds = intval(strtotime($enddate) - date('U'));
	if ($seconds < 0) {
		return "FINISHED - no time ";
	}
	
	// days
	$days = intval($seconds/86400);
	if ($days > 4) {
		return sprintf("%s days", $days);
	}
	
	// hours
	if ($days > 3) {
		$hours = intval(($seconds/3600) - ($days * 24));
		return sprintf("%s days %s hours", $days, $hours);
	} else {
		$hours = intval($seconds/3600);
	}
	if ($hours > 24) {
		return sprintf("%s hours", $hours);
	}
	
	if ($hours > 0) {
		// hours & minutes
		$minutes = intval(($seconds/60) - ($hours * 60));
		return sprintf("%s hour%s %s min%s", $hours, $hours == 1 ? '' : 's', $minutes, $minutes == 1 ? '' : 's');
	}
	
	return sprintf("%s mins %s secs", intval($seconds/60), $seconds - intval($seconds/60)*60);
}