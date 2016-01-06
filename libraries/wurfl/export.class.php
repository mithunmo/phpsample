<?php
/**
 * wurflExport class
 * 
 * Stored in wurflExport.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage wurfl
 * @category wurflCapability
 * @version $Rev: 650 $
 */


/**
 * wurflExport
 * 
 * Creates a WURFL XML file from a wurflResultSet, optionally creating a patch file
 * 
 * @package scorpio
 * @subpackage wurfl
 * @category wurflExport
 */
class wurflExport {
	
	/**
	 * Stores $_Modified
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified;
	
	/**
	 * wurflResultSet object
	 *
	 * @var wurflResultSet
	 * @access protected
	 */
	protected $_ResultSet;
	
	/**
	 * Stores $_BuildPatchFile
	 * 
	 * @var boolean
	 * @access protected
	 */
	protected $_BuildPatchFile;
	
	/**
	 * Stores $_UseCustomValues
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_UseCustomValues;
	
	/**
	 * Stores $_Xml
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Xml;
	
	
	
	/**
	 * Creates a new instance of the wurflExport engine
	 *
	 * @param wurflResultSet $inResultSet
	 * @param boolean $inBuildPatch
	 * @param boolean $inUseCustomValues
	 * @return wurflExport
	 * @access private
	 */
	private function __construct($inResultSet = null, $inBuildPatch = false, $inUseCustomValues = false) {
		if ( $inResultSet !== null && $inResultSet instanceof wurflResultSet ) {
			$this->setResultSet($inResultSet);
		}
		if ( $inBuildPatch !== null ) {
			$this->setBuildPatchFile($inBuildPatch);
		}
		if ( $inUseCustomValues !== null ) {
			$this->setUseCustomValues($inUseCustomValues);
		}
		
		$this->_Xml = null;
	}
	
	
	
	/**
	 * Returns a new instance of wurflExport engine
	 *
	 * @param wurflResultSet $inResultSet
	 * @param boolean $inBuildPatch
	 * @param boolean $inUseCustomValues
	 * @return wurflExport
	 * @static 
	 */
	public static function getInstance($inResultSet, $inBuildPatch, $inUseCustomValues) {
		return new self($inResultSet, $inBuildPatch, $inUseCustomValues);
	}
	
	/**
	 * Returns a block of XML for the head of the WURFL file
	 *
	 * @return string
	 */
	public static function buildHeaderXml() {
		$xml  = '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
		$xml .= "<wurfl>\n";
		$xml .= "\t<devices>\n";
		return $xml;
	}
	
	/**
	 * Returns a block of XML for the head of the WURFL file
	 *
	 * @return string
	 */
	public static function buildPatchHeaderXml() {
		$xml  = '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
		$xml .= "<wurfl_patch>\n";
		$xml .= "\t<devices>\n";
		return $xml;
	}
	
	/**
	 * Builds the device entry header
	 *
	 * @param wurflDevice $oDevice
	 * @return string
	 */
	public static function buildDeviceOpenXml(wurflDevice $oDevice) {
		$deviceRoot = ($oDevice->getRootDevice()) ? ' actual_device_root="true"' : '';
		$xml = "\t\t<device user_agent=\"{$oDevice->getUserAgent()}\" fall_back=\"{$oDevice->getFallBackID()}\" id=\"{$oDevice->getWurflID()}\"$deviceRoot>\n";
		return $xml;
	}
	
	/**
	 * Returns a block of XML for the device capabilities
	 *
	 * @param wurflDeviceCapabilities $oCapabilities
	 * @param boolean $inUseCustomValues
	 * @return string
	 */
	public static function buildCapabilityXml(wurflDeviceCapabilities $oCapabilities, $inUseCustomValues) {
		$capabilityArray = array();
		if ( false ) $oCapability = new wurflDeviceCapability();
		foreach ( $oCapabilities as $capabilityName => $oCapability ) {
			systemLog::info("Processing capability: $capabilityName");
			
			$method = 'getWurflValue';
			if ( $inUseCustomValues == true ) {
				$method = 'getCustomValue';
			}
			
			if ( $oCapability->getCapability()->getVarType() == wurflCapability::VARTYPE_BOOLEAN ) {
				$capabilityArray[$oCapability->getCapability()->getCapabilityGroup()->getDescription()][$oCapability->getCapability()->getDescription()] = ($oCapability->$method() == 1 ? 'true' : 'false');
			} else {
				$capabilityArray[$oCapability->getCapability()->getCapabilityGroup()->getDescription()][$oCapability->getCapability()->getDescription()] = $oCapability->$method();
			}
		}
		
		$xml = '';
		if ( is_array($capabilityArray) && count($capabilityArray) >  0 ) {
			foreach ( $capabilityArray as $groupName => $capabilities ) {
				$xml .= "\t\t\t<group id=\"$groupName\">\n";
				foreach ( $capabilities as $capability => $value ) {
					$xml .= "\t\t\t\t<capability name=\"$capability\" value=\"$value\"/>\n";
				}
				$xml .= "\t\t\t</group>\n";
			}
		}
		return $xml;
	}
	
