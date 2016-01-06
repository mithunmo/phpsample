<?php
/**
 * Smarty plugin
 * @package mofilm
 * @subpackage mvc
 * @category smarty_plugin
 * @version $Rev: 11 $
 */

/**
 * Smarty {movieStatusSelect} plugin
 *
 * Type:     function<br>
 * Name:     movieStatusSelect<br>
 * Purpose:  Builds a movie status selector box
 * @author Dave Redfern
 * @param array Format:
 * Input:<br>
*            - name       (optional) - string default "select"
*            - selected   (optional) - string default not set
 * @param Smarty
 * @return string
 */
function smarty_function_movieStatusSelect($params, $smarty, $template = null) {
	require_once(SMARTY_PLUGINS_DIR . 'function.html_options.php');
	
	$title = (isset($params['title']) ? $params['title'] : 'Any Status');
	$params['options'] = array(0 => $title);
	
	if ( array_key_exists('showAll', $params) ) {
		unset($params['showAll']);
		foreach ( mofilmMovieManager::getAvailableMovieStatuses() as $status ) {
			$params['options'][$status] = $status;
		}
	} else {
		$params['options'][mofilmMovie::STATUS_APPROVED] = mofilmMovie::STATUS_APPROVED;
		$params['options'][mofilmMovie::STATUS_PENDING] = mofilmMovie::STATUS_PENDING;
		$params['options'][mofilmMovie::STATUS_REJECTED] = mofilmMovie::STATUS_REJECTED;
	}

	if ( isset($params['selected']) ) {
		if (
			$params['selected'] instanceof utilityOutputWrapper ||
			$params['selected'] instanceof utilityOutputWrapperArray ||
			$params['selected'] instanceof utilityOutputWrapperIterator
		) {
			$params['selected'] = $params['selected']->getSeed();
		}
	}
	
	return smarty_function_html_options($params, $smarty, $template);
}