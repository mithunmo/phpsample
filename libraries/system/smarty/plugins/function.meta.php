<?php
/**
 * Smarty plugin
 * @package scorpio
 * @subpackage mvc
 * @category smarty_plugin
 * @version $Rev: 638 $
 */


/**
 * Adds the meta to the internal view stack of meta attributes
 *
 * Meta function calls must be placed before any header is included or before
 * a template is extended. meta-redirects should be avoided. This is included
 * for completeness and as a compliment to css and js function calls.
 *
 * Type:     function<br>
 * Name:     meta<br>
 * Date:     Jan 14, 2011
 * Purpose:
 * Input:    Adds the header meta type to the view
 * Example:  {meta type='http-equiv|name' name='' content=''}
 * @author   Dave Redfern
 * @version 1.0
 * @param string
 * @return void
 */
function smarty_function_meta($params, $smarty) {
	if ( !isset($params['name']) ) {
		throw new mvcViewException('Missing required parameter (name)');
	}
	if ( !isset($params['content']) ) {
		throw new mvcViewException('Missing required parameter (content)');
	}
	$oView = $smarty->getTemplateVars('oView');
	
	if ( $oView instanceof utilityOutputWrapper ) {
		$oView = $oView->getSeed();
	}
	
	if ( $oView instanceof mvcViewBase ) {
		$oView->addMetaResource(
			new mvcViewMeta(
				md5($params['name']),
				($params['type'] == 'http-equiv' ? mvcViewMeta::META_TYPE_HTTP_EQUIV : mvcViewMeta::META_TYPE_NAME),
				$params['name'],
				$params['content']
			)
		);
	}
}