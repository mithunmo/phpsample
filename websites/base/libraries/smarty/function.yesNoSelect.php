<?php
/**
 * Smarty plugin
 * @package mofilm
 * @subpackage mvc
 * @category smarty_plugin
 * @version $Rev: 11 $
 */

/**
 * Smarty {yesNoSelect} plugin
 *
 * Type:     function<br>
 * Name:     yesNoSelect<br>
 * Purpose:  Builds a boolean yes/no selector box
 * @author Dave Redfern
 * @param array Format:
 * Input:<br>
*            - name       (optional) - string default "select"
*            - selected   (optional) - string default not set
 * @param Smarty
 * @return string
 */
function smarty_function_yesNoSelect($params, $smarty, $template = null) {
	
	require_once dirname(__FILE__).'/function.booleanSelect.php';
	//$smarty->loadPlugin('smarty_function_booleanSelect');
	
	$params['true'] = 'Yes';
	$params['trueValue'] = 'Y';
	$params['false'] = 'No';
	$params['falseValue'] = 'N';
	
	return smarty_function_booleanSelect($params, $smarty, $template);
}