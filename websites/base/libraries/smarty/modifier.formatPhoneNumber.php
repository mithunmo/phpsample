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
 * Removes non-numeric chars from string (eg +123-345 6789 => 1233456789)
 *
 * Type:     modifier<br>
 * Name:     formatPhoneNumber<br>
 * Date:     Feb 11, 2010
 * Purpose:  removes non-numeric chars from string (eg +123-345 6789 => 1233456789)
 * Input:    phone number string
 * Example:  {$phone|formatPhoneNumber}
 * @author   Dave Redfern
 * @version 1.0
 * @param string
 * @return string
 */
function smarty_modifier_formatPhoneNumber($string) {
	return preg_replace("/[^0-9]/", '', $string);
}