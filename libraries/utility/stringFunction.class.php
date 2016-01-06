<?php
/**
 * utilityStringFunction Class
 * 
 * Stored in stringFunction.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityStringFunction
 * @version $Rev: 706 $
 */


/**
 * utilityStringFunction Class
 * 
 * utilityStringFunction is a static class that encapsulates many string
 * manipulation functions. It is used extensively throughout Scorpio to
 * perform tasks such as normalising strings for URIs, keywords, converting
 * to XML safe, handling UTF8 characters etc. Each method contains
 * information about what it does and any notes or problems that may arise.
 * 
 * @package scorpio
 * @subpackage utility
 * @category utilityStringFunction
 */
class utilityStringFunction {
	
	/**
	 * Array of character entities used for filtering
	 * 
	 * Note: it is possible for this array to become corrupt if the
	 * file encoding changes. Always edit this file in a UTF-8 editor.
	 *
	 * @todo: DR: find a better to store this data (utf-8 hex entities?)
	 * @var array
	 * @static 
	 */
	static $_XmlChars = array(
		"<" => "&lt;", ">" => "&gt;", "\"" => "&quot;", "&" => "&#38;", "'" => "&#39;", 
		"&#352;" => "&#138;", "ÃJ" => "&#138;", "&#353;" => "&#154;", "ÃZ" => "&#154;",
		"&#381;" => "&#142;", "ÃN" => "&#142;", "&#382;" => "&#158;", "Ã^" => "&#158;",
		"&#376;" => "&#159;", "Ã_" => "&#159;", "ÿ" => "&#255;", "Ã¿" => "&#255;",
		"À" => "&#192;", "Ã&#8364;" => "&#192;", "à" => "&#224;", "Ã " => "&#224;",
		"Á" => "&#193;", "Ã?" => "&#193;", "á" => "&#225;", "Ã¡" => "&#225;", 
		"Â" => "&#194;", "Ã&#8218;" => "&#194;", "â" => "&#226;", "Ã¢" => "&#226;", 
		"Ã" => "&#195;", "Ã&#402;" => "&#195;", "ã" => "&#227;", "Ã£" => "&#227;", 
		"Ä" => "&#196;", "Ã&#8222;" => "&#196;", "ä" => "&#228;", "Ã€" => "&#228;", 
		"Å" => "&#197;", "Ã&#8230;" => "&#197;", "å" => "&#229;", "Ã¥" => "&#229;", 
		"Æ" => "&#198;", "Ã&#8224;" => "&#198;", "æ" => "&#230;", "ÃŠ" => "&#230;",
		"Ç" => "&#199;", "Ã&#8225;" => "&#199;", "ç" => "&#231;", "Ã§" => "&#231;",
		"È" => "&#200;", "Ã&#710;" => "&#200;", "è" => "&#232;", "Ãš" => "&#232;",
		"É" => "&#201;", "Ã&#8240;" => "&#201;", "é" => "&#233;", "Ã©" => "&#233;",
		"Ê" => "&#202;", "Ã&#352;" => "&#202;", "ê" => "&#234;", "Ãª" => "&#234;",
		"Ë" => "&#203;", "Ã&#8249;" => "&#203;", "ë" => "&#235;", "Ã«" => "&#235;",
		"Ì" => "&#204;", "Ã&#338;" => "&#204;", "ì" => "&#236;", "Ã¬" => "&#236;",
		"Í" => "&#205;", "Ã?" => "&#205;", "í" => "&#237;", "Ã­" => "&#237;",
		"Î" => "&#206;", "Ã&#381;" => "&#206;", "î" => "&#238;", "Ã®" => "&#238;",
		"Ï" => "&#207;", "Ã?" => "&#207;", "ï" => "&#239;", "Ã¯" => "&#239;",
		"Ð" => "&#208;", "Ã?" => "&#208;", "ð" => "&#240;", "Ã°" => "&#240;",
		"Ñ" => "&#209;", "Ã&#8216;" => "&#209;", "ñ" => "&#241;", "Ã±" => "&#241;",
		"Ò" => "&#210;", "Ã&#8217;" => "&#210;", "ò" => "&#242;", "Ã²" => "&#242;",
		"Ó" => "&#211;", "Ã&#8220;" => "&#211;", "ó" => "&#243;", "Ã³" => "&#243;",
		"Ô" => "&#212;", "Ã&#8221;" => "&#212;", "ô" => "&#244;", "ÃŽ" => "&#244;",
		"Õ" => "&#213;", "Ã&#8226;" => "&#213;", "õ" => "&#245;", "Ãµ" => "&#245;",
		"Ö" => "&#214;", "Ã&#8211;" => "&#214;", "ö" => "&#246;", "Ã¶" => "&#246;", 
		"Ø" => "&#216;", "Ã&#732;" => "&#216;", "ø" => "&#248;", "Ãž" => "&#248;", 
		"Ù" => "&#217;", "Ã&#8482;" => "&#217;", "ù" => "&#249;", "Ã¹" => "&#249;",
		"Ú" => "&#218;", "Ã&#353;" => "&#218;", "ú" => "&#250;", "Ãº" => "&#250;",
		"Û" => "&#219;", "Ã&#8250;" => "&#219;", "û" => "&#251;", "Ã»" => "&#251;",
		"Ü" => "&#220;", "Ã&#339;" => "&#220;", "ü" => "&#252;", "ÃŒ" => "&#252;",
		"Ý" => "&#221;", "Ã?" => "&#221;", "ý" => "&#253;", "Ãœ" => "&#253;",
		"Þ" => "&#222;", "Ã&#382;" => "&#222;", "þ" => "&#254;", "ÃŸ" => "&#254;",
		"ß" => "&#223;", "Ã&#376;" => "&#223;", "£" => "&#163;" , "" => "&#8364;"
	);
	
