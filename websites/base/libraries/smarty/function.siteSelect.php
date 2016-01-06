<?php
/**
 * Smarty plugin
 * @package mofilm
 * @subpackage mvc
 * @category smarty_plugin
 * @version $Rev: 233 $
 */

/**
 * Smarty {siteSelect} plugin
 *
 * Type:     function<br>
 * Name:     siteSelect<br>
 * Purpose:  Builds a website selector box
 * @author Dave Redfern
 * @param array Format:
 * Input:<br>
 *            - name       (optional) - string default "select"
 *            - selected   (optional) - string default not set
 * @param Smarty
 * @return string
 */
function smarty_function_siteSelect($params, $smarty, $template = null) {
	require_once(SMARTY_PLUGINS_DIR . 'function.html_options.php');
	
	$objects = mvcSiteTools::listOfObjects();
	$params['options'] = array('' => 'Not selected');
	
	/* @var mvcSiteTools $oObject */
	foreach ( $objects as $oObject ) {
		$params['options'][$oObject->getDomainName()] = $oObject->getDomainName().' ('.$oObject->getType().')';
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