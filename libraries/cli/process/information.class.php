<?php
/**
 * cliProcessInformation Class
 * 
 * Stored in information.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliProcessInformation
 * @version $Rev: 754 $
 */


/**
 * cliProcessInformation Class
 * 
 * Parses a daemon status file into parameters allowing them to be displayed
 * to users or used by other processes.
 * 
 * Example usage:
 * <code>
 * $oProcInfo = new cliProcessInformation('loggingd');
 * </code>
 * 
 * @package scorpio
 * @subpackage cli
 * @category cliProcessInformation
 * @static 
 */
class cliProcessInformation extends baseSet {
	
	const PROP_STATUS = 'Status';
	const PROP_NAME = 'Daemon';
	const PROP_LAST_UPDATED = 'LastUpdated';
	
	/**
	 * Stores $_ProcessName
	 *
	 * @var string
	 * @access protected
	 */
	protected $_ProcessName;
	
	/**
	 * Stores $_ProcessStatusFile
	 *
	 * @var string
	 * @access protected
	 */
	protected $_ProcessStatusFile;
	
	/**
	 * Stores $_ProcessPidFile
	 *
	 * @var string
	 * @access protected
	 */
	protected $_ProcessPidFile;
	
	/**
	 * Stores $_ProcessID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_ProcessID;
	
	
	
	/**
	 * Creates a new process information object
	 * 
	 * $inProcessName is the filename of the daemon process to find information for.
	 * This should exclude any extension. e.g. to fetch information for the loggingd
	 * daemon process:
	 * 
	 * <code>
	 * $oProcInfo = new cliProcessInformation('loggingd');
	 * </code>
	 * 
	 * @return cliProcessInformation
	 */
	function __construct($inProcessName = null) {
		$this->reset();
		if ( $inProcessName !== null ) {
			$this->setProcessName($inProcessName);
			$this->load();
		}
	}
	
	
	
	/**
	 * Returns an array of cliProcessInformation objects
	 * 
	 * Objects are returned for each daemon listed in the daemons folder.
	 * 
	 * @return array
	 * @static
	 */
	static function getDaemonInformation() {
		$return = array();
		$daemons = fileObject::parseDir(system::getConfig()->getPathDaemons(), false);
		if ( count($daemons) > 0 ) {
			foreach ( $daemons as $oFile ) {
				if ( strpos($oFile->getFilename(), '.') !== 0 ) {
					$oObject = new cliProcessInformation(basename($oFile->getFilename(), '.'.$oFile->getExtension()));
					$return[] = $oObject;
				}
			}
		}
		return $return;
	}
	
	
	
	/**
	 * Loads process information
	 * 
	 * @return boolean
	 */
	function load() {
		if ( $this->getProcessName() ) {
			$this->setProcessStatusFile(system::getConfig()->getPathTemp().'/status/'.$this->getProcessName().'.log');
			$this->setProcessPidFile(system::getConfig()->getPathTemp().'/'.$this->getProcessName().'.pid');
			
			if ( @file_exists($this->getProcessPidFile()) ) {
				$this->setProcessID(file_get_contents($this->getProcessPidFile()));
			}
			if ( @file_exists($this->getProcessStatusFile()) ) {
				$contents = file_get_contents($this->getProcessStatusFile());
				if ( strlen($contents) > 0 ) {
					$contents = explode("\n", $contents);
					foreach ( $contents as $line ) {
						if ( strlen(trim($line)) > 0 ) {
							list($property, $value) = explode(":", trim($line), 2);
							$this->setProperty(trim($property), trim($value));
						}
					}
				} else {
					$this->setProperty('Status', 'Unknown');
				}
			} else {
				$this->setProperty('Status', 'Unknown');
			}
			return true;
		}
		return false;
	}

	/**
	 * Resets the object
	 * 
	 * @return void
	 */
	function reset() {
		$this->_ProcessName = null;
		$this->_ProcessStatusFile = null;
		$this->_ProcessPidFile = null;
		$this->_ProcessID = null;
		parent::_resetSet();
	}
	
	
	
	/**
	 * Returns $_ProcessName
	 *
	 * @return string
	 */
	function getProcessName() {
		return $this->_ProcessName;
	}
	
	/**
	 * Set $_ProcessName to $inProcessName
	 *
	 * @param string $inProcessName
	 * @return cliProcessInformation
	 */
	function setProcessName($inProcessName) {
		if ( $inProcessName !== $this->_ProcessName ) {
			$this->_ProcessName = $inProcessName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_ProcessStatusFile
	 *
	 * @return string
	 */
	function getProcessStatusFile() {
		return $this->_ProcessStatusFile;
	}
	
	/**
	 * Set $_ProcessStatusFile to $inProcessStatusFile
	 *
	 * @param string $inProcessStatusFile
	 * @return cliProcessInformation
	 */
	function setProcessStatusFile($inProcessStatusFile) {
		if ( $inProcessStatusFile !== $this->_ProcessStatusFile ) {
			$this->_ProcessStatusFile = $inProcessStatusFile;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_ProcessPidFile
	 *
	 * @return string
	 */
	function getProcessPidFile() {
		return $this->_ProcessPidFile;
	}
	
	/**
	 * Set $_ProcessPidFile to $inProcessPidFile
	 *
	 * @param string $inProcessPidFile
	 * @return cliProcessInformation
	 */
	function setProcessPidFile($inProcessPidFile) {
		if ( $inProcessPidFile !== $this->_ProcessPidFile ) {
			$this->_ProcessPidFile = $inProcessPidFile;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_ProcessID
	 *
	 * @return integer
	 */
	function getProcessID() {
		return $this->_ProcessID;
	}
	
	/**
	 * Set $_ProcessID to $inProcessID
	 *
	 * @param integer $inProcessID
	 * @return cliProcessInformation
	 */
	function setProcessID($inProcessID) {
		if ( $inProcessID !== $this->_ProcessID ) {
			$this->_ProcessID = $inProcessID;
			$this->setModified();
		}
		return $this;
	}
	
	
	
	/**
	 * Returns the property value for $inProperty
	 * 
	 * @param string $inProperty
	 * @return mixed
	 */
	function getProperty($inProperty) {
		return $this->_getItem($inProperty);
	}
	
	/**
	 * Sets the property with $inValue
	 * 
	 * @param string $inProperty
	 * @param mixed $inValue
	 * @return cliProcessInformation
	 */
	function setProperty($inProperty, $inValue) {
		return $this->_setItem($inProperty, $inValue);
	}
	
	/**
	 * Removes the property
	 * 
	 * @param string $inProperty
	 * @return cliProcessInformation
	 */
	function removeProperty($inProperty) {
		return $this->_removeItem($inProperty);
	}
}