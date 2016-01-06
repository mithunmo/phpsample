<?php
/**
 * mvcControllerMap.class.php
 * 
 * mvcControllerMap class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcControllerMap
 * @version $Rev: 650 $
 */


/**
 * mvcControllerMap
 * 
 * Holds the located controller info from the mapper; including the matched controller
 * and the controller path. getController() returns the matched controller; getControllerLevel()
 * returns at what "level" this was matched; and the path is held in getControllerPath()
 *
 * It should be noted that the original structure is still a SimpleXML object. This class
 * provides a wrapper around this allowing the SimpleXML to be used as instances rather than
 * multiple copies.
 * 
 * The controller properties can be accessed directly by accessor methods (getName(), getDescription()).
 * If the return would be SimpleXML, it is wrapped in another mvcControllerMap shell making the
 * interface consistent.
 * 
 * <code>
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
 * @category mvcControllerMap
 */
class mvcControllerMap {
	
	/**
	 * Stores $_Controller
	 * 
	 * @var SimpleXMLElement
	 * @access protected
	 */
	protected $_Controller				= false;
	
	/**
	 * Stores $_ControllerLevel
	 * 
	 * @var integer
	 * @access protected
	 */
	protected $_ControllerLevel			= 0;
	
	/**
	 * Stores $_ControllerPath
	 * 
	 * @var array
	 * @access protected
	 */
	protected $_ControllerPath			= array();
	
	
	
	/**
	 * Returns new instance of mvcControllerMap
	 *
	 * @return mvcControllerMap
	 */
	function __construct() {
		
	}
	
	
	
	/**
	 * Creates a new instance of mvcControllerMap using SimpleXML fragment
	 * 
	 * Level is the level within the site the SimpleXML object correlates to
	 * e.g. 0 is root, 1 would be second level etc. $inPath is an array of
	 * SimpleXML objects that make up the path to $oXML.
	 *
	 * @param SimpleXMLElement $oXML
	 * @param integer $inLevel
	 * @param array $inPath
	 * @return mvcControllerMap
	 * @static 
	 */
	static function factory(SimpleXMLElement $oXML, $inLevel = 0, $inPath = array()) {
		$oMap = new mvcControllerMap();
		$oMap->setController($oXML);
		$oMap->setControllerLevel($inLevel);
		$oMap->setControllerPath($inPath);
		$oMap->addControllerToPath($oXML, $inLevel);
		return $oMap;
	}
	
	
	
	/**
	 * Return ControllerPath
	 * 
	 * @return array(mvcControllerMap)
	 */
	function getControllerPath() {
		$return = array();
		if ( is_array($this->_ControllerPath) && count($this->_ControllerPath) > 0 ) {
			foreach ( $this->_ControllerPath as $level => $oXML ) {
				$return[] = self::factory($oXML, $level, $this->getControllerPathToLevel($level));
			}
		}
		return $return;
	}
	
	/**
	 * Returns an array of all the elements up to $inLevel; array is SimpleXMLElements
	 *
	 * @param integer $inLevel
	 * @return array(SimpleXMLElement)
	 */
	protected function getControllerPathToLevel($inLevel = 0) {
		$return = array();
		if ( is_array($this->_ControllerPath) && count($this->_ControllerPath) > 0 ) {
			foreach ( $this->_ControllerPath as $level => $oXML ) {
				if ( $level <= $inLevel ) {
					$return[$level] = $oXML;
				}
			}
		}
		return $return;
	}
	
	/**
	 * Returns the controller held at $inLevel in the controller path
	 *
	 * @param integer $inLevel
	 * @return mvcControllerMap
	 */
	function getControllerAtLevel($inLevel = 0) {
		if ( array_key_exists($inLevel, $this->_ControllerPath) ) {
			return self::factory($this->_ControllerPath[$inLevel], $inLevel, $this->getControllerPathToLevel($inLevel));
		}
		return false;
	}
	
