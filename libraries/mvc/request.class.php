<?php
/**
 * mvcRequest.class.php
 * 
 * mvcRequest class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcRequest
 * @version $Rev: 725 $
 */


/**
 * mvcRequest class
 * 
 * Handles the inbound request passing data from the distributor to the controller.
 * mvcRequest is a static class that wraps baseSet and is populated by the
 * {@link mvcDistributorBase} class.
 * 
 * mvcRequest is then used within mvcControllers and Views to fetch additional
 * information about the request.
 * 
 * The requested mime-type and output format are held within the request object.
 *
 * Please note: mvcRequest does not encapsulate _POST, _GET, _REQUEST or _COOKIE.
 * Access into these arrays should be handled via the {@link utilityInputManager}
 * that uses the PHP {@link http://www.php.net/filter filter} extension.
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcRequest
 */
class mvcRequest extends baseSet {

	const OUTPUT_ATOM = 'atom';
	const OUTPUT_FRAGMENT = 'fragment';
	const OUTPUT_HTML = 'html';
	const OUTPUT_JAVASCRIPT = 'jscript';
	const OUTPUT_JSON = 'json';
	const OUTPUT_RSS = 'rss';
	const OUTPUT_WML = 'wml';
	const OUTPUT_XHTML = 'xhtml';
	const OUTPUT_XML = 'xml';
	
	const PARAM_ACCEPT_TYPES = 'accept.types';
	const PARAM_CONTROLLER_URI = 'mvc.controller.uri';
	const PARAM_DISTRIBUTOR = 'mvc.distributor';
	const PARAM_DISTRIBUTOR_CONTROLLER_MAP = 'mvc.distributor.controller.map';
	const PARAM_DISTRIBUTOR_MIMETYPE = 'mvc.distributor.output.mimetype';
	const PARAM_DISTRIBUTOR_OUTPUT_TYPE = 'mvc.distributor.output.type';
	const PARAM_DISTRIBUTOR_SERVER_NAME = 'mvc.distributor.server.name';
	const PARAM_DISTRIBUTOR_SITE_PATH = 'mvc.distributor.site.path';
	const PARAM_REQUEST_LOCALE = 'request.locale';
	const PARAM_REQUEST_LOCALE_SOURCE = 'request.locale.source';
	const PARAM_REQUEST_ORIGINAL_URI = 'request.original.uri';
	const PARAM_REQUEST_SESSION = 'request.session';
	const PARAM_REQUEST_URI = 'request.uri';
	const PARAM_SERVER_NAME = 'server.name';
	const PARAM_SERVER_ADDRESS = 'server.address';
	const PARAM_SERVER_PORT = 'server.port';
	const PARAM_SERVER_PROTOCOL = 'server.protocol';
	const PARAM_SERVER_SSL = 'server.ssl';
	
	const LOCALE_SOURCE_COOKIE = 'cookie';
	const LOCALE_SOURCE_OTHER = 'direct-call-to-setLocale';
	const LOCALE_SOURCE_REQUEST = 'request';
	const LOCALE_SOURCE_SESSION = 'session';
	const LOCALE_SOURCE_SYSTEM = 'system';
	const LOCALE_SOURCE_URI = 'uri';
	
	/**
	 * Stores an instance of mvcRequest
	 *
	 * @var mvcRequest
	 */
	private static $_Instance = false;
	
	
	
	/**
	 * Returns new mvcRequest instance
	 *
	 * @return mvcRequest
	 */
	private function __construct() {
		$this->initialise();
	}
	
	
	
	/**
	 * Returns an instance of mvcRequest
	 *
	 * @return mvcRequest
	 * @access public
	 * @static 
	 */
	public static function getInstance() {
		if ( !self::$_Instance instanceof mvcRequest ) {
			self::$_Instance = new mvcRequest();
		}
		return self::$_Instance;
	}
	
	
	
