<?php
/**
 * wurflParser
 * 
 * Stored in wurflParser.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage wurfl
 * @category wurflParser
 * @version $Rev: 650 $
 */


/**
 * wurflParser Class
 * 
 * Parses a WURFL XML file; either the whole file or a patch file. So long as the file
 * validates as XML and conforms to the WURFL specification it can be parsed by this 
 * class into the necessary objects and committed to the database.
 * 
 * As this is an intensive process it is recommended to run the parser offline on the
 * command line or as a detached process.
 * 
 * <code>
 * $oParser = new wurflParser("/path/to/wurfl.xml");
 * $oParser->process();
 * </code>
 * 
 * To make the database data more useful, a rebuild option is included. This will assign
 * the model name and manufacturer reference to the device record. This then allows for
 * devices to be selected by manufacturer and displayed.
 * 
 * @package scorpio
 * @subpackage wurfl
 * @category wurflParser
 */
class wurflParser {
	
	/**
	 * Stores $_Resource
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Resource			= '';
	
	/**
	 * Stores $_Xml
	 *
	 * @var simpleXml object
	 * @access protected
	 */
	protected $_Xml			= false;
	
	/**
	 * Stores $_RebuildDeviceData
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_RebuildDeviceData			= false;
	/**
	 * Stores $_TreatAsCustomData
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_TreatAsCustomData			= false;
	/**
	 * Stores $_ProcessCount
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_ProcessCount;
	
	
	
	/**
	 * Returns a new wurflParser object, if $inRebuildDeviceData is true, device data is rebuilt
	 * automatically after parsing. If $inTreatAsCustomData is true, the resource is treated as
	 * containing custom data that will override the WURFL data (stored separately).
	 *
	 * @param string $inResource
	 * @param boolean $inRebuildDeviceData
	 * @param boolean $inTreatAsCustomData
	 * @return wurflParser
	 */
	function __construct($inResource = false, $inRebuildDeviceData = false, $inTreatAsCustomData = false) {
		if ( $inResource ) {
			$this->setResource($inResource);
		}
		$this->_ProcessCount = 0;
		$this->setRebuildDeviceData($inRebuildDeviceData);
		$this->setTreatAsCustomData($inTreatAsCustomData);
		$this->readResource();
	}
	
	
	
	/**
	 * Returns xml
	 *
	 * @return simpleXml object
	 */
	function getXml() {
		return $this->_Xml;
	}
	