	/**
	 * Cleans a string like ,123,,123 and turn it into 123,123
	 *
	 * @param string $inString
	 * @param string $inParam
	 * @return string
	 * @access public
	 * @static 
	 */
	public static function cleanSeperatedString($inString, $inParam = ',') {
		$inString = trim(preg_replace("/[".$inParam."]{2,}/", ',', $inString));
		return trim(preg_replace("/^".$inParam."|".$inParam."$/", '', $inString));
	}
	
	/**
	 * Returns a string with $inRemoveValue removed from $inString, optionally
	 * separated by $inSeperator (default ,)
	 *
	 * @param string $inString
	 * @param string $inRemoveValue
	 * @param string $inSeperator
	 * @return string
	 * @static 
	 */
	public static function removeStringItem($inString, $inRemoveValue, $inSeperator = ',') {
		$array = explode($inSeperator, $inString);
		$nString = '';
		foreach ($array as $value) {
			if ( !preg_match("/$inRemoveValue/", $value) ) {
				$nString .= ($nString?',':'').$value;
			}
		}
		return $nString;
	}
	
	/**
	 * Converts any string containing a number to a string that contains only words
	 *  
	 * e.g. 2u from disk 3 => twou from disk three
	 *
	 * @param string $inString
	 * @return string
	 * @static 
	 */
	public static function numberStringToString($inString) {
		$array = array('zero','one','two','three','four','five','six','seven','eight','nine');
		return strtr($inString,$array); 
	}
	
	/**
	 * Returns an XML string as a utf8 string
	 *
	 * @param string $string
	 * @return string
	 * @static 
	 */
	static function xmlStringToUtf8($string) {
		if ( $string && strlen($string) > 0 ) {
			$data = array_merge(array_flip(self::$_XmlChars),array("&#34;" => '"'));
			$string = strtr($string,$data);
		}
		return $string;
	}
	
	/**
	 * Returns a safe XML string
	 *
	 * @param string $string
	 * @return string
	 * @static 
	 */
	static function xmlString($string) {
		if ( $string && strlen($string) > 0 ) {
			$string = strtr($string, self::$_XmlChars);
		}
		return $string;
	}
	
