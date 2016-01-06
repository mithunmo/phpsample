<?php
/**
 * systemConfigBase.class.php
 * 
 * System config class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemConfigBase
 * @version $Rev: 670 $
 */


/**
 * systemConfigBase
 * 
 * systemConfigBase class contains the basic methods for reading, writing and modifying a
 * configuration file. Scorpio config files are very simple XML files with the following
 * structure:
 * 
 * <code>
 * <config>
 *     <section name="name">
 *         <param name="paramName" value="paramValue" />
 *     </section>
 * </config>
 * </code>
 * 
 * Additionally, the config system supports the concept of protected values. By specifying
 * override="" a parameter can be locked from being overwritten. This applies to the section
 * as well. Override takes a boolean value which is one of: true, yes, 1 or false, no, 0.
 * 
 * Sections can contain multiple parameters and are used to group similar config params
 * together. While further levels could be supported, this system only allows for a single
 * section -> param setup to keep the files straight forward.
 * 
 * Example usage:
 * <code>
 * // load route config.xml file (system config)
 * $oConfig = new systemConfigBase();
 * $oConfig->load();
 * 
 * // load a custom config file
 * $oConfig = new systemConfigBase();
 * $oConfig->load('/path/to/my/config.xml');
 * 
 * // access a param
 * $oConfig->getParam('section','param', false);
 * </code>
 * 
 * When fetching params, a systemConfigParam object is returned. It is important to understand
 * that this is a full object, and that to get the actual value it is necessary to either
 * cast as a string, use in a string context or to call the method ->getParamValue().
 * 
 * You can freely extend this class into a specific configuration object with dedicated methods
 * that return the values if you wish. This is done in {@link systemConfig}.
 * 
 * When calling getParam(), the third parameter is a default value that should be returned should
 * a value not be found. This defaults to false (the boolean false, not a string containing false).
 * If you wish another value, simply specify it when calling getParam().
 * 
 * Note:
 * When loading files, should a param attempt to overwrite an existing param that is marked as
 * not overridable, an exception will be thrown. You can either catch the exception, or allow it
 * to bubble. The default is to allow it to bubble as this (could be) an attempt to change
 * core system parameters. The raised exception will give the name of the parameter AND the value
 * that was going to be written - it does not give the existing value. Either way: it is up to
 * the developer to decide how to handle this.
 * 
 * @package scorpio
 * @subpackage system
 * @category systemConfigBase
 */
class systemConfigBase extends baseSet {
	
	/**
	 * Name of the root config file that must be located in libraries/base
	 *
	 * @var string
	 */
	const ROOT_CONFIG_FILE				= 'config.xml';
	
	/**
	 * Stores $_LastConfigFile
	 *
	 * @var string
	 * @access protected
	 */
	protected $_LastConfigFile			= '';
	
	
	
	/**
	 * Returns new systemConfigBase
	 *
	 * @return systemConfigBase
	 */
	function __construct() {
		$this->reset();
		$this->setBasePath();
	}
	
	/**
	 * Resets the object
	 *
	 * @return void
	 */
	function reset() {
		$this->_LastConfigFile = '';
		parent::_resetSet();
	}
	
	
	
