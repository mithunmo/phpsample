<?php
/**
 * wurflManager class
 * 
 * Provides an interface to the wurfl object system
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage wurfl
 * @category wurflManager
 * @version $Rev: 722 $
 */


/**
 * wurflManager Class
 * 
 * The manager is used to instantiate an instance of the wurflDevice class. It can be created
 * in a number of ways:
 * 
 * <ol>
 *    <li>By DeviceID</li>
 *    <li>By UserAgent</li>
 *    <li>By WURFL ID</li>
 * </ol>
 * 
 * Both getInstanceByUserAgent() and getInstanceByWurflID call getInstance() internally and load
 * by deviceID. This is for performance reasons (it is faster to index by integer than string).
 * UserAgent lookups attempt to match first by the entire string, then failing that the string
 * is recursed until either it hits the minimum limit (4 characters) or a partial match is made.
 * 
 * These methods only ever return 1 device - even on failure an empty device is still returned.
 * 
 * <code>
 * // get by user agent 
 * $oDevice = wurflManager::getInstanceByUserAgent($_SERVER['HTTP_USER_AGENT']);
 * $oDevice->getCapabilities()->getCapability('model_name');
 * 
 * // get by wurflID
 * $oDevice = wurflManager::getInstanceByWurflID('sonyericsson_w880i_ver1');
 * $oDevice->getCapabilities()->getCapability('model_name');
 * </code>
 * 
 * For ease of use a "stub" method is included that will attempt to return just the stub of the user
 * agent. This is usually the part before the first / e.g. SonyEricssonW880i/R3HJ .... will return
 * SonyEricssonW880i. In the case of Mozilla, Vodafone and some LG, either the entire string is
 * returned, or the first two components, e.g. Vodafone/MOT-Z3/04.32.12 returns Vodafone/MOT-Z3.
 * 
 * @package scorpio
 * @subpackage wurfl
 * @category wurflManager
 */
class wurflManager {
	
	/**
	 * Stores the number of iterations required to match the last user-agent
	 *
	 * @var integer
	 * @access private
	 * @static
	 */
	private static $_UserAgentIterations = 0;
	
	/**
	 * @access private
	 * @throws wurflException
	 */
	private function __construct() {
		throw new wurflException('wurflManager is a static class and can not be instantiated');
	}
	
	
	
