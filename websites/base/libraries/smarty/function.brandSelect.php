<?php
/**
 * Smarty plugin
 * @package mofilm
 * @subpackage mvc
 * @category smarty_plugin
 * @version $Rev: 11 $
 */

/**
 * Smarty {sourceSelect} plugin
 *
 * Type:     function<br>
 * Name:     sourceSelect<br>
 * Purpose:  Builds a source selector box
 * @author Dave Redfern
 * @param array Format:
 * Input:<br>
*            - name       (optional) - string default "select"
*            - selected   (optional) - string default not set
*            - eventID    (optional) - selected eventID
*            - user       (optional) - mofilmUser object
*            - all        (optional) - show all sources
 * @param Smarty
 * @return string
 */
function smarty_function_brandSelect($params, $smarty, $template = null) {
	require_once(SMARTY_PLUGINS_DIR . 'function.html_options.php');
	//$corporateID = isset($params['corporateID']) && is_numeric($params['corporateID']) && $params['corporateID'] > 0 ? $params['corporateID'] : null;
	$oUser = false;
	if ( array_key_exists('user', $params) ) {
		$oUser = $params['user'];
		if ( $oUser instanceof utilityOutputWrapper ) {
			$oUser = $oUser->getSeed();
		}
		unset($params['user']);
	}
	if ( array_key_exists('all', $params) ) {
		$allSources = true;
		unset($params['all']);
	} else {
		$allSources = false;
	}
	if ( $oUser && $oUser instanceof utilityOutputWrapper ) {
		$oUser = $oUser->getSeed();
	}

	if ( $oUser instanceof mofilmUser && $oUser->getClientID() != mofilmClient::MOFILM ) {
		$objects = mofilmBrand::listOfObjects(null, null, $params, $oUser->getSourceSet()->getObjectIDs(), !$allSources);
	} else {
		$objects = mofilmBrand::listOfObjects(null, null, $params, array(), !$allSources,7);
	}
	
	$title = (isset($params['title']) ? $params['title'] : 'Any Brands');
	$params['options'] = array("" => $title);
	
	if ( false ) $oObject = new mofilmSource();
	foreach ( $objects as $oObject ) {
		if ( $eventID !== null ) {
			$params['options'][$oObject->getID().'-'.$oObject->getName()] = $oObject->getName();
		} else {
			$params['options'][$oObject->getID().'-'.$oObject->getName()] = $oObject->getName();
		}
                 if ( isset($params['selected']) && $params['selected'] == $oObject->getID() ) {
                    $params['selected'] = $oObject->getID().'-'.$oObject->getName();
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