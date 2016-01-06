<?php
/**
 * Smarty plugin
 * @package scorpio
 * @subpackage mvc
 * @category smarty_plugin
 * @version $Rev: 807 $
 */


/**
 * Smarty printr modifier plugin
 *
 * Type:     modifier<br>
 * Name:     print<br>
 * Date:     May 27 2007
 * Purpose:  does a print_r on the value
 * Input:    var to display
 * Example:  {$var|printr}
 * @author   Dave Redfern
 * @version 1.0
 * @param mixed
 * @return string
 */
function smarty_modifier_printr($var)
{
    return '<pre>'.print_r($var,1).'</pre>';
}

/* vim: set expandtab: */