<?php
/**
 * utilityCurl
 * 
 * Stored in curl.class.php
 * 
 * @package scorpio
 * @subpackage utility
 * @category utilityCurl
 * @version $Rev: 706 $
 */


/**
 * utilityCurl
 * 
 * This is a wrapper around the CURL extension to make it a bit easier to
 * use. It is based on code found at the PHP.net manual originally by:
 * lmshad at wp dot pl dot foo dot bar.
 * 
 * The main methods are for fetching content and downloading files to a
 * local file system. Proxy servers can be configured and the useragent
 * can be changed. The default useragent is Firefox/3.0.
 * 
 * Any CURL options can be specified in the public $options variable. These
 * will be passed to CURL when it is started.
 * 
 * <code>
 * // fetch a URI
 * echo utilityCurl::fetchContent('www.onet.pl');
 * 
 * // download a file to the local drive
 * utilityCurl::downloadFile('http://download.gadu-gadu.pl/gg77.exe', 'c:/temp/gg77.exe');
 * </code>
 * 
 * @author lmshad
 * @link http://ca.php.net/manual/en/function.curl-exec.php
 * @package scorpio
 * @subpackage utility
 * @category utilityCurl
 */ 
class utilityCurl {
	
	/**
	 * An array of useragents
	 * 
	 * @var array
	 * @static
	 */
	public static $userAgents = array(
		'FireFox3' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; pl; rv:1.9) Gecko/2008052906 Firefox/3.0',
		'GoogleBot' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
		'IE7' => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)',
		'Netscape' => 'Mozilla/4.8 [en] (Windows NT 6.0; U)',
		'Opera' => 'Opera/9.25 (Windows NT 6.0; U; en)'
	);

	/**
	 * Array of default options
	 *
	 * @var array
	 * @static
	 */
	public static $options = array(
		CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; pl; rv:1.9) Gecko/2008052906 Firefox/3.0',
		CURLOPT_AUTOREFERER => true,
		CURLOPT_COOKIEFILE => '',
		CURLOPT_FOLLOWLOCATION => true
	);
	
	/**
	 * Internal array of proxy servers
	 * 
	 * @var array
	 * @access private
	 * @static 
	 */
	private static $proxyServers = array();
	
	/**
	 * Proxy server count
	 * 
	 * @var integer
	 * @access private
	 * @static
	 */
	private static $proxyCount = 0;
	
	/**
	 * Current proxy server
	 * 
	 * @var integer
	 * @access private
	 * @static 
	 */
	private static $currentProxyIndex = 0;
	
	/**
	 * Add a new proxy server to the internal list
	 * 
	 * @param string $inUri
	 * @return void
	 * @static 
	 */
	public static function addProxyServer($inUri) {
		self::$proxyServers[] = $inUri;
		++self::$proxyCount;
	}

	/**
	 * Fetches content from $inUri, returning it
	 * 
	 * @param string $inUri
	 * @return string
	 * @static 
	 * @throws systemException
	 */
	public static function fetchContent($inUri) {
		if ( ($curl = curl_init($inUri)) == false ) {
			throw new systemException("curl_init error for url $inUri.");
		}
		
		if ( self::$proxyCount > 0 ) {
			$proxy = self::$proxyServers[self::$currentProxyIndex++ % self::$proxyCount];
			curl_setopt($curl, CURLOPT_PROXY, $proxy);
			systemLog::debug("Reading $inUri [Proxy: $proxy] ... ");
		} else {
			systemLog::debug("Reading $inUri ... ");
		}
		
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt_array($curl, self::$options);
		
		$content = curl_exec($curl);
		if ( $content === false ) {
			throw new systemException('curl_exec error for url '.$inUri.' ['.curl_errno($curl).'] '.curl_error($curl));
		}
		
		curl_close($curl);
		systemLog::debug("Done.");
		
		$content = preg_replace('#\n+#', ' ', $content);
		$content = preg_replace('#\s+#', ' ', $content);
		
		return $content;
	}
	
	/**
	 * Downloads the at $inUri to $inFilename
	 * 
	 * @param string $inUri
	 * @param string $inFilename
	 * @return string
	 * @static 
	 * @throws systemException
	 */
	public static function downloadFile($inUri, $inFilename) {
		if ( ($curl = curl_init($inUri)) == false ) {
			throw new systemException("curl_init error for url $inUri.");
		}
		
		if ( self::$proxyCount > 0 ) {
			$proxy = self::$proxyServers[self::$currentProxyIndex++ % self::$proxyCount];
			curl_setopt($curl, CURLOPT_PROXY, $proxy);
			systemLog::debug("Downloading $inUri [Proxy: $proxy] ... ");
		} else {
			systemLog::debug("Downloading $inUri ... ");
		}
		
		curl_setopt_array($curl, self::$options);
		
		if ( substr($inFilename, -1) == '/' ) {
			$targetDir = $inFilename;
			$inFilename = tempnam(system::getConfig()->getPathTemp()->getParamValue(), 'curl_');
		}
		if ( ($fp = fopen($inFilename, "wb")) === false ) {
			throw new systemException("fopen error for filename $inFilename");
		}
		
		curl_setopt($curl, CURLOPT_FILE, $fp);
		curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
		
		if ( curl_exec($curl) === false ) {
			fclose($fp);
			unlink($inFilename);
			throw new systemException('curl_exec error for url '.$inUri.' ['.curl_errno($curl).'] '.curl_error($curl));
		} elseif ( isset($targetDir) ) {
			$eurl = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
			preg_match('#^.*/(.+)$#', $eurl, $match);
			fclose($fp);
			rename($inFilename, "$targetDir{$match[1]}");
			$inFilename = "$targetDir{$match[1]}";
		} else {
			fclose($fp);
		}
		
		curl_close($curl);
		systemLog::debug("Done.");
		return $inFilename;
	}
}