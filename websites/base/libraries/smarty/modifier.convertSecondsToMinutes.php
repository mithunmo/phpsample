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
 * Converts value in seconds into minutes and seconds as a string
 *
 * Type:     modifier<br>
 * Name:     convertSecondsToMinutes<br>
 * Date:     Feb 11, 2010
 * Purpose:  Converts value in seconds into minutes and seconds as a string
 * Input:    terms ID for the record
 * Example:  {$seconds|convertSecondsToMinutes}
 * @author   Chris Noden
 * @version 1.0
 * @param string
 * @return string
 */
function smarty_modifier_convertSecondsToMinutes($seconds) {
	if ( $seconds > 0 ) {
        $mins = floor ($seconds / 60);
        $secs = $seconds % 60;
        
        return $mins . "m " .$secs . "s";
    }
}