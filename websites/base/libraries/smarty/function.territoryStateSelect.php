<?php
/**
 * Smarty plugin
 * @package mofilm
 * @subpackage mvc
 * @category smarty_plugin
 * @version $Rev: 11 $
 */

/**
 * Smarty {territoryStateSelect} plugin
 *
 * Type:     function<br>
 * Name:     territoryStateSelect<br>
 * Purpose:  Builds a territory selector box
 * @author Dave Redfern
 * @param array Format:
 * Input:<br>
*            - name       (optional) - string default "select"
*            - selected   (optional) - string default not set
*            - nameaskey  (optional) - use state name as the key
 * @param Smarty
 * @return string
 */
function smarty_function_territoryStateSelect($params, $smarty, $template = null) {
	require_once(SMARTY_PLUGINS_DIR . 'function.html_options.php');
	
	$objects = mofilmTerritoryState::listOfObjects();
	$title = (isset($params['title']) ? $params['title'] : 'Not selected');
	$params['options'] = array(0 => $title);
	
	if ( false ) $oObject = new mofilmTerritoryState();
	foreach ( $objects as $oObject ) {
		if ( isset($params['nameaskey']) ) {
			$params['options'][$oObject->getTerritory()->getCountry()][$oObject->getDescription()] = $oObject->getDescription();
		} else {
			$params['options'][$oObject->getTerritory()->getCountry()][$oObject->getID()] = $oObject->getDescription();
		}
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