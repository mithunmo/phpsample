<?php
/**
 * mvcControllerMapper.class.php
 * 
 * mvcControllerMapper class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcControllerMapper
 * @version $Rev: 707 $
 */


/**
 * mvcControllerMapper class
 * 
 * Reads and writes the main site controllerMap.xml file. Internally this is held as a
 * SimpleXML object tree allowing xpath queries to be run against it.
 * 
 * This class is used to map a request to a controller; finding the first most specific
 * match to the request. If no entry is defined for the actual request, the closest match
 * is returned. The match is returned as an mvcControllerMap object.
 * 
 * The _ControllerMap property holds the full map, whereas the mvcControllerMap object
 * contains only the request portion and the path to that request.
 * 
 * As parsing over SimpleXML objects can be awkward when they have been wrapper by
 * utilityOutputWrapper, an additional method is available: getMapAsControllers(). This
 * returns an array containing all the top level controllers and can be safely iterated.
 * The benefit of this approach is that each controller is now a proper mvcControllerMap
 * object and so will wrap all sub-controllers.
 * 
 * <code>
 * // example loading a config file and mapping to a "string" controller
 * $oMapper = mvcControllerMapper::getInstance('/path/to/controllerMap.xml');
 * $oMap = $oMapper->getController('/the/request/string');
 * 
 * $oMap->getName();
 * $oMap->getDescription();
 * $oMap->getUriPath();
 * $oMap->getFilePath();
 * if ( $oMap->hasSubControllers() ) {
 *     foreach ( $oMap->getSubControllers as $oSubMap ) {
 *          $oSubMap->getName();
 *     }
 * }
 * </code>
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcControllerMapper
 */
class mvcControllerMapper {

	/**
	 * Holds the instance of mvcControllerMapper
	 *
	 * @var mvcControllerMapper
	 */
	private static $_Instance = false;
	
	/**
	 * Stores $_LoadedConfigFile
	 *
	 * @var string
	 * @access private
	 */
	private $_LoadedConfigFile;
	
	/**
	 * Stores $_ControllerMap
	 *
	 * @var SimpleXMLElement
	 * @access private
	 */
	private $_ControllerMap;
	
	/**
	 * Stores $_Modified
	 *
	 * @var boolean
	 * @access private
	 */
	private $_Modified;
	
	
	
	/**
	 * Creates new mvcControllerMapper
	 *
	 * @return mvcControllerMapper
	 */
	function __construct() {
		$this->_LoadedConfigFile = null;
		$this->_ControllerMap = null;
		$this->_Modified = false;
	}
	
	
	
	/**
	 * Returns the instance of the mvcControllerMapper
	 *
	 * @param string $inConfigFile
	 * @return mvcControllerMapper
	 * @static 
	 */
	static function getInstance($inConfigFile = null) {
		if ( self::$_Instance instanceof mvcControllerMapper ) {
			return self::$_Instance;
		}
		
		self::$_Instance = new mvcControllerMapper();
		if ( $inConfigFile !== null ) {
			self::$_Instance->load($inConfigFile);
		}
		return self::$_Instance;
	}
	
	
	
	/**
	 * Reads the supplied config file and loads the data into the map
	 * 
	 * @param string $inConfigFile
	 * @return boolean
	 * @throws mvcMapConfigFileNotReadable
	 * @throws mvcException
	 */
	function load($inConfigFile) {
		if ( !@file_exists($inConfigFile) ) {
			throw new mvcMapConfigFileDoesNotExist($inConfigFile);
		}
		if ( !@is_readable($inConfigFile) ) {
			throw new mvcMapConfigFileNotReadable($inConfigFile);
		}
		$oXML = @simplexml_load_file($inConfigFile);
		if ( !is_object($oXML) ) {
			throw new mvcMapConfigFileIsNotValidXml($inConfigFile);
		}
		$this->setLoadedConfigFile($inConfigFile);
		$this->setControllerMap($oXML);
	}
	
	/**
	 * Writes the config data out to a file
	 *
	 * @param string $inConfigFile
	 * @return boolean
	 * @throws mvcMapConfigFileIsNotWritable
	 * @throws mvcMapConfigFileCouldNotBeWritten
	 * @throws mvcException
	 */
	function save($inConfigFile = null) {
		if ( $inConfigFile === null && $this->_LoadedConfigFile ) {
			$inConfigFile = $this->_LoadedConfigFile;
		}
		if ( !@is_writable(dirname($inConfigFile)) || (@file_exists($inConfigFile) && !@is_writable($inConfigFile)) ) {
			throw new mvcMapConfigFileIsNotWritable($inConfigFile);
		}
		
		$data = $this->getControllerMap()->asXML();
		$bytes = @file_put_contents($inConfigFile, $data, LOCK_EX);
		systemLog::info("Wrote $bytes bytes for $inConfigFile");
		if ( !$bytes || $bytes == 0 ) {
			throw new mvcMapConfigFileCouldNotBeWritten($inConfigFile);
		}
		return true;
	}
	
