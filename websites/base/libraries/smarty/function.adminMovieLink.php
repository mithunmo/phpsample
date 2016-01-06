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
 * Returns the URL for editing the specified movie based on user permissions
 *
 * Type:     function<br>
 * Name:     adminMovieLink<br>
 * Date:     Sep 6, 2010
 * Purpose:
 * Input:    Movie ID to create a link for
 * Example:  {adminMovieLink movieID=$movieID}
 * @author   Dave Redfern
 * @version 1.0
 * @param integer
 * @return string
 */
function smarty_function_adminMovieLink($params, $smarty, $template = null) {
	$editUri = $smarty->getTemplateVars('editURI');
	$watchUri = $smarty->getTemplateVars('watchURI');
	if ( !$editUri ) {
		$editUri = '/videos/edit';
	}
	if ( !$watchUri ) {
		$watchUri = '/videos/watch';
	}
	$value = $watchUri;
	
	if ( !array_key_exists('movieID', $params) ) {
		throw new mvcViewException('Missing required parameter (movieID)');
	}
	
	$oUser = $smarty->getTemplateVars('oUser');
	if ( $oUser->isAuthorised('videosController.edit') ) {
		$value = $editUri;
	}
	
	return $value.'/'.$params['movieID'];
}