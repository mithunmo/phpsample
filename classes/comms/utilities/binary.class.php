<?php
/**
 * commsUtilitiesBinary
 *
 * Stored in binary.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage utilities
 * @category commsUtilitiesBinary
 * @version $Rev: 10 $
 */


/**
 * commsUtilitiesBinary
 * 
 * Static methods for handling binary messages, used in the sending layer
 * 
 * @package comms
 * @subpackage utilities
 * @category commsUtilitiesBinary
 */
class commsUtilitiesBinary {
	
	/**
	 * Prevent instantiation of class
	 */
	private function __construct() {
	}
	
	/**
	 * Parses a message body and enforces messages are 7-bit only, returns parsed message
	 * 
	 * @param string $inNewLineChar
	 * @param string $inMessage
	 * @return string
	 * @static 
	 * @todo DR: Slow function, needs replacing
	 */	
	public static function parseMessage($inNewLineChar, $inMessage) {
		$res = '';
		$i = 0;
		$msgBody = $inMessage;
		
		$msgBody = preg_replace("/<CR><LF>/", $inNewLineChar, $msgBody);
		$msgBody = preg_replace("/\n\r/", $inNewLineChar, $msgBody);
		$msgBody = preg_replace("/\r\n/", $inNewLineChar, $msgBody);
		$msgBody = preg_replace("/\r/", $inNewLineChar, $msgBody);
		$msgBody = preg_replace("/\n/", $inNewLineChar, $msgBody);
		$msgBody = preg_replace("/%NL%/i", $inNewLineChar, $msgBody);
		
		// Loop through each character removing/replacing non 7-bit characters
		while ($i < strlen($msgBody)) {
			$char = $msgBody[$i];
			// get rid of weird ' characters and replace with the normal ones
			if ((ord($char) == 145) || (ord($char) == 146)) {
				$res .= chr(39);	
			} elseif (((ord($char) >= 32) && (ord($char) <= 126)) || (ord($char) == 10) || (ord($char) == 10)  || (ord($char) == 163)) {
				// this character is alphanumeric or punctuation, not a special character
				$res .= $char;
			} elseif (ord($char) == 38) {
				// Ampersand
				$res .= htmlspecialchars('&');
			} elseif (ord($char) == 128) {
				// Euro symbol (in URL-encoding-land)
				$res .= $char;
			}
			$i++;
		}
		
		// check length of string and truncate to 160 chars, wrapping at the word boundary
		if ( strlen($res) > 160 ) {
			systemLog::error("Message text is longer than 160 characters; truncating");
			systemLog::info("Original text: ".$res);
			$res = substr($res, 0, 160);
			systemLog::info("New text: ".$res);
		}
		return $res;
	}
	
	/**
	 * Removes HTML tags like <br> <html> <p> etc to leave just the text
	 * 
	 * @param string $message
	 * @return string
	 * @static
	 */
	public static function removeTags($inMessage) {
		$search = array (
			"'<script[^>]*?>.*?</script>'si",  // Strip out javascript
			"'<br>'i",
			"'<[\/\!]*?[^<>]*?>'si",           // Strip out html tags
			"'([\r\n])[\s]+'",                 // Strip out white space
			"'&(quot|#34);'i",                 // Replace html entities
			"'&(amp|#38);'i",
			"'&(lt|#60);'i",
			"'&(gt|#62);'i",
			"'&(nbsp|#160);'i",
			"'&(iexcl|#161);'i",
			"'&(cent|#162);'i",
			"'&(pound|#163);'i",
			"'&(copy|#169);'i",
			"'&#(\d+);'e");                    // evaluate as php
		$replace = array (
			"",
			"\n",
			"",
			"\\1",
			"\"",
			"&",
			"<",
			">",
			" ",
			chr(161),
			chr(162),
			chr(163),
			chr(169),
			"chr(\\1)");
		$inMessage = @preg_replace($search, $replace, $inMessage);
		return $inMessage;
	}
	
	
	
	/**
	 * Returns the number of hex pairs (ex A0 is one pair) in the supplied hex
	 * 
	 * @param string $hex
	 * @return integer
	 * @static 
	 */
	public static function hexLength($inHex) {
		$length = (int) (strlen($inHex) / 2);
		return intval($length);
	}
	