	/**
	 * Reads an XML config file into the systemConfigBase class, if no path attempts to load from the current location
	 *
	 * The XML should be structured as:
	 * <config>
	 *     <section name="" override="true|false">
	 *         <param name="" value="" override="true|false" />
	 *     </section>
	 * </config>
	 * 
	 * @param string $filename
	 * @throws systemConfigFileNotReadable
	 * @throws systemConfigFileNotValidXml
	 * @throws systemConfigParamCannotBeOverridden
	 */
	function load($inFilename = false) {
		if ( !$inFilename || strlen($inFilename) < 1 ) {
			$basePath = $this->getBasePath().system::getDirSeparator();
			/*
			 * Check for master config file in either libraries or data/config
			 */
			$inFilename = $basePath.'libraries'.system::getDirSeparator().self::ROOT_CONFIG_FILE;
			if ( !@file_exists($inFilename) || !@is_readable($inFilename) ) {
				$inFilename = $basePath.'data'.system::getDirSeparator().'config'.system::getDirSeparator().self::ROOT_CONFIG_FILE;
				if ( !@file_exists($inFilename) || !@is_readable($inFilename) ) {
					return false;
				}
			}
		}
		$this->setLastConfigFile($inFilename);
		
		if ( !@file_exists($inFilename) || !@is_readable($inFilename) ) {
			throw new systemConfigFileNotReadable($inFilename);
		}
		
		$oXML = @simplexml_load_file($inFilename);
		if ( !$oXML ) {
			throw new systemConfigFileNotValidXml($inFilename);
		}
		
		try {
			foreach ( $oXML as $node ) {
				$oSection = new systemConfigSection(
					(string) $node['name'],
					true,
					$this->_convertToBoolean((string) $node['override'])
				);
				
				foreach ( $node as $param ) {
					$oSection->getParamSet()->addParam(
						new systemConfigParam(
							(string) $param['name'],
							$this->_convertToBoolean((string) $param['value']),
							$this->_convertToBoolean((string) $param['override'])
						)
					);
				}
				$this->addSection($oSection);
			}
		} catch ( systemConfigParamCannotBeOverridden $e ) {
			throw $e;
		}
	}
	
	/**
	 * Attempts to save the current config to the last read file location
	 *
	 * @return boolean
	 * @throws systemConfigFileCannotBeWritten
	 */
	function save() {
		if ( $this->getModified() ) {
			if ( !@file_exists($this->getLastConfigFile()) && !@is_writable(dirname($this->getLastConfigFile()))  ) {
				throw new systemConfigFileCannotBeWritten(dirname($this->getLastConfigFile()));
			}
			if ( @file_exists($this->getLastConfigFile()) && !@is_writable($this->getLastConfigFile()) ) {
				throw new systemConfigFileCannotBeWritten($this->getLastConfigFile());
			}
			
			$oDom = new DOMDocument('1.0', 'UTF-8');
			$oDom->formatOutput = true;
			$oDom->preserveWhiteSpace = true;
			$oDomConfig = $oDom->createElement('config');
			
			foreach ( $this as $oSection ) {
				if ( $oSection->getCanBeOverridden() || stripos($this->getLastConfigFile(), self::ROOT_CONFIG_FILE) !== false ) {
					$oDomSection = $oDom->createElement('section');
					$oDomSection->setAttribute('name', $oSection->getParamName());
					$oDomSection->setAttribute('override', (int) $oSection->getCanBeOverridden());
					
					foreach ( $oSection->getParamSet() as $oParam ) {
						switch ( true ) {
							case $oSection->getParamName() == 'paths' && $oParam->getParamName() == 'base':
							case $oSection->getParamName() == 'site' && $oParam->getParamName() == 'path':
								continue;
							break;
							
							default:
								$oDomOption = $oDom->createElement('option');
								$oDomOption->setAttribute('name', $oParam->getParamName());
								$oDomOption->setAttribute('value', $oParam->getParamValue());
								$oDomOption->setAttribute('override', (int) $oParam->getCanBeOverridden());
								$oDomSection->appendChild($oDomOption);
						}
					}
					if ( $oDomSection->hasChildNodes() ) {
						$oDomConfig->appendChild($oDomSection);
					}
				}
			}
			$oDom->appendChild($oDomConfig);
			
			$bytes = @file_put_contents($this->getLastConfigFile(), $oDom->saveXML(), OVERWRITE|LOCK_EX);
			if ( $bytes > 0 ) {
				return true;
			}
		}
		return false;
	}
	
	
	