	/**
	 * Returns the closing tag for a device
	 *
	 * @return string
	 */
	public static function buildDeviceCloseXml() {
		return "\t\t</device>\n";
	}
	
	/**
	 * Returns the footer XML block
	 *
	 * @return string
	 */
	public static function buildFooterXML() {
		return "\t</devices>\n</wurfl>";
	}
	
	/**
	 * Returns the footer XML block
	 *
	 * @return string
	 */
	public static function buildPatchFooterXML() {
		return "\t</devices>\n</wurfl_patch>";
	}
	
	
	
	/**
	 * Exports selected device data to wurfl XML
	 *
	 * @return wurflExport
	 * @throws wurflException
	 */
	function export() {
		if ( $this->_ResultSet instanceof wurflResultSet && $this->_ResultSet->getResultCount() > 0 ) {
			if ( false ) $oDevice = new wurflDevice();
			systemLog::message('Generating XML data set');
			if ( $this->getBuildPatchFile() ) {
				$xml  = self::buildPatchHeaderXml();
			} else {
				$xml  = self::buildHeaderXml();
			}
			$oldSource = systemLog::getInstance()->getSource();
			
			/*
			 * Loop over selected devices
			 */
			foreach ( $this->_ResultSet as $oDevice ) {
				try {
					systemLog::getInstance()->setSource(systemLogSource::getInstance(array(systemLogSource::DESC_WURFL_ID => $oDevice->getWurflID())));
					systemLog::message('Exporting deviceID: '.$oDevice->getWurflID());
					
					$oDevice->setDevicePath(array($oDevice->getDeviceID()));
					systemLog::info('Fetching device capabilities');
					$oCapabilities = $oDevice->getCapabilities();
					
					$xml .= self::buildDeviceOpenXml($oDevice);
					$xml .= self::buildCapabilityXml($oCapabilities, $this->getUseCustomValues());
					$xml .= self::buildDeviceCloseXml();
					
					systemLog::message('----------------------------------------------------------------');
				} catch ( Exception $e ) {
					throw $e;
				}
			}
			
			if ( $this->getBuildPatchFile() ) {
				$xml .= self::buildPatchFooterXML();
			} else {
				$xml .= self::buildFooterXML();
			}
			
			systemLog::getInstance()->setSource($oldSource);
			
			$this->setXml($xml);
			return $this;
		} else {
			throw new wurflException('No wurflResultSet to parse');
		}
	}
	
	
	
	/**
	 * Set $_Modified to $inStatus
	 *
	 * @param boolean $inStatus
	 * @return wurflExport
	 */
	function setModified($inStatus = true) {
		if ( $this->_Modified !== $inStatus ) {
			$this->_Modified = $inStatus;
		}
		return $this;
	}
	
	/**
	 * Returns $_ResultSet
	 *
	 * @return wurflResultSet
	 */
	function getResultSet() {
		return $this->_ResultSet;
	}
	
	/**
	 * Set $_ResultSet to $inResultSet
	 *
	 * @param wurflResultSet $inResultSet
	 * @return wurflExport
	 */
	function setResultSet($inResultSet) {
		if ( $inResultSet !== $this->_ResultSet ) {
			$this->_ResultSet = $inResultSet;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return BuildPatchFile
	 * 
	 * @return boolean
	 */
	function getBuildPatchFile() {
		return $this->_BuildPatchFile;
	}
	
	/**
	 * Set $_BuildPatchFile to $inBuildPatchFile
	 * 
	 * @param boolean $inBuildPatchFile
	 * @return wurflExport
	 */
	function setBuildPatchFile($inBuildPatchFile) {
		if ( $inBuildPatchFile !== $this->_BuildPatchFile ) {
			$this->_BuildPatchFile = $inBuildPatchFile;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_UseCustomValues
	 *
	 * @return boolean
	 */
	function getUseCustomValues() {
		return $this->_UseCustomValues;
	}
	
	/**
	 * Set $_UseCustomValues to $inUseCustomValues
	 *
	 * @param boolean $inUseCustomValues
	 * @return wurflExport
	 */
	function setUseCustomValues($inUseCustomValues) {
		if ( $inUseCustomValues !== $this->_UseCustomValues ) {
			$this->_UseCustomValues = $inUseCustomValues;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_Xml
	 *
	 * @return string
	 */
	function getXml() {
		return $this->_Xml;
	}
	
	/**
	 * Set $_Xml to $inXml
	 *
	 * @param string $inXml
	 * @return wurflExport
	 */
	function setXml($inXml) {
		if ( $inXml !== $this->_Xml ) {
			$this->_Xml = $inXml;
			$this->setModified();
		}
		return $this;
	}
}