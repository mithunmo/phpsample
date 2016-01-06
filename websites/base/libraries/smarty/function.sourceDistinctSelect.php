<?php
/**
 * Smarty plugin
 * @package mofilm
 * @subpackage mvc
 * @category smarty_plugin
 * @version $Rev: 1 $
 */

/**
 * Smarty {sourceSelect} plugin
 *
 * Type:     function<br>
 * Name:     sourceDistinctSelect<br>
 * Purpose:  Builds a source selector box
 * @author Pavan Kumar P G
 * @param array Format:
 * Input:<br>
*            - name       (optional) - string default "select"
*            - selected   (optional) - string default not set
*            - all        (optional) - show all sources
 * @param Smarty
 * @return string
 */
function smarty_function_sourceDistinctSelect($params, $smarty, $template = null) {
	require_once(SMARTY_PLUGINS_DIR . 'function.html_options.php');

	$objects = mofilmSource::listOfDistinctSourceNameObjects();
	
	$title = (isset($params['title']) ? $params['title'] : 'Any Brand');
	$params['options'] = array(0 => $title);
	
	$oObject = new mofilmSource();
	foreach ( $objects as $oObject ) {
		$params['options'][$oObject->getName()] = $oObject->getName();
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