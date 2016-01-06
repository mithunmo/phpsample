<?php
/**
 * Smarty plugin
 * @package mofilm
 * @subpackage mvc
 * @category smarty_plugin
 * @version $Rev: 11 $
 */

/**
 * Smarty {clientSelect} plugin
 *
 * Type:     function<br>
 * Name:     clientSelect<br>
 * Purpose:  Builds a client selector box
 * @author Dave Redfern
 * @param array Format:
 * Input:<br>
*            - name       (optional) - string default "select"
*            - selected   (optional) - string default not set
 * @param Smarty
 * @return string
 */
function smarty_function_clientSelect($params, $smarty, $template = null) {
	require_once(SMARTY_PLUGINS_DIR . 'function.html_options.php');
	
	$objects = mofilmClient::listOfObjects();
	$params['options'] = array(0 => 'No Client');
	
	if ( false ) $oObject = new mofilmClient();
	foreach ( $objects as $oObject ) {
		$params['options'][$oObject->getID()] = $oObject->getCompanyName();
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