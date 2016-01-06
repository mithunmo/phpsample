<?php
/**
 * Smarty plugin
 * @package mofilm
 * @subpackage mvc
 * @category smarty_plugin
 * @version $Rev: 11 $
 */

/**
 * Smarty {termsSelect} plugin
 *
 * Type:     function<br>
 * Name:     termsSelect<br>
 * Purpose:  Builds a terms selector box
 * @author Dave Redfern
 * @param array Format:
 * Input:<br>
*            - name       (optional) - string default "select"
*            - selected   (optional) - string default not set
 * @param Smarty
 * @return string
 */
function smarty_function_termsSelect($params, $smarty, $template = null) {
	require_once(SMARTY_PLUGINS_DIR . 'function.html_options.php');
	
	$objects = mofilmTerms::listOfObjects();
	$title = (isset($params['title']) ? $params['title'] : 'MOFILM Standard Terms');
	$params['options'] = array(0 => $title);
	
	if ( false ) $oObject = new mofilmTerms();
	foreach ( $objects as $oObject ) {
		$params['options'][$oObject->getID()] = $oObject->getDescription().' ('.$oObject->getVersion().')';
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