	/**
	 * Removes accented characters
	 *
	 * @param string $inString
	 * @return string
	 * @static 
	 */
	public static function removeAccents($inString) {
		return strtr($inString,
		 "\xe1\xc1\xe0\xc0\xe2\xc2\xe4\xc4\xe3\xc3\xe5\xc5".
		 "\xaa\xe7\xc7\xe9\xc9\xe8\xc8\xea\xca\xeb\xcb\xed".
		 "\xcd\xec\xcc\xee\xce\xef\xcf\xf1\xd1\xf3\xd3\xf2".
		 "\xd2\xf4\xd4\xf6\xd6\xf5\xd5\x8\xd8\xba\xf0\xfa".
		 "\xda\xf9\xd9\xfb\xdb\xfc\xdc\xfd\xdd\xff\xe6\xc6\xdf",
		 "aAaAaAaAaAaAacCeEeEeEeEiIiIiIiInNoOoOoOoOoOoOoouUuUuUuUyYyaAs"); 
	}
	
	/**
	 * Returns a string containing only A-Z, 0-9 and a space suitable for use in a search index
	 *
	 * @param string $inString
	 * @return string
	 * @static 
	 */
	public static function normaliseStringCharacters($inString) {
		$string = utilityStringFunction::removeAccents($inString);
		$string	= trim(preg_replace("/[^ 0-9a-zA-Z]/", '', $string));
		return preg_replace("/[ ]{2,}/", ' ', $string);
	}
	
	/**
	 * Creates a uri safe text string to be used for page names
	 *
	 * @param string $inString
	 * @param string $inSeparator
	 * @return string
	 * @static 
	 */
	public static function normaliseStringCharactersForUri($inString, $inSeparator) {
		$string = utilityStringFunction::removeAccents($inString);
		$separator = $inSeparator;
		if ( $inSeparator == '\\' || $inSeparator == '/' || $inSeparator == '-' ) {
			$separator = '\-';
		}
		$regEx = "/[^ 0-9a-zA-Z$separator]/";
		$string	= trim(preg_replace($regEx, ' ', $string));
		$string = preg_replace("/[ ]{2,}/", ' ', $string);
		return str_replace(' ', $inSeparator, $string);
	}
	
	/**
	 * Returns a string containing only A-Z, 0-9 (as words) and a space suitable for use in a search index
	 *
	 * @param string $inString
	 * @return string
	 * @static 
	 */
	public static function normaliseStringForKeywords($inString) {
		$string = utilityStringFunction::removeAccents($inString);
		$string = utilityStringFunction::numberStringToString($string);
		$string	= trim(preg_replace("/[^ 0-9a-zA-Z]/", '', $string));
		return preg_replace("/[ ]{2,}/", ' ', $string);
	}
	
	/**
	 * Returns a string containing unique phrases that have been prepared to be used as keywords
	 *
	 * @param string $inString
	 * @return string
	 * @static 
	 */
	public static function normaliseStringForUniqueKeywords($inString) {
		$string = explode(' ', self::normaliseStringForKeywords($inString));
		$string = array_unique($string);
		sort($string);
		return implode(' ', $string);
	}
	
	/**
	 * Returns the size in human readable format
	 *
	 * @param integer $inSize
	 * @return string
	 * @static 
	 */
	static function humanReadableSize($inSize) {
		$i=0;
		$iec = array("B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB");
		while (($inSize/1024)>1) {
			$inSize=$inSize/1024;
			$i++;
		}
		return substr($inSize,0,strpos($inSize,'.')+4).' '.$iec[$i];
	}
	
	/**
	 * Returns the time difference in a human readable format
	 *
	 * @param integer $inTimeInSeconds
	 * @return string
	 * @static 
	 */
	static function humanReadableTime($inTimeInSeconds) {
		$inTimeInSeconds = round($inTimeInSeconds,1);
		if (($inHumanTime = $inTimeInSeconds / (365*24*60*60)) > 1 ) {
			return 'Over a year';
		}
		if (($inHumanTime = $inTimeInSeconds / (24*60*60)) > 1 ) {
			return round($inHumanTime,1).' day'.(round($inHumanTime)>1?'s':'');;
		} elseif (($inHumanTime = $inTimeInSeconds / (60*60)) > 1 ) {
			return round($inHumanTime,1).' hour'.(round($inHumanTime)>1?'s':'');
		} elseif (($inHumanTime = $inTimeInSeconds / 60) > 1 ) {
			return round($inHumanTime).' min';
		} else {
			return $inTimeInSeconds.' sec';
		}
	}
	
