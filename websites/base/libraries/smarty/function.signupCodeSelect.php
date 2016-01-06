<?php
/**
 * Smarty plugin
 * @package mofilm
 * @subpackage mvc
 * @category smarty_plugin
 * @version $Rev: 11 $
 */

/**
 * Smarty {signupCodeSelect} plugin
 *
 * Type:     function<br>
 * Name:     signupCodeSelect<br>
 * Purpose:  Builds a signup code selector box
 * @author Pavan Kumar P G
 * @param array Format:
 * Input:<br>
*            - name       (optional) - string default "select"
*            - selected   (optional) - string default not set
 * @param Smarty
 * @return string
 */
function smarty_function_signupCodeSelect($params, $smarty, $template = null) {
	require_once(SMARTY_PLUGINS_DIR . 'function.html_options.php');
	
	if ( isset ($params['All']) ) {
		$objects = mofilmUserSignupCode::listOfObjects();
	} else {
		$objects = mofilmUserSignupCode::listOfObjects(NULL, 30, TRUE);
	}
	
	$title = (isset($params['title']) ? $params['title'] : 'Not selected');
	$params['options'] = array(0 => $title);

	foreach ( $objects as $oObject ) {
		$params['options'][$oObject->getID()] = $oObject->getDescription();
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