	/**
	 * Builds the UDH for a wap push (Wap SI)
	 * 
	 * Currently restricted to a single SMS - doesn't support concatenated messages
	 * 
	 * @param string $inTitle
	 * @param string $inUri
	 * @param integer $inExpiry expiry time in hours, min should be 24
	 * @return string
	 * @static 
	 */
	public static function generateWapPush($inTitle, $inUri, $inExpiry = 96) {
		$inTitle = preg_replace("/[^0-9a-zA-Z ]/", '', $inTitle);
		systemLog::debug("Wap Push: Title=$inTitle  URL=$inUri  Valid=$inExpiry");
	
		if ( empty($inTitle) ) {
			return false;
		}
		if ( empty($inUri) ) {
			return false;
		}
		$inExpiry = 24+$inExpiry;
		$inExpiry = date('Ymd',strtotime('+'.$inExpiry.'hours'));
		
		if ( $inExpiry === -1 ) {
			return false;
		}
		
		$creation_date = date('YmdHis');
		
		$UDH = '0605040B8423F0'; // user data header needed for WAP push
		
		// WAP Push PDU payload
		$UD  = '00'; // Push Transaction ID
		$UD .= '06'; // PDU Type (Push PDU)
		$UD .= '01'; // header length (1 byte)
		$UD .= 'AE'; // Content Type application/vnd.wap.sic
		
		// WBMXML Payload
		$UD .= '02'; // Version Number WBXML 1.2
		$UD .= '05'; // SI 1.0 Public Identifier
		$UD .= '6A'; // Charset=UTF-8 (MIBEnum 106)
		$UD .= '00'; // String table length
		$UD .= '45'; // SI with content
		$UD .= 'C6'; // Indication with content and attributes
		
		// Split out the URL into components host and path
		$arr = array();
		if ( preg_match("/^(http[s]{0,1}):/", $inUri, $arr) ) {
			$prot = $arr[1];
		} else {
			$prot = "http";
		}
		$temp = $prot.":\/\/";
		$inUri = preg_replace("/$temp/i", "", $inUri);
		$inUri = preg_replace("/[\/]{1,}$/", "", $inUri);
		$arr = array();
		
		if ( preg_match("/\//", $inUri) ) {
			$arr = split("\/", $inUri,2);
			$host = $prot."://".$arr[0];
			$path = "/".$arr[1];
		} else {
			$host = $prot."://".$inUri;
			$path = "";
		}
		
		/*
		 * ++++++++++++++++++++++++++++++++
		 * get correct token for HTTP
		 * C = http://
		 * D = http://www.
		 * E = https://
		 * F = https://www.
		 * ++++++++++++++++++++++++++++++++
		 */
		if ( stripos($host, "https://www.") === 0 ) {
			$href_token = '0F';
		} elseif ( stripos($host, "https://") === 0 ) {
			$href_token = '0E';
		} elseif ( stripos($host, "http://www.") === 0 ) {
			$href_token = '0D';
		} elseif ( stripos($host, "http://") === 0 ) {
			$href_token = '0C';
		}
		
		// remove any trailing slash from the host string               
		$host = preg_replace("/\/$/","", $host);
		
		/*
		 * ++++++++++++++++++++++++++++++++
		 * check for any well know top level domains that can be tokenized
		 * 85 = .com/
		 * 86 = .edu/
		 * 87 = .net/
		 * 88 = .org/
		 * ++++++++++++++++++++++++++++++++
		 */
		if ( preg_match("/\.com$/i",$host) ) {// match found
			$host = preg_replace("/\.com$/i", "", $host);
			$tld_token = '85'; // equivalent to '.com/'
			$path = preg_replace("/^\//", "", $path); // now don't need leading slash on path
		}
		if ( preg_match("/\.edu$/i",$host) ) {// match found
			$host = preg_replace("/\.edu$/i", "", $host);
			$tld_token = '86'; // equivalent to '.edu/'
			$path = preg_replace("/^\//", "", $path);
		}
		if ( preg_match("/\.net$/i",$host) ) {// match found
			$host = preg_replace("/\.net$/i", "", $host);
			$tld_token = '87'; // equivalent to '.net/'
			$path = preg_replace("/^\//", "", $path);
		}
		if ( preg_match("/\.org$/i",$host) ) {// match found
			$host = preg_replace("/\.org$/i", "", $host);
			$tld_token = '88'; // equivalent to '.org/'
			$path = preg_replace("/^\//", "", $path);
		}
		
		// now strip the leading protocol from the host
		$host = str_replace("https://", "", $host);
		$host = str_replace("http://", "", $host);
		$host = preg_replace("/^www\./i", "", $host);
		
		
		// add host
		$UD .= $href_token;
		$UD .= "03"; // inline string follows
		$UD .= strtoupper(bin2hex($host)) .'00';
		if (!empty($tld_token) ) {
			$UD .= $tld_token;
		}
		
		// add path
		if (!empty($path) ) {
			$UD .= "03"; // inline string follows
			$UD .= strtoupper(bin2hex($path)) .'00';
		}
		
		// add created date and time
		$UD .= '0A'; // Token for 'created='
		$UD .= 'C3'; // Opaque data follows
		$UD .= '07'; // Length Field (7 Bytes)
		$UD .= $creation_date;
		
		// add expiry Date 
		$UD .= '10'; // Token for 'si-expires';
		$UD .= 'C3'; // Opaque data follows
		$UD .= '04'; // Length field (4 bytes)
		$UD .= $inExpiry;
		
		// end of attributes
		$UD .= '01'; // END of indication attribute list
		
		// add Push Message Text
		$UD .= '03'; // inline string follows
		$UD .= strtoupper(bin2hex($inTitle)).'00';
		
		// end indication elements
		$UD .= '01'; // END of indication element
		$UD .= '01'; // END of si element
		
		return ($UDH.$UD);
	}
	
