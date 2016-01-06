<?php
/**
 * Smarty plugin
 * @package scorpio
 * @subpackage mvc
 * @category smarty_plugin
 * @version $Rev: 638 $
 */

/**
 * Smarty {highlightSource} plugin
 *
 * Type:     function<br>
 * Name:     highlightSource<br>
 * Purpose:  Highlights the source code from a PHP file, either the whole thing
 *           or just a set of lines.
 * @author Dave Redfern
 * @param array Format:
 * <pre>
 * array('file' => full path to the file,
 *       'start' => line number to start from,
 *       'end' => line number to finish on
 * </pre>
 * @param Smarty
 * @return string
 */
function smarty_function_highlightSource($params, $smarty) {
	$display = '';
	
	if ( !system::getConfig()->isProduction() ) {
		if ( !isset($params['file']) || strlen($params['file']) < 1 ) {
			throw new mvcViewException('highlightSource missing required parameter: file');
		}
		if ( !is_readable($params['file']) ) {
			throw new mvcViewException('highlightSource cannot read the specified file: '.$params['file']);
		}
		
		$start = isset($params['start']) && is_numeric($params['start']) && $params['start'] >= 1 ? $params['start'] : 1;
		$end = isset($params['end']) && is_numeric($params['end']) && $params['end'] >= 1 ? $params['end'] : false;
		
		if ( $start && $end ) {
			$contents = explode("\n", file_get_contents($params['file']));
			$max = count($contents);
			$lines = array_slice($contents, $start-1, ($end && $end <= $max ? $end : $max)-$start+1);
			$display = highlight_string("<?php\n".implode("\n", $lines)."\n ?>", true);
		} else {
			$display = highlight_file($params['file'], true);
		}
	} else {
		systemLog::notice('Source code highlighting is disabled when in production');
	}
	return $display;
}