	/**
	 * Returns a string from "SomethingLikeThis" into "Something Like This"
	 * 
	 * @param string $inString
	 * @param string $inSeparator (default is a space)
	 * @return string
	 * @static 
	 */
	static function convertCapitalizedString($inString, $inSeparator = ' ') {
		$match = false;
		while (preg_match('/([a-z])([A-Z])/',$inString,$match)) {
			$inString = str_replace($match[0],$match[1].$inSeparator.$match[2],$inString);
		}
		return $inString;
	}
	
	/**
	 * Returns human readable time difference between $inDateNow and $inDateThen
	 *
	 * @param string $inDateNow
	 * @param string $inDateThen
	 * @return string
	 * @static 
	 */
	static function getTimeDifferenceHuman($inDateNow, $inDateThen) {
		$now = utilityStringFunction::formatDate($inDateNow, false);
		$then = utilityStringFunction::formatDate($inDateThen, false);
		$diff = $now - $then;
		return self::humanReadableTime($diff);
	}
	
	/**
	 * Formats a date to $inFormat from $inDate, if $inFormat is false or null
	 * date is returned as unix timestamp
	 *
	 * @param string $inDate
	 * @param string $inFormat
	 * @return string
	 * @static 
	 */
	static function formatDate($inDate, $inFormat) {
		if ( !$inFormat ) {
			$inFormat = '%U';
		}
		return date($inFormat, strtotime($inDate));
	}
	
	/**
	 * Cleans the tailing slash off the path	
	 *
	 * @param string $inPath
	 * @return string
	 * @static 
	 */
	static function cleanPath($inPath) {
		return preg_replace('/\/$/','',$inPath);
	}
	
	/**
	 * Removes the ./ ../ from web paths in the supplied string
	 *
	 * @param string $inPath
	 * @return string
	 * @static 
	 */
	static function cleanPathBackHacks($inPath) {
		$path = str_replace(array('/./','/../','/.../'), '/', $inPath);
		$path = preg_replace("/[\/]{2,}/", '/', $path);
		return $path;
	}
	
	/**
	 * Ensures that a URI path only contains / 
	 *
	 * @param string $inPath
	 * @return string
	 * @static
	 */
	static function cleanUriSlashes($inPath) {
		return str_replace(array('\\','/'), '/', $inPath);
	}
	
	/**
	 * Replaces directory slashes with the system set directory separator
	 *
	 * @param string $inPath
	 * @return string
	 * @static 
	 */
	static function cleanDirSlashes($inPath) {
		$path = str_replace(array('\\','/'), system::getDirSeparator(), $inPath);
		$path = preg_replace("/[\/\\\]{2,}/", system::getDirSeparator(), $path);
		return $path;
	}
	
	/**
	 * Outputs an associative array of data in a formatted manner on the CLI
	 *
	 * @param array $inData
	 * @param array $inColumns
	 * @param integer $inWidth
	 * @return string
	 * @static 
	 * @deprecated since version 0.2.0
	 */
	static function cliDataPrint($inData, $inColumns=null, $inWidth=80) {
		throw new systemException(__CLASS__.'::'.__METHOD__.' is deprecated, use cliConsoleTools::'.__METHOD__);
	}
	
	/**
	 * Returns an array containing the calculated widths for the cliDataPrint method
	 *
	 * @param array $inData
	 * @param array $inColums
	 * @param integer $maxWidth
	 * @return array
	 * @static 
	 * @deprecated since version 0.2.0
	 */
	static function getWidths($inData,$inColums,$maxWidth) {
		throw new systemException(__CLASS__.'::'.__METHOD__.' is deprecated, use cliConsoleTools::'.__METHOD__);
	}
	
