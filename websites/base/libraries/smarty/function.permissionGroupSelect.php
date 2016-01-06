<?php
/**
 * Smarty plugin
 * @package mofilm
 * @subpackage mvc
 * @category smarty_plugin
 * @version $Rev: 11 $
 */

/**
 * Smarty {permissionGroupSelect} plugin
 *
 * Type:     function<br>
 * Name:     permissionGroupSelect<br>
 * Purpose:  Builds a permission group selector box
 * @author Dave Redfern
 * @param array Format:
 * Input:<br>
*            - name       (optional) - string default "select"
*            - selected   (optional) - string default not set
 * @param Smarty $smarty
 * @return string
 */
function smarty_function_permissionGroupSelect($params, $smarty, $template = null) {
	require_once(SMARTY_PLUGINS_DIR . 'function.html_options.php');
	
	$oUser = $smarty->getVariable('oUser')->value;
	if ( $oUser instanceof utilityOutputWrapper ) {
		$oUser = $oUser->getSeed();
	}
	
	$objects = mofilmPermissionGroup::listOfObjects();
	if ( false ) $oObject = new mofilmPermissionGroup(); // for autocomplete
	
	if ( $oUser instanceof mofilmUser ) {
		if ( $oUser->getPermissions()->isRoot() ) {
			$params['options'] = array(-1 => 'No Group', 0 => 'Root');
		}
		
		if ( $oUser->getClientID() == mofilmClient::MOFILM ) {
			foreach ( $objects as $oObject ) {
				$params['options'][$oObject->getID()] = $oObject->getDescription();
			}
		} else {
			foreach ( $objects as $oObject ) {
				if ( in_array($oObject->getID(), mofilmPermissionGroup::$publicGroups) ) {
					$params['options'][$oObject->getID()] = $oObject->getDescription();
				}
			}
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