	/**
 	 * Adds the section to the set but only if it can be overridden
 	 *
 	 * @param systemConfigSection $oSection
 	 */
 	function addSection(systemConfigSection $oSection) {
 		$key = $oSection->getParamName();
 		if ( $this->_itemKeyExists($key) ) {
 			if ( $this->_getItem($key)->getCanBeOverridden() ) {
 				if ( false ) $oParam = new systemConfigParam();
 				foreach ( $oSection->getParamSet() as $oParam ) {
 					$this->_getItem($key)->getParamSet()->addParam($oParam);
 				}
 				return $this;
 			} else {
 				throw new systemConfigParamCannotBeOverridden($oSection);
 			}
 		} else {
 			return $this->_setItem($key, $oSection);
 		}
 	}
 	
 	/**
 	 * Returns the section with name $sectionName; if it does not exist, it is created
 	 *
 	 * @param string $sectionName
 	 * @return systemConfigSection
 	 */
 	function getSection($inSectionName) {
 		if ( $inSectionName ) {
 			if ( $this->_itemKeyExists($inSectionName) ) {
	 			return $this->_getItem($inSectionName);
 			}
			$oSection = new systemConfigSection($inSectionName, true, true);
			$oSection->setModified();
			$this->addSection($oSection);
			return $oSection;
 		}
 		return false;
 	}
	
 	/**
 	 * Returns systemConfigParam, if $inDefault set, param will inherit this value
 	 *
 	 * @param string $inSectionName
 	 * @param string $inParamName
 	 * @param mixed $inDefault
 	 * @return systemConfigParam
 	 */
 	function getParam($inSectionName, $inParamName, $inDefault = false) {
 		return $this->getSection($inSectionName)->getParamSet()->getParam($inParamName, $inDefault);
 	}
 	
 	
	
	/**
	 * Sets the base path relative to the config file
	 *
	 * @return void
	 */
	private function setBasePath() {
		$this->getSection('paths')->getParamSet()->addParam(
			new systemConfigParam(
				'base',
				utilityStringFunction::cleanDirSlashes(dirname(dirname(dirname(dirname(__FILE__))))),
				true
			)
		);
	}
	
	/**
	 * Returns the current base path
	 *
	 * @return systemConfigParam
	 */
	function getBasePath() {
		return $this->getParam('paths','base');
	}
	
	/**
	 * Returns ConfigParams
	 *
	 * @return array
	 */
	function getConfigParams() {
		return $this->_getItem();
	}
	
	/**
	 * Set ConfigParams property
	 *
	 * @param array $inConfigParams
	 * @return systemConfigBase
	 */
	function setConfigParams($inConfigParams) {
		return $this->_setItem($inConfigParams);
	}
	
	/**
	 * Returns LastConfigFile
	 *
	 * @return string
	 */
	function getLastConfigFile() {
		return $this->_LastConfigFile;
	}
	
	/**
	 * Set LastConfigFile property
	 *
	 * @param string $LastConfigFile
	 * @return systemConfigBase
	 */
	function setLastConfigFile($inString) {
		if ( $inString !== $this->_LastConfigFile ) {
			$this->_LastConfigFile = $inString;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns true if set has been modified
	 *
	 * @return boolean
	 */
	function getModified() {
		$modified = $this->_Modified;
		if ( $this->_itemCount() > 0 ) {
			foreach ( $this as $oSection ) {
				$modified = $modified || $oSection->getModified();
			}
		}
		return $modified;
	}
	
	/**
	 * Converts $inValue to boolean true or false if the string can be interrpreted as a boolean value
	 *
	 * @param string $inValue
	 * @return boolean|string Returns the original unmodified value if not boolean
	 * @access private
	 */
	private function _convertToBoolean($inValue) {
		if ( in_array(strtolower((string) $inValue), array('1','true','yes')) ) {
			return true;
		}
		if ( in_array(strtolower((string) $inValue), array('0','false','no')) ) {
			return false;
		}
		return $inValue;
	}
}