	/**
	 * Initialises the request
	 *
	 * @return void
	 */
	function initialise() {
		$this->resetParams();
		$this->setServerName($_SERVER['SERVER_NAME']);
		$this->setServerAddress($_SERVER['SERVER_ADDR']);
		$this->setServerPort($_SERVER['SERVER_PORT']);
		$this->setServerProtocol($_SERVER['SERVER_PROTOCOL']);
		$this->setRequestUri(preg_replace('/\?.*$/','', utilityStringFunction::cleanPathBackHacks($_SERVER['REQUEST_URI'])));
		$this->setParam(self::PARAM_REQUEST_ORIGINAL_URI, $_SERVER['REQUEST_URI']);
		$this->setServerSsl((isset($_SERVER['HTTPS']) || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on')));
		$this->setAcceptTypes();
		$this->setFileType();
	}
	
	/**
	 * Figures out the format the response should be sent in
	 * 
	 * This method handles checking for an override. This occurs first,
	 * before a more generic URI check. Overrides are formatted as: /as.xml
	 * Any valid output type can be used as an override - however there
	 * may not be a template for that type.
	 * 
	 * @return mvcRequest
	 */
	public function setFileType() {
		$matches = array();
		preg_match('/\/as.([a-z]{2,})$/', $this->getRequestUri(), $matches);
		if ( count($matches) > 0 ) {
			$this->setOutputType($matches[1]);
			$this->setRequestUri(str_replace($matches[0], "", $this->getRequestUri()));
		} else {
			if ( strpos($this->getServerName(), 'wap.') === 0 ) {
				/*
				 * Must disable sending of session cookies (may/can cause problems for phones)
				 */
				ini_set("session.use_cookies", 0);
				if ( $this->isXhtmlMobile() ) {
					$this->setOutputType(mvcRequest::OUTPUT_XHTML);
				} else {
					$this->setOutputType(mvcRequest::OUTPUT_WML);
				}
			} elseif (strpos($this->getServerName(),'rss.') === 0) {
				$this->setOutputType(mvcRequest::OUTPUT_RSS);
			} elseif (strpos($this->getServerName(),'xml.') === 0) {
				$this->setOutputType(mvcRequest::OUTPUT_XML);
			} elseif (strpos($this->getServerName(),'atom.') === 0) {
				$this->setOutputType(mvcRequest::OUTPUT_ATOM);
			} else {
				$this->setOutputType(mvcRequest::OUTPUT_HTML);
			}
		}
		return $this;
	}
	
	/**
	 * Returns true if mobile headers suggest XHTML support
	 *
	 * @return boolean
	 */
	function isXhtmlMobile() {
		/*
		 * Array of valid XHTML mime types that *should* appear in the accept headers, text/css is also required for text/html
		 */
		$xhtmlTypes = array(
			'application/xhtml+xml',
			'application/vnd.wap.xhtml+xml',
			'text/html'
		);
		
		foreach ( $xhtmlTypes as $type ) {
			switch ( $type ) {
				case 'text/html':
					if ( array_search($type, $this->getAcceptTypes()) !== false && array_search('text/css', $this->getAcceptTypes()) !== false ) {
						return true;
					}
				break;
					
				default:
					if ( array_search($type, $this->getAcceptTypes()) !== false ) {
						return true;
					}
				break;
			}
		}
		
		/*
		 * MS mobile devices sometimes have no accept headers but * / *, all MSIE mobiles support xhtml though
		 */
		if ( stripos($_SERVER['HTTP_USER_AGENT'], ' MSIE ') !== false && array_search('*/*', $this->getAcceptTypes()) !== false ) {
			return true;
		}
		return false;
	}

	/**
	 * Returns true if the current request is an ajax request
	 *
	 * @return boolean
	 */
	function isAjaxRequest() {
		if (
			isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
			strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
		) {
			return true;
		}
		if ( (isset($_GET['ajax']) || isset($_POST['ajax'])) && ($_GET['ajax'] == true || $_POST['ajax'] == true) ) {
			return true;
		}
		return false;
	}
	
	
	
	/**
	 * Returns the set locale for this request
	 *
	 * If no locale has been set yet, it will be detected. Detection
	 * operates on various fallbacks starting with a _COOKIE value of
	 * 'locale', then _SESSION param of 'locale', any request var
	 * named 'lang', the URI string for the beginning component and
	 * finally the registry value is fetched if any of the above
	 * fail or are not valid locales.
	 * 
	 * @return string 
	 */
	function getLocale() {
		if ( !$this->getParam(self::PARAM_REQUEST_LOCALE) ) {
			$this->setLocale();
		}
		return $this->getParam(self::PARAM_REQUEST_LOCALE);
	}
	
	/**
	 * Sets the locale for this request, if $inLocale is null attempts this automatically
	 * 
	 * Detection operates on various fallbacks starting with a _COOKIE value of
	 * 'locale', then _SESSION param of 'locale', any request var named 'lang',
	 * the URI string for the beginning component and finally the registry value
	 * is fetched if any of the above fail or are not valid locales.
	 *
	 * @param string $inLocale
	 * @return mvcRequest
	 */
	function setLocale($inLocale = null) {
		$locale = false;
		$source = false;
		if ( $inLocale === null || strlen($inLocale) < 2 ) {
			if ( !$locale && isset($_COOKIE['locale']) ) {
				$locale = strip_tags(trim(stripslashes($_COOKIE['locale'])));
				$source = self::LOCALE_SOURCE_COOKIE;
			}
			if ( !$locale && $this->getSession() instanceof mvcSessionBase ) {
				$locale = $this->getSession()->getParam('locale');
				$source = self::LOCALE_SOURCE_SESSION;
			}
			if ( !$locale && isset($_REQUEST['lang']) ) {
				$locale = strip_tags(trim(stripslashes($_REQUEST['lang'])));
				$source = self::LOCALE_SOURCE_REQUEST;
			}
			if ( !$locale && strlen($this->getRequestUri()) > 0 ) {
				// check first chunk of request
				$pieces = explode('/', $this->getRequestUri());
				if ( strlen($pieces[0]) == 0 ) {
					unset($pieces[0]);
				}
				$locale = array_shift($pieces);
				$source = self::LOCALE_SOURCE_URI;
			}
		} else {
			$locale = strip_tags(trim(stripslashes($inLocale)));
			$source = self::LOCALE_SOURCE_OTHER;
		}
		
		if ( systemLocale::isValidLocale($locale, true) ) {
			$this->setParam(self::PARAM_REQUEST_LOCALE, $locale);
			$this->setParam(self::PARAM_REQUEST_LOCALE_SOURCE, $source);
		} else {
			$this->setParam(self::PARAM_REQUEST_LOCALE, (string) system::getLocale());
			$this->setParam(self::PARAM_REQUEST_LOCALE_SOURCE, self::LOCALE_SOURCE_SYSTEM);
		}
		return $this;
	}
	
	/**
	 * Returns the locale source
	 *
	 * @return string
	 */
	function getLocaleSource() {
		return $this->getParam(self::PARAM_REQUEST_LOCALE_SOURCE);
	}
	
	/**
	 * Get the output type for this request
	 *
	 * @return string
	 */
	function getOutputType() {
		return $this->getParam(self::PARAM_DISTRIBUTOR_OUTPUT_TYPE);
	}
	
	/**
	 * Returns the mime-type to be used with the output type
	 *
	 * @return string
	 */
	function getMimeType() {
		return $this->getParam(self::PARAM_DISTRIBUTOR_MIMETYPE);
	}
	
	/**
	 * Set output type (and hence mime type)
	 *
	 * @param string $inType
	 * @return mvcRequest
	 */
	function setOutputType($inType) {
		$this->setParam(self::PARAM_DISTRIBUTOR_OUTPUT_TYPE, $inType);
		switch ($inType) {
			case "fragment": $this->setParam(self::PARAM_DISTRIBUTOR_MIMETYPE, 'text/html');             break;
			case "xhtml":    $this->setParam(self::PARAM_DISTRIBUTOR_MIMETYPE, 'application/xhtml+xml'); break;
			case "wml":      $this->setParam(self::PARAM_DISTRIBUTOR_MIMETYPE, 'text/vnd.wap.wml');      break;
			case "rss":      $this->setParam(self::PARAM_DISTRIBUTOR_MIMETYPE, 'application/rss+xml');   break;
			case "atom":     $this->setParam(self::PARAM_DISTRIBUTOR_MIMETYPE, 'application/atom+xml');  break;
			case "xml":      $this->setParam(self::PARAM_DISTRIBUTOR_MIMETYPE, 'application/xml');       break;
			case 'json':     $this->setParam(self::PARAM_DISTRIBUTOR_MIMETYPE, 'application/json');      break;
			case "jscript":
			case "js":
				$this->setParam(self::PARAM_DISTRIBUTOR_MIMETYPE, 'text/javascript');
			break;
			
			default:
				$this->setParam(self::PARAM_DISTRIBUTOR_MIMETYPE, 'text/html');
				$this->setParam(self::PARAM_DISTRIBUTOR_OUTPUT_TYPE, 'html');
		}
		return $this;
	}
	
	/**
	 * Returns the current requests session
	 *
	 * @return mvcSession
	 */
	function getSession() {
		return $this->getParam(self::PARAM_REQUEST_SESSION);
	}
	
	/**
	 * Sets the request session
	 *
	 * @param mvcSessionBase $inSession
	 * @return mvcRequest
	 */
	function setSession(mvcSessionBase $inSession) {
		return $this->setParam(self::PARAM_REQUEST_SESSION, $inSession);
	}
	
	/**
	 * Returns the distributor for this request
	 *
	 * @return mvcDistributorBase
	 */
	function getDistributor() {
		return $this->getParam(self::PARAM_DISTRIBUTOR);
	}
	
	/**
	 * Set main distributor for this request
	 *
	 * @param mvcDistributorBase $inDistributor
	 * @return mvcRequest
	 */
	function setDistributor($inDistributor){
		return $this->setParam(self::PARAM_DISTRIBUTOR, $inDistributor);
	}

	/**
	 * Returns the distributor server name for this request
	 * 
	 * Note: do not confuse Distributor Server Name with actual server name. The 
	 * distributor server name is how the system identifies the site in the main
	 * websites folder.
	 *
	 * @return string
	 */
	function getDistributorServerName() {
		return $this->getParam(self::PARAM_DISTRIBUTOR_SERVER_NAME);
	}
	
	/**
	 * Set the Distributor server name for this request
	 * 
	 * Note: do not confuse Distributor Server Name with actual server name. The 
	 * distributor server name is how the system identifies the site in the main
	 * websites folder.
	 *
	 * @param string $inServerName
	 * @return mvcRequest
	 */
	function setDistributorServerName($inServerName){
		return $this->setParam(self::PARAM_DISTRIBUTOR_SERVER_NAME, $inServerName);
	}

	/**
	 * Returns the site path for this request
	 *
	 * @return string
	 */
	function getDistributorSitePath() {
		return $this->getParam(self::PARAM_DISTRIBUTOR_SITE_PATH);
	}
	
	/**
	 * Set the site path for this request
	 *
	 * @param string $inSitePath
	 * @return mvcRequest
	 */
	function setDistributorSitePath($inSitePath){
		return $this->setParam(self::PARAM_DISTRIBUTOR_SITE_PATH, $inSitePath);
	}
	
	/**
	 * Get current request controller map
	 *
	 * @return mvcControllerMap
	 */
	function getControllerMap() {
		return $this->getParam(self::PARAM_DISTRIBUTOR_CONTROLLER_MAP);
	}
	
	/**
	 * Set current request controller map name to value $inParamValue
	 *
	 * @param mvcControllerMap $inParamValue
	 * @return mvcRequest
	 */
	function setControllerMap(mvcControllerMap $inParamValue) {
		return $this->setParam(self::PARAM_DISTRIBUTOR_CONTROLLER_MAP, $inParamValue);
	}
	
	/**
	 * Returns the request URI
	 *
	 * @return string
	 */
	function getRequestUri() {
		return $this->getParam(self::PARAM_REQUEST_URI);
	}
	
	/**
	 * Set the request URI
	 *
	 * @param string $inParamValue
	 * @return mvcRequest
	 */
	function setRequestUri($inParamValue) {
		return $this->setParam(self::PARAM_REQUEST_URI, $inParamValue);
	}
	
	/**
	 * Returns the server name
	 *
	 * @return string
	 */
	function getServerName() {
		return $this->getParam(self::PARAM_SERVER_NAME);
	}
	
	/**
	 * Set server name
	 *
	 * @param string $inParamValue
	 * @return mvcRequest
	 */
	function setServerName($inParamValue) {
		return $this->setParam(self::PARAM_SERVER_NAME, $inParamValue);
	}
	
	/**
	 * Returns the server name
	 *
	 * @return string
	 */
	function getServerAddress() {
		return $this->getParam(self::PARAM_SERVER_ADDRESS);
	}
	
	/**
	 * Set server name
	 *
	 * @param string $inParamValue
	 * @return mvcRequest
	 */
	function setServerAddress($inParamValue) {
		return $this->setParam(self::PARAM_SERVER_ADDRESS, $inParamValue);
	}
	
	/**
	 * Returns the server Port
	 *
	 * @return string
	 */
	function getServerPort() {
		return $this->getParam(self::PARAM_SERVER_PORT);
	}
	
	/**
	 * Set server Port
	 *
	 * @param string $inParamValue
	 * @return mvcRequest
	 */
	function setServerPort($inParamValue) {
		return $this->setParam(self::PARAM_SERVER_PORT, $inParamValue);
	}
	
	/**
	 * Returns the server name
	 *
	 * @return string
	 */
	function getServerProtocol() {
		return $this->getParam(self::PARAM_SERVER_PROTOCOL);
	}
	
	/**
	 * Set server name
	 *
	 * @param string $inParamValue
	 * @return mvcRequest
	 */
	function setServerProtocol($inParamValue) {
		return $this->setParam(self::PARAM_SERVER_PROTOCOL, $inParamValue);
	}
	
	/**
	 * Returns true if the current request is over SSL
	 *
	 * @return boolean
	 */
	function getServerSsl() {
		return $this->getParam(self::PARAM_SERVER_SSL);
	}
	
	/**
	 * Set if the request is over SSL
	 *
	 * @param boolean $inParamValue
	 * @return mvcRequest
	 */
	function setServerSsl($inParamValue) {
		return $this->setParam(self::PARAM_SERVER_SSL, $inParamValue);
	}

	/**
	 * Returns array of current accept types from connecting device (may have been overridden)
	 *
	 * @return array
	 */
	function getAcceptTypes() {
		return $this->getParam(self::PARAM_ACCEPT_TYPES);
	}
	
	/**
	 * Set connecting device accept headers, or override is supplied (must be uni-dimensional array of mime types)
	 *
	 * @param array $inRequestTypes
	 * @return mvcRequest
	 */
	function setAcceptTypes($inRequestTypes = null) {
		if ( $inRequestTypes === null ) {
			$types = array();
			
			$acceptTypes = explode(",", $_SERVER['HTTP_ACCEPT']);
			foreach ($acceptTypes as $string) {
				list($list,) = explode(";", $string); // remove preference e.g. ;q=0.8
				$types[] = trim($list);
			}
		}
		if ( $inRequestTypes !== null && is_array($inRequestTypes) ) {
			$types = $inRequestTypes;
		}
		
		if ( $types ) {
			$this->setParam(self::PARAM_ACCEPT_TYPES, $types);
		}
		return $this;
	}
	
	/**
	 * Returns the controller URI identified in the request
	 *
	 * @return string
	 */
	function getControllerUri() {
		return $this->getParam(self::PARAM_CONTROLLER_URI);
	}
	
	/**
	 * Sets the controller URI to $inUri
	 *
	 * @param string $inUri
	 * @return mvcRequest
	 */
	function setControllerUri($inUri) {
		return $this->setParam(self::PARAM_CONTROLLER_URI, $inUri);
	}
	
	
	
	/**
	 * Returns param named $inKey
	 *
	 * @param string $inKey
	 * @return mixed
	 */
	function getParam($inKey = null) {
		return $this->_getItem($inKey);
	}
	
	/**
	 * Set param in request object
	 *
	 * @param string $inKey
	 * @param mixed $inParamValue
	 * @return mvcRequest
	 */
	function setParam($inKey, $inParamValue) {
		return $this->_setItem($inKey, $inParamValue);
	}
	
	/**
	 * Reset param set
	 *
	 * @return mvcRequest
	 */
	function resetParams(){
		return $this->_resetSet();
	}
	
	/**
	 * Return param count
	 *
	 * @return integer
	 */
	function getParamCount(){
		return $this->_itemCount();
	}
}