	/**
	 * Creates UDH and UDB hex string
	 * 
	 * Accepts OMA REL XML containing valid UID and Encryption Key, or pre-built hex string
	 * NOT including UDH. Returns a hex string containing the UDF and UDB.
	 *
	 * @param string $inRights OMA XML string containing rights data
	 * @return string
	 * @static 
	 */
	public static function generateWbxmlRights($inRights) {
		if ( !$inRights || strlen($inRights) < 1 ) {
			systemLog::warning('No rights data specified');
			return false;
		}
		
		$addUDBH = false;
		if ( stripos($inRights, '<o-ex:rights') === 0 ) {
			$oXML = @simplexml_load_string($inRights);
			if ( !is_object($oXML) ) {
				systemLog::error('OMA REL Rights XML failed to parse to valid XML object');
				return false;
			}
			
			$UD  = '03'; //WBXML version number ? WBXML version 1.3
			$UD .= '0E'; //Public identifier (-//OMA//DTD DRMREL 1.0//EN)
			$UD .= '6A'; //Charset = UTF-8
			$UD .= '00'; //String table length = 00 (empty string table)
			$UD .= 'C5'; //<o-ex:rights
			$UD .= '05'; //xmlns:o-ex=
			$UD .= '85'; //http://odrl.net/1.1/ODRL-EX (attribute value)
			$UD .= '06'; //xmlns:o-dd=
			$UD .= '86'; //http://odrl.net/1.1/ODRL-DD (attribute value)
			$UD .= '07'; //xmlns:ds=
			$UD .= '87'; //http://www.w3.org/2000/09/xmldsig#/ (attribute value)
			$UD .= '01'; //>
			$UD .= '46'; //<o-ex:context>
			$UD .= '47'; //<o-dd:version>
			$UD .= '03'; //STR_I (inline string follows with a terminator)
			$UD .= strtoupper(bin2hex('1.0'));
			$UD .= '00';
			
			$UD .= '01'; //</o-dd:version>
			$UD .= '01'; //</o-ex:context>
			$UD .= '49'; //<o-ex:agreement>
			$UD .= '4A'; //<o-ex:asset>
			$UD .= '46'; //<o-ex:context>
			$UD .= '48'; //<o-dd:uid>
			$UD .= '03'; //STR_I (inline string follows with a terminator)
			$contentID = $oXML->xpath('//o-dd:uid');
			$UD .= strtoupper(bin2hex((string) $contentID[0]));
			$UD .= '00';
			
			$UD .= '01'; //</o-dd:uid>
			$UD .= '01'; //</o-ex:context>
			$UD .= '4B'; //<ds:KeyInfo>
			$UD .= '4C'; //<ds:KeyValue>
			$UD .= 'C3'; //Opaque token
			$UD .= '10'; //Length of opaque data (16 bytes = 0x10)
			$key = $oXML->xpath('//ds:KeyValue');
			$UD .= strtoupper(bin2hex(base64_decode((string) $key[0])));
			$UD .= '01'; //</ds:KeyValue>
			$UD .= '01'; //</ds:KeyInfo>
			$UD .= '01'; //</o-ex:asset>
			$UD .= '4D'; //<o-ex:permission>
			
			if ( count($oXML->xpath('//o-dd:play')) > 0 ) {
				$UD .= '0E'; //<o-dd:play/>
			} elseif ( count($oXML->xpath('//o-dd:display')) > 0 ) {
				$UD .= 'OF'; //<o-dd:display/>
			} else {
				systemLog::error('OMA REL XML contains no rights information');
				return false;
			}
			
			$UD .= '01'; //</o-ex:permission>
			$UD .= '01'; //</o-ex:agreement>
			$UD .= '01'; //</o-ex:rights>
			$addUDBH = true;
		} elseif ( stripos($inRights, '030E') === 0 ) {
			$UD = $inRights;
			$addUDBH = true;
		} else {
			$UD = $inRights;
		}
		
		$UDH = '0605040B8423F0'; //Bin file header for message
		
		$UDBH  = '4C'; //transaction id
		$UDBH .= '06'; //PDU type ("push")
		$UDBH .= '03'; //headers length
		$UDBH .= 'CB'; //application/vnd.oma.drm.rights+wbxml
		$UDBH .= 'AF88'; //x-wap-application=drm.ua
		
		if ( $addUDBH === true ) {
			$UD = $UDBH.$UD;
		}
		return ($UDH.$UD);
	}
}