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
 * Adds the JS file to the internal view stack of JS files
 *
 * Type:     function<br>
 * Name:     js<br>
 * Date:     Jan 14, 2011
 * Purpose:
 * Input:    Movie ID to create a link for
 * Example:  {js file='path/to/js/file.js'}
 * @author   Dave Redfern
 * @version 1.0
 * @param integer
 * @return string
 */
function smarty_function_js($params, $smarty, $template = null) {
	if ( !isset($params['file']) ) {
		throw new mvcViewException('Missing required parameter (file)');
	}
	$oView = $smarty->getTemplateVars('oView');
	
	if ( $oView instanceof utilityOutputWrapper ) {
		$oView = $oView->getSeed();
	}
	
	if ( $oView instanceof mvcViewBase ) {
		$oView->addJavascriptResource(new mvcViewJavascript(md5($params['file']), mvcViewJavascript::TYPE_FILE, $params['file']));
	}
}