<?php
/**
 * Smarty plugin
 * @package scorpio
 * @subpackage mvc
 * @category smarty_plugin
 * @version $Rev: 807 $
 */


/**
 * Smarty spacifyCapitalisedString modifier plugin
 *
 * Type:     modifier<br>
 * Name:     spacifyCapitalisedString<br>
 * Date:     May 27 2007
 * Purpose:  converts strings such as SomethingLikeThis to Something Like This
 * Input:    var to display
 * Example:  {$var|spacifyCapitalisedString}
 * @author   Dave Redfern
 * @version 1.0
 * @param mixed
 * @return string
 */
function smarty_modifier_spacifyCapitalisedString($var)
{
    return utilityStringFunction::convertCapitalizedString($var);
}

/* vim: set expandtab: */