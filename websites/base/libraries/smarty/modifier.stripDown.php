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
 * Removes non-alphanumeric chars from string (eg Hello World => HelloWorld)
 *
 * Type:     modifier<br>
 * Name:     stripDown<br>
 * Date:     Feb 11, 2010
 * Purpose:  removes non-alphanumeric chars from string (eg Hello World => HelloWorld)
 * Input:    string
 * Example:  {$termsID|stripDown}
 * @author   Chris Noden
 * @version 1.0
 * @param string
 * @return string
 */
function smarty_modifier_stripDown($string) {
	return preg_replace("/[^0-9a-zA-Z]/", '', $string);
}