	/**
	 * Adds a path element to the map, $inLevel is the depth that this map corresponds to; 0 = root
	 *
	 * @param SimpleXMLElement $inPath
	 * @param integer $inLevel
	 * @return mvcControllerMap
	 */
	function addControllerToPath(SimpleXMLElement $inPath, $inLevel = 0) {
		$key = array_search($inPath, $this->_ControllerPath);
		if ( $key === false || ($key !== false && $key !== $inLevel) ) {
			if ( $key !== false && $key !== $inLevel ) {
				$this->_ControllerPath[$key] = null;
				unset($this->_ControllerPath[$key]);
			}
			$this->_ControllerPath[$inLevel] = $inPath;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Set the full path to the controller overwriting the existing data, must be array of SimpleXML elements
	 * 
	 * @param array $inControllerPath
	 * @return mvcControllerMap
	 */
	function setControllerPath(array $inControllerPath = array()) {
		if ( $inControllerPath !== $this->_ControllerPath ) {
			$this->_ControllerPath = $inControllerPath;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Return just the controller name
	 *
	 * @return string
	 */
	function getName() {
		return (string) $this->_Controller['name'];
	}
	
	/**
	 * Returns controller description; returns formatted name if no description
	 *
	 * @return string
	 */
	function getDescription() {
		if ( isset($this->_Controller['description']) ) {
			return (string) $this->_Controller['description'];
		} else {
			return utilityStringFunction::convertCapitalizedString(ucwords($this->getName()));
		}
	}
	
	/**
	 * Returns just the path set in the controller XML definition
	 *
	 * @return string
	 */
	function getPath() {
		return (string) isset($this->_Controller['path']) ? $this->_Controller['path'] : '';
	}
	
	
	
	/**
	 * Return SimpleXML controller definition built from the controllerMap.xml
	 * 
	 * @return SimpleXMLElement
	 */
	function getController() {
		return $this->_Controller;
	}
	
	/**
	 * Set $_Controller to $inController
	 * 
	 * @param SimpleXMLElement $inController
	 * @return mvcControllerMap
	 */
	function setController($inController) {
		if ( $inController !== $this->_Controller ) {
			$this->_Controller = $inController;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Return ControllerLevel
	 * 
	 * @return integer
	 */
	function getControllerLevel() {
		return $this->_ControllerLevel;
	}
	
	/**
	 * Set $_ControllerLevel to $inControllerLevel
	 * 
	 * @param integer $inControllerLevel
	 * @return mvcControllerMap
	 */
	function setControllerLevel($inControllerLevel) {
		if ( $inControllerLevel !== $this->_ControllerLevel ) {
			$this->_ControllerLevel = $inControllerLevel;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns true if the current controller has sub-controllers
	 *
	 * @return boolean
	 */
	function hasSubControllers() {
		return isset($this->_Controller->controllers);
	}
	
	/**
	 * Returns the sub-controllers for the current controller; if no sub-controllers returns empty array
	 *
	 * @return array(mvcControllerMap)
	 */
	function getSubControllers() {
		$return = array();
		if ( $this->hasSubControllers() ) {
			foreach ( $this->_Controller->controllers->controller as $oXML ) {
				$return[] = self::factory($oXML, $this->getControllerLevel()+1, $this->_ControllerPath);
			}
		}
		return $return;
	}
	
	
	
	/**
	 * Returns controller path e.g. something/somewhere/doController.class.php
	 *
	 * @return string
	 */
	function getFilePath() {
		if ( isset($this->_Controller['path']) ) {
			return (string) $this->_Controller['path'];
		} else {
			return $this->getPathAsString().system::getDirSeparator().$this->getName().'Controller.class.php';
		}
	}
	
	/**
	 * Returns the path as seen on the URI i.e. separated with a /
	 *
	 * @return string
	 */
	function getUriPath() {
		return $this->getPathAsString('/');
	}
	
	/**
	 * Returns the path as a string using $inSeparator to split the path; path is prefixed with $inSeparator
	 * If $inSeparator is null system dir sep is used
	 *
	 * @param string $inSeparator
	 * @return string
	 */
	function getPathAsString($inSeparator = null) {
		$separator = system::getDirSeparator();
		if ( $inSeparator !== null ) {
			$separator = $inSeparator;
		}
		if ( is_array($this->_ControllerPath) && count($this->_ControllerPath) > 0 ) {
			$path = array();
			foreach ( $this->getControllerPath() as $oController ) {
				$path[] = $oController->getName();
			}
			$path = implode($separator, $path);
		} else {
			$path = $this->getName();
		}
		
		if ( strpos($path, $separator) !== 0 ) {
			$path = $separator.$path;
		}
		return $path;
	}
	
	/**
	 * Returns the main controllers and descriptions to get to the current controller
	 *
	 * @return array(mvcControllerMapPath)
	 */
	function getPathComponents() {
		if ( is_array($this->_ControllerPath) && count($this->_ControllerPath) > 0 ) {
			$path = array();
			
			foreach ( $this->getControllerPath() as $oController ) {
				$path[] = new mvcControllerMapPath($oController->getName(), $oController->getDescription(), $oController->getUriPath());
			}
		} else {
			$path = array(
				new mvcControllerMapPath($this->getName(), $this->getDescription(), $this->getUriPath())
			);
		}
		return $path;
	}
}