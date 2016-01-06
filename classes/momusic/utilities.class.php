<?php

/**
 * momusicUtilities
 * 
 * Stored in momusicUtilities.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage momusicUtilities
 * @category momusicUtilities
 * @version $Rev: 80 $
 */

/**
 * momusicUtilities Class
 * 
 * A set of utility methods for handling data types and other shared methods.
 * 
 * @package mofilm
 * @subpackage momusicUtilities
 * @category momusicUtilities
 */
class momusicUtilities {

	/**
	 * Builds a datetime from the supplied data vars
	 * 
	 * $inDateVar is the name of the date component variable in $inData.
	 * $inTimeVar is the name of the time component variable in $inData.
	 * The date and time can be either strings or arrays of up to 3 elements
	 * as generated by Smarty html_select_date and html_select_time.
	 * 
	 * When using arrays for date and time, they should contain the following
	 * elements: date - array(Year, Month, Day) time - array(Hour, Minute, Second).
	 * 
	 * @param array $inData
	 * @param string $inDateVar
	 * @param string $inTimeVar
	 * @param string $inDefaultDate (optional) default date format if no date found (valid date() format)
	 * @param string $inDefaultTime (optional) default time to use if no time found
	 * @return string
	 * @static
	 */
	static function buildDate(array $inData, $inDateVar = 'Date', $inTimeVar = 'Time', $inDefaultDate = 'Y-m-d', $inDefaultTime = '00:00:00') {
		$date = $time = false;

		/*
		 * Handle the date component
		 */
		if ( isset($inData[$inDateVar]) ) {
			if ( is_array($inData[$inDateVar]) ) {
				switch ( count($inData[$inDateVar]) ) {
					case 3:
						$date = $inData[$inDateVar]['Year'] . '-' . $inData[$inDateVar]['Month'] . '-' . $inData[$inDateVar]['Day'];
						break;

					case 2:
						$date = $inData[$inDateVar]['Year'] . '-' . $inData[$inDateVar]['Month'] . '-01';
						break;

					case 1:
						$date = $inData[$inDateVar]['Year'] . '-01-01';
						break;
				}
			} else {
				if ( preg_match('/^\d{4}-\d{2}-\d{2}$/', $inData[$inDateVar]) ) {
					$date = $inData[$inDateVar];
				}
			}
		}

		/*
		 * Handle the time component
		 */
		if ( isset($inData[$inTimeVar]) ) {
			if ( is_array($inData[$inTimeVar]) ) {
				switch ( count($inData[$inTimeVar]) ) {
					case 3:
						$time = $inData[$inTimeVar]['Hour'] . ':' . $inData[$inTimeVar]['Minute'] . ':' . $inData[$inTimeVar]['Second'];
						break;

					case 2:
						$time = $inData[$inTimeVar]['Hour'] . ':' . $inData[$inTimeVar]['Minute'] . ':00';
						break;

					case 1:
						$time = $inData[$inTimeVar]['Hour'] . ':00:00';
						break;
				}
			} else {
				if ( preg_match('/\d{2}:\d{2}:\d{2}/', $inData[$inTimeVar]) ) {
					$time = $inData[$inTimeVar];
				}
			}
		}

		if ( !$date ) {
			$date = date($inDefaultDate);
		}

		if ( !$time ) {
			$time = $inDefaultTime;
		}

		return $date . ' ' . $time;
	}

	/**
	 * Creates a tinyUrl style random hash
	 *
	 * @param mixed $inHashData (optional) Additional data to use in generating a hash
	 * @param integer $inLength (optional) Hash length, default 8
	 * @return string
	 * @static
	 */
	public static function buildMiniHash($inHashData = null, $inLength = 8) {
		mt_srand(time());
		$randNum = mt_rand(1, 1000000);

		if ( $inHashData !== null ) {
			$hash = md5(serialize($inHashData) . ":$randNum:" . date('U'));
		} else {
			$hash = md5("$randNum:" . date('U'));
		}

		$shortHash = substr($hash, mt_rand(0, strlen($hash) - $inLength), $inLength);
		return $shortHash;
	}

	/**
	 * Returns a new random string of letters and numbers of length $inLength
	 * 
	 * @param integer $inLength
	 * @return string
	 * @static
	 */
	public static function generateRandomString($inLength = 8) {
		if ( !$inLength || !is_numeric($inLength) ) {
			$inLength = 8;
		}
		$validChars = 'bcdfghjkmnpqrstvwxyzBCDFGHJKMNPQRSTVWXYZ23456789';

		srand((double) microtime() * 1000000);
		$m = strlen($validChars);
		$rand = '';
		while ( $inLength-- ) {
			$rand .= substr($validChars, rand() % $m, 1);
		}
		return $rand;
	}

	/**
	 * Returns the 2 char ISO3166 country code for the IP address via MaxMind GeoIP Service
	 *
	 * @param string $inIpAddress (optional) will use $_SERVER['REMOTE_ADDR'] if you don't specify an IP
	 * @return string 2 char code
	 * @static
	 */
	static function getCountryFromIpAddress($inIpAddress = false) {
		$country = false;

		if ( !$inIpAddress ) {
			if ( !isset($_SERVER['REMOTE_ADDR']) ) {
				return false;
			}
			$inIpAddress = $_SERVER['REMOTE_ADDR'];
		}

		// get the 2 char ISO3166 country code from MaxMind
		$query = 'http://' .
			system::getConfig()->getParam('maxmind', 'server', 'geoip1.maxmind.com') . '/a?l=' .
			system::getConfig()->getParam('maxmind', 'license', 'pZlVkwRZFfMf') . '&i=' .
			$inIpAddress;

		$url = parse_url($query);
		$host = $url["host"];
		$path = $url["path"] . "?" . $url["query"];
		$errno = $errstr = $buf = false;
		$timeout = 15;
		$fp = @fsockopen($host, 80, $errno, $errstr, $timeout);
		if ( $fp ) {
			fputs($fp, "GET $path HTTP/1.0\nHost: " . $host . "\n\n");
			while ( !feof($fp) ) {
				$buf .= fgets($fp, 128);
			}
			$lines = explode("\n", $buf);
			$country = strtoupper($lines[count($lines) - 1]);
			@fclose($fp);
		}
		if ( stripos($country, 'IP_NOT_FOUND') !== false ) {
			$country = false;
		}
		return $country;
	}

	public static function solrSearch() {
		$curl_handle = curl_init();
		curl_setopt($curl_handle, CURLOPT_URL, 'http://localhost:8080/solr/select/?q=s_id:2');
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		$buffer = curl_exec($curl_handle);
		curl_close($curl_handle);
		systemLog::message($buffer);
	}

}