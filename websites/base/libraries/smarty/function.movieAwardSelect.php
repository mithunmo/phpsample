<?php
/**
 * Smarty plugin
 * @package mofilm
 * @subpackage mvc
 * @category smarty_plugin
 * @version $Rev: 11 $
 */

/**
 * Smarty {movieAwardSelect} plugin
 *
 * Type:     function<br>
 * Name:     movieAwardSelect<br>
 * Purpose:  Builds a movie award selector box
 * @author Dave Redfern
 * @param array Format:
 * Input:<br>
*            - name       (optional) - string default "select"
*            - selected   (optional) - string default not set
 * @param Smarty
 * @return string
 */
function smarty_function_movieAwardSelect($params, $smarty, $template = null) {
	require_once(SMARTY_PLUGINS_DIR . 'function.html_options.php');

	$title = (isset($params['title']) ? $params['title'] : 'All Videos');
	$params['options'] = array(0 => $title);

	foreach ( mofilmMovieAward::getTypes() as $type ) {
		//$params['options'][$type] = ($type == mofilmMovieAward::TYPE_WINNER ? 'Event Winner' : $type);
                if ( $type == mofilmMovieAward::TYPE_WINNER ) {
                    $params['options'][$type] = "Event Winner";
                } else if ( $type == mofilmMovieAward::TYPE_FINALIST) {
                    $params['options'][$type] = "Winners";
                } else if ( $type == mofilmMovieAward::TYPE_PRO_FINAL) {
                    $params['options'][$type] = "Pro Final";
                 } else if ( $type == mofilmMovieAward::TYPE_PRO_SHOWCASE) {
                    $params['options'][$type] = "Pro Showcase";
                } else {
                    $params['options'][$type] = $type;
                }
   	}
        $params['options']['BestOfClients'] = 'Best Of Client';    
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