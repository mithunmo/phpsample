<?php
/**
 * Smarty plugin
 * @package mofilm
 * @subpackage mvc
 * @category smarty_plugin
 * @version $Rev: 11 $
 */

/**
 * Smarty {languageSelect} plugin
 *
 * Type:     function<br>
 * Name:     languageSelect<br>
 * Purpose:  Builds a language selector box
 * @author Dave Redfern
 * @param array Format:
 * Input:<br>
*            - name       (optional) - string default "select"
*            - selected   (optional) - string default not set
 * @param Smarty
 * @return string
 */
function smarty_function_languageSelect($params, $smarty, $template = null) {
	require_once(SMARTY_PLUGINS_DIR . 'function.html_options.php');
	
	$objects = mofilmLanguage::listOfObjects();
	$title = (isset($params['title']) ? $params['title'] : 'Not selected');
	$useIso = (isset($params['useISO']) ? true : false);
	$params['options'] = array(0 => $title);
	
	if ( false ) $oObject = new mofilmLanguage();
	foreach ( $objects as $oObject ) {
		$params['options'][($useIso ? $oObject->getIso() : $oObject->getID())] = $oObject->getName();
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