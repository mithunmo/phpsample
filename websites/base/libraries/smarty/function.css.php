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
 * Adds the CSS to the internal view stack of CSS files
 *
 * Type:     function<br>
 * Name:     css<br>
 * Date:     Jan 14, 2011
 * Purpose:
 * Input:    Movie ID to create a link for
 * Example:  {css file='path/to/css/file.css'}
 * @author   Dave Redfern
 * @version 1.0
 * @param integer
 * @return string
 */
function smarty_function_css($params, $smarty, $template = null) {
	if ( !isset($params['file']) ) {
		throw new mvcViewException('Missing required parameter (file)');
	}
	$oView = $smarty->getTemplateVars('oView');
	
	if ( $oView instanceof utilityOutputWrapper ) {
		$oView = $oView->getSeed();
	}
	
	if ( $oView instanceof mvcViewBase ) {
		$oView->addCssResource(new mvcViewCss(md5($params['file']), mvcViewCss::TYPE_FILE, $params['file']));
	}
}