	/**
	 * Takes a string of key => value pairs and creates an associative array from them.
	 * Will always return an array, even if the string is empty.
	 *
	 * @param string $inString
	 * @param string $inParamSeparator
	 * @param string $inParamAssignment
	 * @return array
	 * @static 
	 */
	static function createArrayFromString($inString, $inParamSeparator = '&', $inParamAssignment = '=') {
		$params = array();
		if ( strlen(trim($inString)) > 0 ) {
			$tmpParams = explode($inParamSeparator, $inString);
			if ( count($tmpParams) > 0 ) {
				foreach ( $tmpParams as $paramPair ) {
					list($paramName, $paramValue) = explode($inParamAssignment, trim($paramPair));
					if ( strlen(trim($paramName)) > 0 ) {
						$params[trim($paramName)] = trim($paramValue); 
					}
				}
			} 
		}
		return $params;
	}
	
	/**
	 * Safely encodes a base64 string to be URL safe
	 * 
	 * Originally posted in the PHP manual:
	 * {@link http://ca.php.net/manual/en/function.base64-encode.php#82506}
	 *
	 * @param string $input
	 * @return string
	 * @static
	 */
	static function base64UrlEncode($input) {
	    return strtr(base64_encode($input), '+/=', '-_,');
	}
	
	/**
	 * Decodes a previous URL safe encoded base64 string
	 * 
	 * Originally posted in the PHP manual:
	 * {@link http://ca.php.net/manual/en/function.base64-encode.php#82506}
	 *
	 * @param string $input
	 * @return string
	 * @static
	 */
	static function base64UrlDecode($input) {
	    return base64_decode(strtr($input, '-_,', '+/=')); 
	}
	
	/**
	 * Converts standard encoded Microsoft "Smart-Quotes" to a character e.g. “ with "
	 * 
	 * If data is received in UTF-8 format, then use {@see convertRawUtf8SmartQuotes}
	 * Taken from: {@link http://shiflett.org/blog/2005/oct/convert-smart-quotes-with-php}
	 *
	 * @param string $inString
	 * @return string
	 * @static
	 */
	static function convertSmartQuotes($inString) {
		/*
		 *  ‘  8216  curly left single quote
		 *  ’  8217  apostrophe, curly right single quote
		 *  “  8220  curly left double quote
		 *  ”  8221  curly right double quote
		 *  —  8212  em dash
		 *  –  8211  en dash
		 *  …  8230  ellipsis
		 */
		$search = array(
			//'&',
			//'<',
			//'>',
			//'"',
			chr(212), chr(213), chr(210), chr(211), chr(209), chr(208), chr(201),
			chr(145), chr(146), chr(147), chr(148), chr(151), chr(150), chr(133)
		);
		$replace = array(
			//'&amp;',
			//'&lt;',
			//'&gt;',
			//'&quot;',
			'\'', '\'', '"', '"', '-', '-', '...',
			'\'', '\'', '"', '"', '-', '-', '...',
		);
		return str_replace($search, $replace, $inString);
	}

	/**
	 * Replaces Microsoft UTF8 "smart-quotes" with entities e.g. “ with "
	 * 
	 * This method requires the string to be in UTF-8 format, otherwise see {@see convertSmartQuotes}
	 * Taken from: {@link http://shiflett.org/blog/2005/oct/convert-smart-quotes-with-php}
	 * 
	 * @param string $inString
	 * @return string
	 * @static
	 */
	static function convertRawUtf8SmartQuotes($inString) {
		$search = array(
			chr(0xe2) . chr(0x80) . chr(0x98), // left single quote
			chr(0xe2) . chr(0x80) . chr(0x99), // right single quote
			chr(0xe2) . chr(0x80) . chr(0x9c), // left double quote 
			chr(0xe2) . chr(0x80) . chr(0x9d), // right double quote
			chr(0xe2) . chr(0x80) . chr(0x93), // dash short
			chr(0xe2) . chr(0x80) . chr(0x94), // dash long
			chr(0xe2) . chr(0x80) . chr(0xa6), // ellipsis
		);
		
		$replace = array(
			'\'', //'&lsquo;',
			'\'', //'&rsquo;',
			'"', //'&ldquo;',
			'"', //'&rdquo;',
			'-', //'&ndash;',
			'-', //'&mdash;',
			'...'
		);
		return str_replace($search, $replace, $inString);
	}
	
