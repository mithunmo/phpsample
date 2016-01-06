<?php
/**
 * Smarty plugin
 * @package mofilm
 * @subpackage mvc
 * @category smarty_plugin
 * @version $Rev: 11 $
 */

/**
 * Smarty {eventSelect} plugin
 *
 * Type:     function<br>
 * Name:     eventSelect<br>
 * Purpose:  Builds an event selector box
 * @author Dave Redfern
 * @param array Format:
 * Input:<br>
*            - name       (optional) - string default "select"
*            - selected   (optional) - string default not set
*            - user       (optional) - mofilmUser object
*            - exclude    (optional) - use users event exclude filter
 * @param Smarty
 * @return string
 */
function smarty_function_eventSelect($params, $smarty, $template = null) {
	require_once(SMARTY_PLUGINS_DIR . 'function.html_options.php');

	$order = $oUser = null;
	if ( array_key_exists('user', $params) ) {
		$oUser = $params['user'];
		if ( $oUser instanceof utilityOutputWrapper ) {
			$oUser = $oUser->getSeed();
		}
		unset($params['user']);
	}
	if ( array_key_exists('order', $params) ) {
		$order = $params['order'];
		unset($params['order']);
		
		switch ( $order ) {
			case 'enddate':
				$order = mofilmEvent::ORDERBY_ENDDATE;
			break;
			
			case 'startdate':
				$order = mofilmEvent::ORDERBY_STARTDATE;
			break;
		}
	}
	if ( $oUser && $oUser instanceof utilityOutputWrapper ) {
		$oUser = $oUser->getSeed();
	}
	if ( $oUser && array_key_exists('exclude', $params) ) {
		$exclude = $oUser->getEventFilter()->toArray();
	} else {
		$exclude = array();
	}
	if ( $oUser instanceof mofilmUser && $oUser->getClientID() != mofilmClient::MOFILM ) {
		$objects = mofilmEvent::listOfObjects(null, null, null, $order, $oUser->getSourceSet()->getEventIDs(), $exclude,$params);
	} else {
		$objects = mofilmEvent::listOfObjects(null, null, null, $order, array(), $exclude,$params);
	}

	$title = (isset($params['title']) ? $params['title'] : 'Any Project');
	$params['options'] = array(0 => $title);
	
	if ( false ) $oObject = new mofilmEvent();
	foreach ( $objects as $oObject ) {
		$params['options'][$oObject->getID()] = $oObject->getName();
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