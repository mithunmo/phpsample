<?php
/**
 * Smarty plugin
 * @package mofilm
 * @subpackage mvc
 * @category smarty_plugin
 * @version $Rev: 11 $
 */

/**
 * Smarty {reportScheduleTypeSelect} plugin
 *
 * Type:     function<br>
 * Name:     reportScheduleTypeSelect<br>
 * Purpose:  Builds a report schedule type selector box
 * @author Dave Redfern
 * @param array Format:
 * Input:<br>
*            - name       (optional) - string default "select"
*            - selected   (optional) - string default not set
 * @param Smarty
 * @return string
 */
function smarty_function_reportScheduleTypeSelect($params, $smarty, $template = null) {
	require_once(SMARTY_PLUGINS_DIR . 'function.html_options.php');
	
	$objects = reportCentreReportScheduleType::listOfObjects(null, null);
	
	if ( false ) $oObject = new reportCentreReportScheduleType();
	foreach ( $objects as $oObject ) {
		$params['options'][$oObject->getScheduleTypeID()] = $oObject->getDescription();
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