	/**
	 * Removes illegal UTF-8 characters from $inString
	 * 
	 * Requires either iconv or mbstring extension for handling conversion.
	 * Otherwise uses {@link utilityStringFunction::convertRawUtf8SmartQuotes}
	 * to try and remove smart quotes etc.
	 *
	 * Notes:
	 * The results of this method can be weird as it turns out that a properly stored
	 * UTF-8 string will maintain MS smart-quotes and other "intelligent characters";
	 * it is not stripped out. If you want to remove completely smart quotes, try
	 * either: {@link utilityStringFunction::convertRawUtf8SmartQuotes} or
	 * {@link utilityStringFunction::filterUtf8ToAscii}
	 * 
	 * @param string $inString
	 * @return string
	 */
	static function filterIllegalUtf8Chars($inString) {
		if ( extension_loaded('iconv') ) {
			return iconv('UTF-8', 'UTF-8//IGNORE', $inString);
		} elseif ( extension_loaded('mbstring') ) {
			mb_substitute_character('none');
			return mb_convert_encoding($inString, 'UTF-8', 'UTF-8');
		} else {
			return self::convertRawUtf8SmartQuotes($inString);
		}
	}

	/**
	 * Filters a UTF-8 string to ASCII, attempting to convert any characters
	 *
	 * This method uses iconv to perform the conversion with the //TRANSLIT flag.
	 * If TRANSLIT is not available this method will error. If iconv is not
	 * available, the original un-modified string is returned.
	 * 
	 * @param string $inString
	 * @return string
	 */
	static function filterUtf8ToAscii($inString) {
		if ( extension_loaded('iconv') ) {
			return iconv('UTF-8', 'ASCII//TRANSLIT', $inString);
		} else {
			return $inString;
		}
	}
	
	/**
	 * Safely converts $inString to a capitalised string based on $inEncoding
	 * 
	 * For example: "this is My String" becomes: "This Is My String". This function
	 * uses mb_string library to do the safe translation of UTF-8 or other encoded
	 * strings, otherwise the standard ucwords(strtolower()) combination is used
	 * which can result in characters becoming broken.
	 *
	 * @param string $inString
	 * @param string $inEncoding
	 * @return string
	 * @static
	 */
	static function capitaliseEncodedString($inString, $inEncoding = 'UTF-8') {
		if ( extension_loaded('mbstring') ) {
			$inString = mb_convert_case(mb_strtolower(trim($inString), $inEncoding), MB_CASE_TITLE, $inEncoding);
		} else {
			$inString = ucwords(strtolower(trim($inString)));
		}
		return $inString;
	}

	/**
	 * Safely converts $inString to a lowercased string based on $inEncoding
	 * 
	 * For example: "this is My String" becomes: "this is my string". This function
	 * uses mb_string library to do the safe translation of UTF-8 or other encoded
	 * strings, otherwise the standard strtolower() function is used which can
	 * result in characters becoming broken.
	 *
	 * @param string $inString
	 * @param string $inEncoding
	 * @return string
	 * @static
	 */
	static function lowercaseEncodedString($inString, $inEncoding = 'UTF-8') {
		if ( extension_loaded('mbstring') ) {
			$inString = mb_strtolower(trim($inString), $inEncoding);
		} else {
			$inString = strtolower(trim($inString));
		}
		return $inString;
	}

	/**
	 * Safely converts $inString to an uppercase string based on $inEncoding
	 * 
	 * For example: "this is My String" becomes: "THIS IS MY STRING". This function
	 * uses mb_string library to do the safe translation of UTF-8 or other encoded
	 * strings, otherwise the standard strtoupper() function is used which can
	 * result in characters becoming broken.
	 *
	 * @param string $inString
	 * @param string $inEncoding
	 * @return string
	 * @static
	 */
	static function uppercaseEncodedString($inString, $inEncoding = 'UTF-8') {
		if ( extension_loaded('mbstring') ) {
			$inString = mb_strtoupper(trim($inString), $inEncoding);
		} else {
			$inString = ucwords(strtolower(trim($inString)));
		}
		return $inString;
	}
	
	
	
	/**
	 * Method to trim whitespace from $inValue, to be used in array_walk and other related functions
	 *
	 * @param string &$inValue
	 * @param string $inKey (optional)
	 * @static
	 */
	static function trim(&$inValue, $inKey = null) {
		$inValue = trim($inValue); 
	}
}