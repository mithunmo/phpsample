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
 * Renders a generic help box wrapped in a jQueryUI element set
 *
 * Type:     function<br>
 * Name:     help<br>
 * Date:     Jan 14, 2011
 * Purpose:
 * Input:    Help text to insert
 * Example:  {help text="Text to display"}
 * @author   Dave Redfern
 * @version 1.0
 * @param integer
 * @return string
 */
function smarty_function_help($params, $smarty, $template = null) {
	if ( !isset($params['text']) ) {
		$params['text'] = 'Element help';
	}
	try {
		$oView = $smarty->getTemplateVars('oView');
		$smarty->assign('helpText', $params['text']);
		$return = $smarty->fetch($oView->getTpl('helpTemplate', '/shared'));
	} catch ( Exception $e ) {
		$return = '';
	}
	return $return;
}