	/**
	 * Returns $_LoadedConfigFile
	 *
	 * @return array
	 */
	function getLoadedConfigFile() {
		return $this->_LoadedConfigFile;
	}
	
	/**
	 * Returns $_LoadedConfigFile
	 *
	 * @param array $inLoadedConfigFile
	 * @return mvcControllerMapper
	 */
	function setLoadedConfigFile($inLoadedConfigFile) {
		if ( $inLoadedConfigFile !== $this->_LoadedConfigFile ) {
			$this->_LoadedConfigFile = $inLoadedConfigFile;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the SimpleXML object set
	 *
	 * @return SimpleXMLElement
	 * @throws mvcMapConfigurationDataNotLoaded
	 */
	function getControllerMap() {
		if ( $this->_ControllerMap instanceof SimpleXMLElement ) {
			return $this->_ControllerMap;
		} else {
			throw new mvcMapConfigurationDataNotLoaded();
		}
	}
	
	/**
	 * Returns an array of mvcControllerMap objects
	 *
	 * @return array
	 */
	function getMapAsControllers() {
		$return = array();
		if ( $this->_ControllerMap ) {
			foreach ( $this->_ControllerMap as $oXML ) {
				$oMap = new mvcControllerMap();
				$oMap->setController($oXML);
				$return[] = $oMap;
			}
		}
		return $return;
	}
	
	/**
	 * Sets the SimpleXML controller map
	 *
	 * @param SimpleXMLElement $inControllerMap
	 * @return mvcControllerMapper
	 */
	function setControllerMap($inControllerMap) {
		if ( $inControllerMap !== $this->_ControllerMap ) {
			$this->_ControllerMap = $inControllerMap;
			$this->setModified();
		}
		return $this;
	}
	
	
	/**
	 * Returns controller named $inControllerName
	 *
	 * @param string $inControllerName
	 * @return mvcControllerMap
	 */
	function getController($inControllerName) {
		return $this->findController($inControllerName);
	}
	
	/**
	 * Attempts to find the controller named in $inRequestPath; returns entry from config
	 *
	 * @param string $inRequestPath
	 * @return mvcControllerMap
	 * @throws mvcMapControllerNotFound
	 * @throws mvcMapNonUniqueControllerName
	 * @throws mvcException
	 */
	function findController($inRequestPath) {
		$components = explode('/', $inRequestPath);
		if ( count($components) < 1 ) {
			throw new mvcMapNoPathComponentsToSearch($inRequestPath);
		}
		
		$xPathQuery = array();
		foreach ( $components as $component ) {
			if ( strlen($component) > 0 ) {
				$xPathQuery[] = 'controller[@name="'.$component.'"]';
			}
		}
		
		$return = $this->getControllerMap();
		$map = new mvcControllerMap();
		$lastController = $lastLevel = 0;
		$xPathCnt = count($xPathQuery);
		for ( $i=0; $i<$xPathCnt; $i++ ) {
			if ( $i == 0 ) {
				$query = $xPathQuery[$i];
			} else {
				$query = 'controllers/'.$xPathQuery[$i];
			}
			
			$return = $return->xpath($query);
			if ( $return && is_array($return) && count($return) > 0 ) {
				$return = $return[0];
				$lastController = $return;
				$lastLevel++;
				$map->addControllerToPath($return, $i);
				if ( !isset($return->controllers) ) {
					break;
				}
			} else {
				break;
			}
		}
		if ( $lastController instanceof SimpleXMLElement ) {
			$map->setController($lastController);
			$map->setControllerLevel($lastLevel);
		}
		return $map;
	}
	
	
	
	/**
	 * Returns $_Modified
	 *
	 * @return boolean
	 */
	function isModified() {
		return $this->_Modified;
	}
	
	/**
	 * Returns $_Modified
	 *
	 * @param boolean $inModified
	 * @return mvcControllerMap
	 */
	function setModified($inModified = true) {
		if ( $inModified !== $this->_Modified ) {
			$this->_Modified = $inModified;
		}
		return $this;
	}
}