	/**
	 * Set xml property
	 *
	 * @param simpleXml object $xml
	 * @return wurflParser
	 */
	function setXml($xml) {
		if ( $xml !== $this->_Xml ) {
			$this->_Xml = $xml;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns Resource
	 *
	 * @return string
	 */
	function getResource() {
		return $this->_Resource;
	}
	
	/**
	 * Set Resource property
	 *
	 * @param string $inResource
	 * @return wurflParser
	 */
	function setResource($inResource) {
		if ( $inResource !== $this->_Resource ) {
			$this->_Resource = $inResource;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Attempts to read the loaded resource which can be either a string of XML or a file location
	 *
	 * @return void
	 * @throws wurflException
	 */
	function readResource() {
		if ( $this->_Resource ) {
			if ( stripos($this->_Resource, '<xml') !== false || stripos($this->_Resource, '<wurfl') !== false ) {
				$this->_Xml = @simplexml_load_string($this->_Resource);
			} elseif ( @file_exists($this->_Resource) && @is_readable($this->_Resource) ) {
				$this->_Xml = @simplexml_load_file($this->_Resource);
			} else {
				throw new wurflException('Cannot read resource '.$this->_Resource.'. Resource must be either a valid file or an XML string');
			}
		}
	}
	
	/**
	 * Returns RebuildDeviceData
	 *
	 * @return boolean
	 */
	function getRebuildDeviceData() {
		return $this->_RebuildDeviceData;
	}
	
	/**
	 * Set RebuildDeviceData property
	 *
	 * @param boolean $inRebuildDeviceData
	 * @return wurflParser
	 */
	function setRebuildDeviceData($inRebuildDeviceData) {
		if ( $inRebuildDeviceData !== $this->_RebuildDeviceData ) {
			$this->_RebuildDeviceData = $inRebuildDeviceData;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns TreatAsCustomData
	 *
	 * @return boolean
	 */
	function getTreatAsCustomData() {
		return $this->_TreatAsCustomData;
	}
	
	/**
	 * Set TreatAsCustomData property
	 *
	 * @param boolean $inTreatAsCustomData
	 * @return wurflParser
	 */
	function setTreatAsCustomData($inTreatAsCustomData) {
		if ( $inTreatAsCustomData !== $this->_TreatAsCustomData ) {
			$this->_TreatAsCustomData = $inTreatAsCustomData;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns $_ProcessCount
	 *
	 * @return integer
	 */
	function getProcessCount() {
		return $this->_ProcessCount;
	}
	
	/**
	 * Set $_ProcessCount to $inProcessCount
	 *
	 * @param integer $inProcessCount
	 * @return wurflParser
	 */
	function setProcessCount($inProcessCount) {
		if ( $inProcessCount !== $this->_ProcessCount ) {
			$this->_ProcessCount = $inProcessCount;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Casts a variable to an alternative type starting with string.
	 * Used during parsing as many variables are objects and need to be an actual variable
	 * 
	 * @param mixed var
	 * @return mixed
	 * @access protected
	 */
	protected function convertVarType($var) {
		$var = (string) $var;
		if ( is_numeric($var) ) {
			$var = (int) $var;
		} elseif ( strtolower($var) == 'true' || strtolower($var) == 'false' ) {
			$var = (strtolower($var)=='true') ? true : false;
		}
		return $var;
	}
	
	
	
	/**
	 * Processes the WURFL data into the database
	 *
	 * @return boolean
	 */
	function process() {
		if ( is_object($this->_Xml) ) {
			$procCount = 0;
			$oldSource = systemLog::getInstance()->getSource();
			
			/*
			 * get top level of devices tree
			 */
			foreach ( $this->_Xml as $rootElement => $rootData ) {
				/*
				 * Parse the devices
				 */
				if ( $rootElement == 'devices' ) {
					$deviceCnt = count($rootData);
					for ( $i=0; $i<$deviceCnt; $i++ ) {
						$tag = $rootData->device[$i];
						$wurfl_id		= trim((string) $tag['id']);
						$user_agent		= trim((string) $tag['user_agent']);
						$device_root	= $this->convertVarType(trim($tag['actual_device_root']));
						$fall_back		= trim((string) $tag['fall_back']);
						systemLog::getInstance()->setSource('import]['.$wurfl_id);
						systemLog::message("Importing data for $wurfl_id");
						if ( $wurfl_id == 'generic' ) {
							$user_agent = 'generic';
						}
						
						/*
						 * Check userAgent and see if we have a modelID already
						 */
						systemLog::notice("Searching for existing device with ID: $wurfl_id");
						$oDevice = wurflManager::getInstanceByWurflID($wurfl_id);
						if ( $oDevice->getDeviceID() ) {
							systemLog::notice("Found existing device for ID: {$oDevice->getDeviceID()}");
						} else {
							$oDevice->setCreateDate(date(system::getConfig()->getDatabaseDatetimeFormat()));
						}
						$oDevice->setUserAgent($user_agent);
						$oDevice->setWurflID($wurfl_id);
						$oDevice->setFallBackID($fall_back);
						$oDevice->setRootDevice($device_root);
						$oDevice->setUpdateDate(date(system::getConfig()->getDatabaseDatetimeFormat()));
						
						/*
						 * Get the groups and build capabilities array
						 */
						systemLog::notice('Processing capabilities');
						
						for ( $j=0; $j<count($tag); $j++ ) {
							$group = $tag->group[$j];
							
							/**
							 * @var deviceCapabilityGroup
							 */
							$oGroup = wurflCapabilityGroup::getInstance((string) $group['id']);
							$oGroup->setDescription((string) $group['id']);
							$oGroup->setDisplayName(ucwords(str_replace('_', ' ', $oGroup->getDescription())));
							$oGroup->save();
							
							for ( $k=0; $k<count($group); $k++ ) {
								$capability = $group->capability[$k];
								/*
								 * Get our capabilities, store them if not already in the DB, and assign to model
								 */
								
								/**
								 * @var deviceCapability
								 */
								$oCapability = new wurflCapability();
								$oCapability->setCapabilityGroupID($oGroup->getCapabilityGroupID());
								$oCapability->setDescription(trim((string) $capability['name']));
								if ( !$oCapability->load() ) {
									$oCapability->setCapabilityGroupID($oGroup->getCapabilityGroupID());
									$oCapability->setDescription(trim((string) $capability['name']));
								}
								$oCapability->save();
								
								/*
								 * Add capability and value to mapper
								 */
								systemLog::info("Adding capability to device {$oCapability->getDescription()}(".$this->convertVarType(trim($capability['value'])).")");
								if ( $this->_TreatAsCustomData === true ) {
									$oDevice->getCapabilities()->setCustomCapability($oCapability->getDescription(), $this->convertVarType(trim($capability['value'])));
								} else {
									$oDevice->getCapabilities()->setCapability($oCapability->getDescription(), $this->convertVarType(trim($capability['value'])));
								}
								
								/*
								 * For brand_name and model_name, parse out and add references / update records
								 */
								if ( $oCapability->getDescription()  == 'brand_name'  && strlen($this->convertVarType(trim($capability['value']))) > 1 ) {
									$oManfacturer = wurflManufacturer::getInstance($this->convertVarType(trim($capability['value'])));
									$oManfacturer->setDescription($this->convertVarType(trim($capability['value'])));
									$oManfacturer->save();
									
									if ( $oManfacturer->getManufacturerID() ) {
										$oDevice->setManufacturerID($oManfacturer->getManufacturerID());
									}
								}
								if ( $oCapability->getDescription() == 'model_name' ) {
									$oDevice->setModelName($this->convertVarType(trim($capability['value'])));
								}
							} // end foreach group;
						} // end foreach tag
							
						$oDevice->save();
						$procCount++;
						unset($oDevice);
						systemLog::notice("Finished processing device\n---------------------------------------------------------------------");
					} // end foreach rootData;
				} // end if == devices;
			} // end foreach rootElement;
			systemLog::getInstance()->setSource($oldSource);
			$this->setProcessCount($procCount);
		} else {
			systemLog::error("Error! Internal SimpleXML object not ready for parsing!");
		}
	}
	
	/**
	 * Rebuilds the device data attaching missing data to the root devices
	 *
	 * @return boolean
	 */
	function rebuildDeviceData() {
		systemLog::notice('Starting rebuild of device data');
		$oStmt = dbManager::getInstance()->prepare('SELECT deviceID FROM '.system::getConfig()->getDatabase('wurfl').'.devices WHERE rootDevice = 1');
		if ( $oStmt->execute() ) {
			foreach ( $oStmt as $row ) {
				systemLog::getInstance()->setSource('rebuild]['.$row['deviceID']);
				$oDevice = wurflManager::getInstance($row['deviceID']);
				systemLog::notice('Processing deviceID '.$row['deviceID']);
				$oDevice->setModelName($oDevice->getCapabilities()->getCapability('model_name'));
				
				$oMan = wurflManufacturer::getInstance($oDevice->getCapabilities()->getCapability('brand_name'));
				if ( $oMan->getManufacturerID() > 0 ) {
					$oDevice->setManufacturerID($oMan->getManufacturerID());
				}
				$oDevice->save();
			}
		}
	}
}