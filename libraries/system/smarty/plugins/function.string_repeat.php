<?php
/**
 * Smarty plugin
 * @package scorpio
 * @subpackage mvc
 * @category smarty_plugin
 * @version $Rev: 807 $
 */


/**
 * Smarty string_repeat modifier plugin
 *
 * Type:     modifier<br>
 * Name:     string_repeat<br>
 * Date:     Feb 24, 2003
 * Purpose:  repeats supplied string $repeat times
 * Input:    string to repeat
 * Example:  {$var|string_repeat:"x":34}
 * @author   Dave Redfern
 * @version 1.0
 * @param string
 * @param integer
 * @return string
 */
function smarty_function_string_repeat($params, $smarty)
{
	if (!isset($params['char'])) {
        $smarty->trigger_error("string_repeat: missing char parameter");
        return;
    }
    if (empty($params['repeat'])) {
        $smarty->trigger_error("string_repeat: missing repeat parameter");
        return;
    }
    return str_repeat($params['char'], $params['repeat']);
}

/* vim: set expandtab: */