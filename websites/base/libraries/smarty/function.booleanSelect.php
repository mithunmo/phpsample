<?php
/**
 * Smarty plugin
 * @package mofilm
 * @subpackage mvc
 * @category smarty_plugin
 * @version $Rev: 11 $
 */

/**
 * Smarty {booleanSelect} plugin
 *
 * Type:     function<br>
 * Name:     booleanSelect<br>
 * Purpose:  Builds a boolean selector box
 * @author Dave Redfern
 * @param array Format:
 * Input:<br>
*            - name       (optional) - string default "select"
*            - selected   (optional) - string default not set
*            - true       (optional) - string value for true default "true"
*            - trueValue  (optional) - representation of true default 1
*            - false      (optional) - string value for false default "false"
*            - falseValue (optional) - representation of false default 0
 * @param Smarty
 * @return string
 */
function smarty_function_booleanSelect($params, $smarty, $template = null) {
	require_once(SMARTY_PLUGINS_DIR . 'function.html_options.php');
	
	$true = isset($params['true']) ? $params['true'] : 'True';
	$trueValue = isset($params['trueValue']) ? $params['trueValue'] : 1;
	$false = isset($params['false']) ? $params['false'] : 'False';
	$falseValue = isset($params['falseValue']) ? $params['falseValue'] : 0;
	
	unset($params['true'], $params['false'], $params['trueValue'], $params['falseValue']);
	
	$params['options'] = array(
		$trueValue => $true,
		$falseValue => $false
	);
	
	return smarty_function_html_options($params, $smarty, $template);
}