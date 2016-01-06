<?php
/**
 * cliRequest Class
 * 
 * Stored in request.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliRequest
 * @version $Rev: 722 $
 */


/**
 * cliRequest class
 *
 * Holds the CLI request data, this object replaces cliArguments.
 * cliRequest separates switches from arguments (parameters). Switches are
 * any single character with a leading hypen e.g. -h -g -f or a set of
 * switches: -vVoh
 * 
 * Arguments are either --Arg or --Arg=value or Arg value. --Arg<space>value
 * is not supported.
 * 
 * cliRequest is required for {@link cliApplication}, {@link cliDaemon} and all
 * of the {@link cliCommand} objects. The cliRequest is a singleton and no more
 * than one instance should ever be present in a cli application.
 * 
 * <code>
 * $oRequest = cliRequest::getInstance();
 * $oRequest->getParam('some value');
 * 
 * if ( $oRequest->getSwitch('w') ) {
 *     // do something on the 'w' switch
 * }
 * </code>
 * 
 * cliRequest can capture user input during application execution. Simply call
 * {@link cliRequest::getUserInput()}. This causes the script to wait for input
 * from STDIN. Basic validation can be performed during this request by requiring
 * that the user answer be in an array of values.
 * 
 * Finally: the cliRequest acts as a transport for the running application in
 * {@link cliApplication}. This allows the application to be referenced. See
 * the cliApplication for further information.
 * 
 * @package scorpio
 * @subpackage cli
 * @category cliRequest
 */
class cliRequest extends baseSet {

	/**
	 * Stores instance of cliRequest
	 *
	 * @var cliRequest
	 * @access private
	 * @static 
	 */
	private static $_Instance = false;
	
	/**
	 * Stores $_Switches
	 *
	 * @var array
	 * @access private
	 */
	private $_Switches;
	
	/**
	 * Stores $_Application
	 *
	 * @var cliApplication
	 * @access private
	 */
	private $_Application;
	
	
	
	/**
	 * Returns new cliRequest object
	 *
	 * @return cliRequest
	 */
	function __construct() {
		$this->_Switches = array();
		$this->_Application = null;
		$this->_resetSet();
		$this->parseArguments();
	}
	
	
	
	/**
	 * Returns instance of cliRequest
	 *
	 * @return cliRequest
	 * @static 
	 */
	static function getInstance() {
		if ( !self::$_Instance instanceof cliRequest ) {
			self::$_Instance = new cliRequest();
		}
		return self::$_Instance;
	}
	
	
	
	/**
	 * Requests additional information from a user on the CLI.
	 * 
	 * This should only be used in interactive sessions.
	 * $inParamName is the name of the item you need a value for.
	 * $inDefault is the default value if no response (i.e. line
	 * feed, enter hit) is given.
	 * $inMessage is a custom message, otherwise one will be
	 * built from the supplied data.
	 * $inValues is an array of permitted values for $inParamName.
	 * 
	 * @param string $inParamName 
	 * @param string $inDefault (optional)
	 * @param string $inMessage (optional)
	 * @param array $inValues (optional)
	 * @return cliRequest
	 */
	function getUserInput($inParamName, $inDefault = null, $inMessage = null, $inValues = null) {
		if ( $inMessage === null ) {
			$inMessage = "Please specify a value for $inParamName";
			if ( $inValues !== null ) {
				$inMessage .= ' ['.implode(',', $inValues).']';
			}
			if ( $inDefault !== null ) {
				$inMessage .= ' Default: '.$inDefault;
			}
			$inMessage .= ': ';
		} else {
			if ( strrpos($inMessage, ':') !== strlen($inMessage)-1 ) {
				$inMessage .= ': ';
			}
		}
		fwrite(STDOUT, $inMessage);
		
		$value = '';
		while ( strlen($value) == 0 ) {
			$value = trim(fgets(STDIN));
			if ( strlen($value) == 0 ) {
				if ( $inDefault !== null ) {
					$value = $inDefault;
				} else {
					fwrite(STDOUT, $inMessage);
				}
			}
			if ( $inValues !== null ) {
				if ( !in_array($value, $inValues) ) {
					fwrite(STDOUT, "Sorry, ($value) is not valid specify one of [".implode(', ', $inValues)."]: ");
					$value = '';
				}
			}
		}
		return $this->setParam($inParamName, $value);
	}
	
