<?php
/**
 * Smarty plugin
 * @package scorpio
 * @subpackage mvc
 * @category smarty_plugin
 * @version $Rev: 638 $
 */

/**
 * Smarty default modifier plugin
 *
 * Type:     modifier<br>
 * Name:     xmlstring<br>
 * Purpose:  replace illegal wml / xml entities with something nice and safe
 * @author   Dave Redfern 
 * @param string
 * @return string
 */
function smarty_modifier_xmlstring($string) {
	return utilityStringFunction::xmlString($string);
}
