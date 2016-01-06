<?php
/**
 * utilityEncrypt
 * 
 * Stored in encrypt.class.php
 * 
 * @package scorpio
 * @subpackage utility
 * @category utilityEncrypt
 * @version $Rev$
 */


/**
 * utilityEncrypt
 *
 * Provides a simple wrapper around mcrypt functions to perform encryption
 * and decryption duties. This class is designed to use the Rijndael 256bit
 * encryption scheme. To use other schemes, extend and replace the various
 * methods.
 *
 * The key that is used should be kept protected at all times.
 *
 * <code>
 * $oEnc = new utilityEncrypt();
 * $oEnc->setKey($key);
 * $encrypted = $oEnc->encrypt('Some data to encrypt');
 *
 * // use in URI
 * echo $oEnc->toUriString($encrypted);
 *
 * // restoring from URI data
 * $data = utilityEncrypt::factory($key)
 *             ->decrypt(utilityEncrypt::fromUriString($uriData));
 * </code>
 *
 * @package scorpio
 * @subpackage utility
 * @category utilityEncrypt
 */
class utilityEncrypt {

	/**
	 * The maximum key length for the encryption scheme
	 *
	 * @var integer
	 */
	const MAX_KEY_LENGTH = 32;

	/**
	 * Map of characters to be replaced (URI unfriendly => URI friendly)
	 *
	 * @var array
	 * @access private
	 * @static
	 */
	private static $_CharacterMap = array(
		'/' => '_',
		'+' => '-',
	);

	/**
	 * Encryption key to use with encrypt library
	 *
	 * @var string
	 * @access private
	 */
	private $_Key;



	/**
	 * Creates a new Encrypt object
	 */
	function __construct() {

	}



	/**
	 * Creates a new encryption utility using $inKey
	 *
	 * @param string $inKey
	 * @return utilityEncrypt
	 * @static
	 */
	static function factory($inKey) {
		if ( strlen($inKey) > 1 ) {
			$oEnc = new utilityEncrypt();
			$oEnc->setKey($inKey);
			return $oEnc;
		} else {
			throw new systemException('Provided key is too short');
		}
	}

	/**
	 * Creates a random string of $inLength
	 *
	 * The string is created from the ASCII range specified by $inMinAscii and
	 * $inMaxAscii. These are both decimal integers e.g. 30 or 155 that correspond
	 * to the portion of the ASCII range to be used when generating a key. The
	 * default is to use characters between ASCII 32 (a space) and ASCII 126 (~).
	 * The range can be increased but may cause issues with displays that cannot
	 * handle particular character sequences.
	 *
	 * If the length is not specified the max key length value will be used instead.
	 *
	 * An optional array of characters can be specified that should NOT be used in
	 * the generated key string. This allows a large ASCII range to be used but to
	 * remove problematic characters such as back-ticks, quotes, braces etc.
	 *
	 * @param integer $inLength Default 32 characters
	 * @param integer $inMinAscii (optional) Default 32
	 * @param integer $inMaxAscii (optional) Default 126
	 * @param array $inIgnore (optional) Array of characters to ignore
	 * @return string
	 * @static
	 */
	static function createKeyString($inLength = 32, $inMinAscii = 32, $inMaxAscii = 126, $inIgnore = array()) {
		if ( !$inLength || !is_numeric($inLength) || $inLength < 1 ) {
			$inLength = self::MAX_KEY_LENGTH;
		}

		srand((double)microtime()*1000000);
		$rand = '';
		while ( strlen($rand) < $inLength ) {
			$char = chr(rand($inMinAscii, $inMaxAscii));
			if ( !in_array($char, $inIgnore) ) {
				$rand .= $char;
			}
		}
		
		return $rand;
	}

	/**
	 * Creates a URI safe string from the data
	 *
	 * @param string $inString
	 * @return string
	 * @static
	 */
	static function toUriString($inString) {
		return str_replace(array_keys(self::$_CharacterMap), array_values(self::$_CharacterMap), base64_encode($inString));
	}

	/**
	 * Returns the encoded data from the URI safe string
	 *
	 * @param string $inString
	 * @return string
	 * @static
	 */
	static function fromUriString($inString) {
		return base64_decode(str_replace(array_values(self::$_CharacterMap), array_keys(self::$_CharacterMap), $inString));
	}



	/**
	 * Encrypt data $inData using the current key
	 *
	 * The Rijndael 256bit scheme is used to encrypt the data. This method returns
	 * the encrypted data as is. To use in a URI context be sure to URI safe encode
	 * the data via {@link utilityEncrypt::toUriString()}.
	 *
	 * @param string $inData
	 * @return string
	 */
	function encrypt($inData) {
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		return mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->_Key, $inData, MCRYPT_MODE_ECB, $iv);
	}

	/**
	 * Decrypts data $inData using the current key
	 *
	 * The Rijndael 256bit scheme is used to decrypt the data. This method returns
	 * the decrypted data. If decrypting a URI encoded string, be sure to first
	 * decode it via {@link utilityEncrypt::fromUriString()}
	 *
	 * @return string
	 * @access public
	 */
	public function decrypt($inData) {
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		return mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->_Key, $inData, MCRYPT_MODE_ECB, $iv);
	}

	/**
	 * Returns the current encryption key
	 *
	 * @return string
	 */
	function getKey() {
		return $this->_Key;
	}

	/**
	 * Sets the encryption key to use
	 *
	 * If the key is longer than {@link utilityEncrypt::MAX_KEY_LENGTH} it will be
	 * trimmed to that number of characters.
	 *
	 * @param string $inKey
	 * @return utilityEncrypt
	 */
	function setKey($inKey) {
		if ( $this->_Key !== $inKey ) {
			$this->_Key = substr($inKey, 0, self::MAX_KEY_LENGTH);
		}
		return $this;
	}
}