	/**
	 * Return array of params
	 *
	 * @return array
	 * @access public
	 */
	public function getParams() {
		return $this->_getItem();
	}
	
	/**
	 * Returns the param count
	 *
	 * @return integer
	 * @access public
	 */
	public function getCount() {
		return $this->_itemCount();
	}
	
	/**
	 * Parses arguments into an array
	 *
	 * @access protected
	 */
	protected function parseArguments() {
		reset($_SERVER['argv']);
		unset($_SERVER['argv'][0]);
		foreach ( $_SERVER['argv'] as $var ) {
			$var = trim($var);
			$arr = array();
			
			/*
			 * Handle parameters --param=value
			 */
			if ( preg_match("/^[-]{2}([^=]*)[=]?(.*)$/i", $var, $arr) ) {
				$key = $arr[1];
				$value = $arr[2];
				
				if ( !$value ) {
					$this->setParam($key, true); #it's a switch
				} else {
					$this->setParam($key, $value);
				}
			}
			/*
			 * Handle switches (-CvbgrDFGJ) 
			 */
			if ( preg_match("/^[-]([^-][^=]*)[=]?(.*)$/i", $var, $arr) ) {
				$key = $arr[1];
				$value = $arr[2];
				if (strlen($value) > 0) {
					exit("Please use a double dash for arguments with a value\n");
				}
					
				for ($k = 0; $k < strlen($key) ; $k++) {
					$this->setSwitches($key[$k]);
				}		
			}
			/*
			 * Handle simple statements (new thing value,  creates:  new => thing; thing => value)
			 */
			if ( stripos($var, '-') === false ) {
				$curKey = array_search($var, $_SERVER['argv']);
				if ( isset($_SERVER['argv'][$curKey+1]) && !preg_match('/^[-]{1,2}/', $_SERVER['argv'][$curKey+1]) ) {
					$this->setParam($var, $_SERVER['argv'][$curKey+1]);
				} else {
					$this->setParam($var, true);
				}
			}
		}
	}
	
	/**
	 * Return paramter value for $inParam
	 *
	 * @param string $inParam
	 * @return mixed
	 * @access public
	 */
	public function getParam($inParam) {
		return $this->_getItem($inParam);
	}
	
	/**
	 * Set param value for $inParam
	 *
	 * @param string $inParam
	 * @param mixed $inValue
	 * @return cliRequest
	 * @access public
	 */
	public function setParam($inParam, $inValue) {
		return $this->_setItem($inParam, $inValue);
	}

	/**
	 * Returns $_Switches
	 *
	 * @return array
	 */
	function getSwitches() {
		return $this->_Switches;
	}
	
	/**
	 * Returns the number of switches specified
	 *
	 * @return integer
	 */
	function getSwitchCount() {
		return count($this->_Switches);
	}
	
	/**
	 * Returns true if $inSwitch has been set
	 *
	 * @param string $inSwitch
	 * @return boolean
	 */
	function getSwitch($inSwitch) {
		if ( in_array($inSwitch, $this->_Switches) ) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Set $_Switches to $inSwitches
	 *
	 * @param string|array $inSwitches
	 * @return cliRequest
	 */
	function setSwitches($inSwitches) {
		if ( is_array($inSwitches) ) {
			$this->_Switches = $inSwitches;
		} else {
			if ( !in_array($inSwitches, $this->_Switches) ) {
				$this->_Switches[] = $inSwitches;
				$this->setModified();
			}
		}
		return $this;
	}

	/**
	 * Returns $_Application
	 *
	 * @return cliApplication
	 * @access public
	 */
	function getApplication() {
		return $this->_Application;
	}
	
	/**
	 * Set $_Application to $inApplication
	 *
	 * @param cliApplication $inApplication
	 * @return cliRequest
	 * @access public
	 */
	function setApplication(cliApplication $inApplication) {
		if ( $this->_Application !== $inApplication ) {
			$this->_Application = $inApplication;
			$this->setModified();
		}
		return $this;
	}
}