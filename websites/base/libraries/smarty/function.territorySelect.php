<?php
/**
 * Smarty plugin
 * @package mofilm
 * @subpackage mvc
 * @category smarty_plugin
 * @version $Rev: 11 $
 */

/**
 * Smarty {territorySelect} plugin
 *
 * Type:     function<br>
 * Name:     territorySelect<br>
 * Purpose:  Builds a territory selector box
 * @author Dave Redfern
 * @param array Format:
 * Input:<br>
*            - name       (optional) - string default "select"
*            - selected   (optional) - string default not set
 * @param Smarty
 * @return string
 */
function smarty_function_territorySelect($params, $smarty, $template = null) {
	require_once(SMARTY_PLUGINS_DIR . 'function.html_options.php');
	
	$objects = mofilmTerritory::listOfObjects();
	$title = (isset($params['title']) ? $params['title'] : 'Not selected');
	$params['options'] = array(0 => $title);

	foreach ( $objects as $oObject ) {
		$params['options'][$oObject->getID()] = $oObject->getCountry();
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