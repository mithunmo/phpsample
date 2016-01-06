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
 * Converts $input into a JSON encoded string
 *
 * Type:     modifier<br>
 * Name:     asJson<br>
 * Date:     Apr 7, 2010
 * Purpose:  Converts $input into a JSON encoded string
 * Input:    string
 * Example:  {$var|asJson}
 * @author   Dave Redfern
 * @version 1.0
 * @param string
 * @return string
 */
function smarty_modifier_asJson($input) {
	if ( $input instanceof utilityOutputWrapper || $input instanceof utilityOutputWrapperArray ) {
		$input = $input->getSeed();
	}
	return json_encode($input, (is_object($input) || is_array($input) ? JSON_FORCE_OBJECT : null));
}