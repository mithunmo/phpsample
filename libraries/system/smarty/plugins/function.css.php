<?php
/**
 * Smarty plugin
 * @package scorpio
 * @subpackage mvc
 * @category smarty_plugin
 * @version $Rev: 638 $
 */


/**
 * Adds the CSS to the internal view stack of CSS files
 *
 * Type:     function<br>
 * Name:     css<br>
 * Date:     Jan 14, 2011
 * Purpose:
 * Input:    Adds the specified CSS file to the current view
 * Example:  {css file='path/to/css/file.css'}
 * @author   Dave Redfern
 * @version 1.0
 * @param string
 * @return void
 */
function smarty_function_css($params, $smarty) {
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