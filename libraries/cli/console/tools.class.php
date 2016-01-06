<?php
/**
 * cliConsoleTools Class
 * 
 * Stored in tools.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliConsoleTools
 * @version $Rev: 707 $
 */


/**
 * cliConsoleTools
 *
 * Contains a variety of tools for working on the CLI these include outputting a
 * pre-defined separator as well as table tools etc.
 * 
 * <code>
 * // output an array like a table on cli
 * $array = array(
 *     // first row
 *     array(
 *         'col1' => 'val1',
 *         'col2' => 'val2',
 *     ),
 *     // second row
 *     array(
 *         'col1' => 'val3',
 *         'col2' => 'val4',
 *     ),
 *     // ... etc
 * );
 * 
 * echo cliConsoleTools::cliDataPrint($array, null, 72);
 * </code>
 * 
 * @package scorpio
 * @subpackage cli
 * @category cliConsoleTools
 */
class cliConsoleTools {
	
	/**
	 * This is a static class
	 */
	private function __construct() {}
	
	/**
	 * Writes a text string to the CLI, interpreting any colour codes via Console_Color.
	 * 
     * Converts colorcodes in the format %y (for yellow) into ansi-control
     * codes. The conversion table is: ('bold' meaning 'light' on some
     * terminals). It's almost the same conversion table irssi uses.
     * <pre> 
     *                  text      text            background
     *      ------------------------------------------------
     *      %k %K %0    black     dark grey       black
     *      %r %R %1    red       bold red        red
     *      %g %G %2    green     bold green      green
     *      %y %Y %3    yellow    bold yellow     yellow
     *      %b %B %4    blue      bold blue       blue
     *      %m %M %5    magenta   bold magenta    magenta
     *      %p %P       magenta (think: purple)
     *      %c %C %6    cyan      bold cyan       cyan
     *      %w %W %7    white     bold white      white
     *
     *      %F     Blinking, Flashing
     *      %U     Underline
     *      %8     Reverse
     *      %_,%9  Bold
     *
     *      %n     Resets the color
     *      %%     A single %
     * </pre>
     * First param is the string to convert, second is an optional flag if
     * colors should be used. It defaults to true, if set to false, the
     * colorcodes will just be removed (And %% will be transformed into %)
     *
     * @param string $inMessage
     * @param boolean $inColour
     * @access public
     * @static
     */
	public static function message($inMessage, $inColour = true) {
		echo Console_Color::convert($inMessage, $inColour);
	}
	
	/**
	 * Returns the separator at $inWidth
	 * 
	 * If width not specified uses the cliConstant
	 * CONSOLE_WIDTH. $inSeparator must be 1 character long, longer values will be
	 * reduced to the first character.
	 *
	 * @param string $inSeparator
	 * @param integer $inWidth
	 * @return string
	 * @static
	 */
	static function drawSeparator($inSeparator = '-', $inWidth = null) {
		if ( strlen($inSeparator) > 1 ) {
			$inSeparator = substr($inSeparator, 0, 1);
		}
		if ( $inWidth === null || !is_numeric($inWidth) ) {
			$inWidth = cliConstants::CONSOLE_WIDTH;
		}
		return str_repeat($inSeparator, $inWidth);
	}

	/**
	 * Outputs an associative array of data in a formatted manner on the CLI
	 * 
	 * $inData is expected to be an associative array with the following format:
	 * <code>
	 * $array = array(
	 *     // first row
	 *     array(
	 *         'col1' => 'val1',
	 *         'col2' => 'val2',
	 *     ),
	 *     // second row
	 *     array(
	 *         'col1' => 'val3',
	 *         'col2' => 'val4',
	 *     ),
	 *     // ... etc
	 * );
	 * </code>
	 * 
	 * @param array $inData
	 * @param array $inColumns
	 * @param integer $inWidth
	 * @return string
	 * @static 
	 */
	static function cliDataPrint($inData, $inColumns = null, $inWidth = 80) {
		if ($inColumns == null || true) {
			$inColumns = array_combine(array_keys($inData[0]),array_keys($inData[0]));
			foreach ($inColumns as $key => $value) {
				$inColumns[$key] = ucwords(utilityStringFunction::convertCapitalizedString($value));
			}
		}
		$str = '';
		$colWidth = cliConsoleTools::getWidths($inData, $inColumns, $inWidth);
		$twidth = array_sum($colWidth)+count($colWidth)+1;
		$str .= str_pad("",$twidth,'-')."\n";
		$str .= "|";
		foreach ($inColumns as $key => $value) {
			$str .= str_pad(substr($value,0,min($colWidth[$key],strlen($value))),$colWidth[$key]).'|';
		}
		$str .= "\n";
		$str .= str_pad("",$twidth,'-')."\n";
		foreach ($inData as $row) {
			$str .= "|";
			foreach ($inColumns as $key => $value) {
				$str .= str_pad($row[$key],$colWidth[$key]).'|';
			}
			$str .= "\n";
		}
		$str .= str_pad("",$twidth,'-')."\n";
		return $str;
	}
	
	/**
	 * Returns an array containing the calculated widths for the cliDataPrint method
	 *
	 * @param array $inData
	 * @param array $inColums
	 * @param integer $inMaxWidth
	 * @param integer $inMaxTextLength
	 * @return array
	 * @static 
	 */
	static function getWidths($inData, $inColums, $inMaxWidth, $inMaxTextLength = 100) {
		$width = array();
		foreach ($inData as $row) {
			foreach ($inColums as $colKey => $colCaption) {
				$value = $row[$colKey];
				if (isset($width[$colKey])) {
					$width[$colKey] = min(max($width[$colKey],strlen($value)),$inMaxTextLength);
				}
				else {
					$width[$colKey] = min(max(strlen($colCaption),strlen($value)),$inMaxTextLength);
				}
			}
	    }
	    $total = 0;
	    foreach ($width as $key => $value) {
	    	$total += $value;
	    }
	    $pWidth = array();
	    foreach ($width as $key => $value) {
	    	$pWidth[$key] = round($value/$total * $inMaxWidth);
	    }
		return $pWidth;
	}
}