	/**
	 * Returns a wurflResultSet built from the supplied array of IDs
	 *
	 * @param array(1,2,3....n+1) $inList
	 * @return wurflResultSet
	 * @throws wurflException
	 * @static 
	 */
	static function getResultSetFromIdList(array $inList = array()) {
		$oResults = new wurflResultSet(array(), 0, new wurflSearch());
		if ( is_array($inList) && count($inList) > 0 ) {
			/*
			 * Check that each deviceID is numeric only
			 */
			foreach ( $inList as $deviceID ) {
				if ( !is_numeric($deviceID) || $deviceID < 1 || $deviceID == '' ) {
					throw new wurflException("List of devices contains non-numeric ID ($deviceID) or it is 0");
				}
			}
			
			$query = 'SELECT * FROM '.system::getConfig()->getDatabase('wurfl').'.devices WHERE deviceID IN ('.implode(",", $inList).')';
			
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				$list = array();
				foreach ( $oStmt as $row ) {
					$oObject = new wurflDevice();
					$oObject->loadFromArray($row);
					$list[] = $oObject;
				}
				
				$oResults->setResults($list);
				$oResults->setTotalResults(count($inList));
			}
		}
		return $oResults;
	}
	
	
	
	/**
	 * Returns an instance of wurflDevice, if not found wurflDevice is empty object
	 *
	 * @param integer $inDeviceID
	 * @return wurflDevice
	 * @static 
	 */
	static function getInstance($inDeviceID) {
		if ( $inDeviceID ) {
			$oCache = wurflDeviceCache::getInstance($inDeviceID);
			if ( $oCache->getDeviceID() ) {
				if ( false ) $oDevice = new wurflDevice();
				$oDevice = $oCache->getData();
				if ( $oDevice instanceof wurflDevice ) {
					if ( (time() - strtotime($oCache->getUpdateDate())) < wurflDeviceCache::CACHE_LIFETIME ) {
						return $oDevice;
					}
				}
			}
			
			$oDevice = wurflDevice::getInstance($inDeviceID);
			$oDevice->getDevicePath();
			
			$oCache = new wurflDeviceCache();
			$oCache->setDeviceID($inDeviceID);
			$oCache->setData($oDevice);
			$oCache->save();
			
			return $oDevice;
		}
		return new wurflDevice();
	}
	
	/**
	 * Returns an instance of wurflDevice by userAgent
	 *
	 * @param string $inUserAgent
	 * @return wurflDevice
	 * @static
	 * @todo DR: replace with a better UA lookup?
	 */
	static function getInstanceByUserAgent($inUserAgent) {
		if ( strlen($inUserAgent) > 0 ) {
    		$keySearch = false;
			$charSearch = '[0-9a-z\.\\\-]{0,20}'; #generic char selector
			$regExps = array(
				'safari' => "/(Safari\/$charSearch)/i",
				'khtml' => '/(\(KHTML, like Gecko\))/i',
				'applewebkit' => "/(AppleWebKit\/$charSearch)/i",
				' [' => '/(\[[a-z\-]{2,5}\])/i',
				' opera' => "/( Opera [0-9\\.]{1,5})/i",
				'up.link' => "/(UP\.link$charSearch)/i",
				'up.browser' => "/(UP\.Browser\/$charSearch)|(\(GUI\))/i",
				'profile' => "/(Profile\/$charSearch)/i",
				'configuration' =>  "/(Configuration\/$charSearch)/i",
				'netfront' => "/(Browser\/NetFront\/$charSearch)/i",
				'vendorid' => "/(VendorID\/$charSearch)/i",
				'semc-browser' => "/(Browser\/SEMC\-Browser\/$charSearch)/i",
				'mozilla' => "/(Mozilla\/$charSearch) (\(compatible; MSIE [0-9\.]{1,10}; Symbian OS; [0-9]{1,10}\))/i",
				'mib' => "/MIB\/$charSearch/i",
				'tmt/wap' => "/(\/WAP $charSearch)/i",
				' midp' => "/(MIDP\-2\.0\/CLDC\-1\.0)/i",
				'mmp' => "/(MMP\/$charSearch)/i",
				'/WAP2.0/MIDP2.0/CLDC1.0' => '/(\/WAP2\.0\/MIDP2\.0\/CLDC1\.0)/i',
				'symbian' => '/(SEMC\-Browser\/Symbian\/[0-9\.]{1,10})/i',
			);
    		
			$oStmt = dbManager::getInstance()->prepare('SELECT deviceID FROM '.system::getConfig()->getDatabase('wurfl').'.devices WHERE userAgent = :userAgent LIMIT 1');
			
			self::$_UserAgentIterations = 0;
			while ( strlen($inUserAgent) > 3 ) {
				self::$_UserAgentIterations++;
				$oStmt->bindValue(':userAgent', $inUserAgent);
				if ( $oStmt->execute() ) {
					$row = $oStmt->fetch();
					if ( $row !== false && is_array($row) ) {
						return self::getInstance($row['deviceID']);
					} else {
						/*
						 * To reduce our iterations, we are going to trim out crap from the Useragent string, we only do this on the first iteration
						 */
						if ( !$keySearch ) {
							foreach ( $regExps as $key => $regExp ) {
								if ( stripos($inUserAgent, $key) !== false && stripos($inUserAgent, $key) > 0 ) {
									$inUserAgent = preg_replace($regExp, '', $inUserAgent);
								}
							}
							$inUserAgent = preg_replace("/[ ]{2,}/", ' ', $inUserAgent);
							$keySearch = 1;
						}
						
						$inUserAgent = substr($inUserAgent, 0, -1);
						if ( strrpos($inUserAgent, ' ') == strlen($inUserAgent)-1 ) {
							$inUserAgent = substr($inUserAgent, 0, -1);
						}
					}
				}
			}
    	}
    	return new wurflDevice();
	}
	
	/**
	 * Returns an instance of wurflDevice by WurflID
	 *
	 * @param string $inWurflID
	 * @return wurflDevice
	 * @static 
	 */
	static function getInstanceByWurflID($inWurflID) {
		$sql = 'SELECT deviceID FROM '.system::getConfig()->getDatabase('wurfl').'.devices WHERE wurflID = :wurflID LIMIT 1';
		
		$oStmt = dbManager::getInstance()->prepare($sql);
		$oStmt->bindValue(':wurflID', $inWurflID);
		if ( $oStmt->execute() ) {
			$row = $oStmt->fetch();
			if ( $row !== false && is_array($row) ) {
				return self::getInstance($row['deviceID']);
			}
		}
		return new wurflDevice();
	}
	
	
	
	/**
     * Attempts to create a "stub" from the userAgent string, always returns a string
     *
     * @param string $inString
     * @return string
     * @static 
     */
    static function getUserAgentStub($inString) {
    	$return = '';
    	if ( is_string($inString) && strlen($inString) > 1 ) {
			/*
			 * First we attempt to create a quick suffix from the stub of the userAgent (everything before the first /)
			 * 
			 * e.g. Nokia6230i/33.1123.234 Configuration/1.1 xxx yyy zzz... becomes Nokia6230i
			 */
			$bits   = explode("/", $inString);
			$return = $bits[0];
			
			switch ( true ) {
				/*
				 * Exception: voda sometimes appear as vodafone/WhatWeReallyWant/...
				 * Exception: LG are now / sometimes using LG/ModelRef/.. and not LG-ModelRef
				 */
				case stripos($inString, 'vodafone') === 0:
				case stripos($inString, 'lg/') === 0:
					$return = $bits[0].'/'.$bits[1];
					break;
				
				/*
				 * Exception: For anything starting Mozilla/XX we can not do the quick hack as this will not match anything!
				 */
				case stripos($inString, 'mozilla') === 0:
					$return = $inString;
					break;
			}
		}
		return $return;
    }
    
    /**
     * Returns the number of iterations to match the last device
     *
     * @return integer
     * @static
     */
    static function getUserAgentIterations() {
    	return self::$_UserAgentIterations;
    }
}