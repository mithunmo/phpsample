<?php
/**
 * Smarty plugin
 * @package mofilm
 * @subpackage mvc
 * @category smarty_plugin
 * @version $Rev: 11 $
 */

/**
 * Smarty {sourceTrackSelect} plugin
 *
 * Type:     function<br>
 * Name:     sourceTrackSelect<br>
 * Purpose:  Builds a source selector box
 * @author Dave Redfern
 * @param array Format:
 * Input:<br>
*            - name       (optional) - string default "select"
*            - selected   (optional) - id of the selected track, default not set
*            - sourceID    (required) - current sourceID or source object
 * @param Smarty
 * @return string
 */
function smarty_function_sourceTrackSelect($params, $smarty, $template = null) {
	require_once(SMARTY_PLUGINS_DIR . 'function.html_options.php');
	
	if ( isset($params['sourceID']) && is_numeric($params['sourceID']) && $params['sourceID'] > 0 ) {
		$source =  mofilmSource::getInstance($params['sourceID']);
	} elseif ( isset($params['sourceID']) && is_object($params['sourceID']) ) {
		if (
			$params['sourceID'] instanceof utilityOutputWrapper ||
			$params['sourceID'] instanceof utilityOutputWrapperArray ||
			$params['sourceID'] instanceof utilityOutputWrapperIterator
		) {
			$source = $params['sourceID']->getSeed();
		} else {
			$source = $params['sourceID'];
		}
		unset($params['sourceID']);
	} else {
		throw new mvcViewException('sourceTrackSelect requires either the sourceID or a mofilmSource object');
	}
	
	if ( !$source instanceof mofilmSource ) {
		throw new mvcViewException('Missing mofilmSource object, please specify a valid sourceID or source object');
	}
	
	$title = (isset($params['title']) ? $params['title'] : 'No track / own selection');
	$params['options'] = array(0 => $title);
	
	if ( false ) $oObject = new mofilmTrack();
	foreach ( $source->getTrackSet() as $oObject ) {
		if ( $oObject->getTitle() && $oObject->getArtist() ) {
			$params['options'][$oObject->getID()] = $oObject->getTile().' by '.$oObject->getArtist();
		} else {
			$params['options'][$oObject->getID()] = $oObject->getDescription();
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