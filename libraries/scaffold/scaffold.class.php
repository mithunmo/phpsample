<?php
/**
 * scaffold.class.php
 *
 * System scaffold class
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage scaffold
 * @category scaffold
 * @version $Rev: 650 $
 */


/**
 * scaffold
 *
 * Scaffold provides a very rudimentary CRUD framework. This makes basic forms for creating, retriving,
 * updating and deleting records of a particular object. It is similar to the Ruby on Rails scaffold system
 * except you need to pass in the object to be scaffold'd. The passed object must be an instance of
 * systemDaoInterface.
 * 
 * The scaffold will intelligently remove static and private methods and properties that can not be used.
 * If methods return objects, this is checked in the template and the result is not displayed.
 *
 * Note: this does NOT perform any validation or authentication it is intended for prototyping only or
 * for quick and dirty data entry during development - it should NOT be used in a production environment.
 * 
 * Example usage:
 * <code>
 * // include system.inc and then in a web accessible folder use scaffold
 * // e.g. with an existing systemDaoObject
 * scaffold::getInstance(new systemUser(), $_REQUEST['action'])->launch();
 * </code>
 * 
 * The scaffold engine uses Smarty internally and the templates can be customised if you want. The HTML
 * produced can be easily modified and it does not wrap in <html> tags so any additional code can be
 * added e.g. on an intranet.
 * 
 * It will only parse out and display properties that have get and set methods. All others, including
 * anything that returns arrays or objects will be ignored.
 * 
 * @package scorpio
 * @subpackage scaffold
 * @category scaffold
 */
class scaffold {
	
	/**
	 * Array of instances
	 *
	 * @var array
	 * @access private
	 * @static
	 */
	private static $_Instances	= array();
	
	/**
	 * Stores $_DaoObject
	 *
	 * @var systemDaoInterface
	 * @access protected
	 */
	protected $_DaoObject = false;
	/**
	 * Stores $_Modified
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;
	/**
	 * Stores $_Action
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Action			= 'create';
	
	const ACTION_CREATE			= 'create';
	const ACTION_DO_CREATE		= 'docreate';
	const ACTION_UPDATE			= 'update';
	const ACTION_DO_UPDATE		= 'doupdate';
	const ACTION_RETRIEVE		= 'retrieve';
	const ACTION_DELETE			= 'delete';
	const ACTION_DO_DELETE		= 'dodelete';
	
	/**
	 * Array of permitted actions
	 *
	 * @var array
	 */
	protected $_AllowedActions = array(
		self::ACTION_CREATE, self::ACTION_DO_CREATE, self::ACTION_DELETE, self::ACTION_DO_DELETE, self::ACTION_RETRIEVE, self::ACTION_UPDATE, self::ACTION_DO_UPDATE
	);
	/**
	 * Stores $_Template
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Template = 'retrieve.tpl';
	/**
	 * Instance of Smarty
	 *
	 * @var Smarty
	 * @access protected
	 */
	protected $_Engine = false;
	/**
	 * Holds a reflection object
	 *
	 * @var RelectionObject
	 */
	protected $_Reflect = false;
	
	
	
	/**
	 * Creates a new instance of scaffold
	 *
	 * @param systemDaoInterface $oObject
	 * @param string $inAction
	 */
	function __construct($oObject, $inAction = self::ACTION_RETRIEVE) {
		if ( !$oObject instanceof systemDaoInterface ) {
			$oReflect = new ReflectionObject($oObject);
			throw new scaffoldException('Supplied object was not instance of systemDaoInterface (received: '.$oReflect->getName().' which implements '.implode(',', $oReflect->getInterfaces()).')');
		}
		$this->setDaoObject($oObject);
		$this->setAction($inAction);
		
		$this->_Engine = new systemSmartyBase();
		$this->_Engine->setTemplateDir(system::getConfig()->getPathLibraries().system::getDirSeparator().'scaffold'.system::getDirSeparator().'templates');
		$this->_Engine->setCompileDir('scaffold');
		$this->_Engine->setCompileCheck(true);
		$this->_Engine->setCaching(false);
	}
	
	/**
	 * Returns a new instance of the scaffold and maintains a reference to the combination of scaffold and object
	 *
	 * @param systemDaoInterface $oObject
	 * @param string $inAction
	 * @return scaffold
	 */
	static function getInstance(systemDaoInterface $oObject, $inAction = self::ACTION_RETRIEVE) {
		$key = array_search($oObject, self::$_Instances);
		if ( $key !== false ) {
			$oObject = self::$_Instances[$key];
			$oObject->setAction($inAction);
			return $oObject;
		}
		
		$oScaffold = new scaffold($oObject, $inAction);
		self::$_Instances[] = $oScaffold;
		return $oScaffold;
	}
	
	
	
	/**
	 * Returns a new reflection object
	 *
	 * @return ReflectionObject
	 */
	function getReflectionObject() {
		if ( !$this->_Reflect ) {
			$this->_Reflect = new ReflectionObject($this->_DaoObject);
		}
		return $this->_Reflect;
	}
	
	/**
	 * Returns an array of method names matching the supplied regExp and where the method is not
	 * in the ignore array. Static, private and protected methods are ignored
	 *
	 * @param string $inRegExp
	 * @param array $inIgnoreList
	 * @return array
	 */
	function getMethodsByRegExp($inRegExp, $inIgnoreList = array()) {
		$return = array();
		$methods = $this->getReflectionObject()->getMethods();
		if ( is_array($methods) && count($methods) > 0 ) {
			if ( false ) $oMethod = new ReflectionMethod();
			foreach ( $methods as $oMethod ) {
				if ( $oMethod->isStatic() || $oMethod->isPrivate() || $oMethod->isProtected() ) {
					continue;
				}
				
				if ( preg_match($inRegExp, $oMethod->getName()) ) {
					$add = true;
					if ( count($inIgnoreList) > 0 ) {
						foreach ( $inIgnoreList as $methodName ) {
							if ( stripos($oMethod->getName(), $methodName) !== false ) {
								$add = false;
							}
						}
					}
					if ( $add == true ) {
						$return[] = $oMethod->getName();
					}
				}
			}
		}
		return $return;
	}
	
	/**
	 * Returns true if action is in permitted actions
	 *
	 * @param string $inAction
	 * @return boolean
	 */
	function isAllowedAction($inAction) {
		return (in_array($inAction, $this->_AllowedActions));
	}
	
	/**
	 * Returns an array of properties for the object, if $inGetValues is true, returns associative
	 * array including default values
	 *
	 * @param boolean $inGetValues
	 * @return array
	 */
	function getProperties($inGetValues = false) {
		$return = array();
		/*
		 * Reflection properties can not access private or protected properties, so we have to
		 * hack around it by calling toArray() which calls an internal get_object_vars function
		 */
		$classVars = $this->_DaoObject->toArray();
		
		/*
		 * Now we get the reflection class so we can get the property types and better intel on the object
		 */
		$properties = $this->getReflectionObject()->getProperties();
		if ( is_array($properties) && count($properties) > 0 ) {
			if ( false ) $oProperty = new ReflectionProperty();
			foreach ( $properties as $oProperty ) {
				if ( !$oProperty->isStatic() && !in_array($oProperty->getName(), array('_Modified')) ) {
					/**
					 * only bother with properties that have both get AND set methods
					 */
					$get = str_replace('_', 'get', $oProperty->getName());
					$set = str_replace('_', 'set', $oProperty->getName());
					
					if ( method_exists($this->_DaoObject, $get) && method_exists($this->_DaoObject, $set) ) {
						if ( $inGetValues ) {
							if ( $oProperty->isPublic() ) {
								$return[$oProperty->getName()] = $oProperty->getValue();
							} else {
								$return[$oProperty->getName()] = $classVars[$oProperty->getName()];
							}
						} else {
							$return[] = $oProperty->getName();
						}
					}
				}
			}
		}
		return $return;
	}
	
	/**
	 * Returns the Get methods for the object
	 *
	 * @return array
	 */
	function getGetMethods() {
		return $this->getMethodsByRegExp('/^(get)([A-Za-z0-9]+)/', array('getInstance'));
	}
	
	/**
	 * Returns the Get methods for the object
	 *
	 * @return array
	 */
	function getSetMethods() {
		return $this->getMethodsByRegExp('/^(set)([A-Za-z0-9]+)/');
	}
	
	/**
	 * Handles requests into the class
	 *
	 * @return void
	 */
	function launch() {
		$vars = $this->buildRequestVars();
		if ( isset($vars['action']) ) {
			$this->setAction($vars['action']);
		}
		
		if ( $this->isAllowedAction($this->_Action) ) {
			switch ( $this->_Action ) {
				case self::ACTION_CREATE:
				case self::ACTION_DO_CREATE:
					$return = $this->doCreate();
					break;
					
				case self::ACTION_DELETE:
				case self::ACTION_DO_DELETE:
					$return = $this->doDelete();
					break;
				
				case self::ACTION_RETRIEVE:
					$return = $this->doRetrieve();
					break;
					
				case self::ACTION_UPDATE:
				case self::ACTION_DO_UPDATE:
					$return = $this->doUpdate();
					break;
			}
			if ( is_bool($return) ) {
				if ( $return === false ) {
					$this->setTemplate('error.tpl');
					echo $this->buildHtml();
				} else {
					$this->goHome();
				}
			} else {
				echo $return;
			}
		} else {
			throw new scaffoldException('Invalid action specified ('.$this->_Action.'), supported actions are: '.implode(', ', $this->_AllowedActions));
		}
	}
	
	/**
	 * Filters inbound request data and returns as a filtered array
	 *
	 * @return array
	 */
	function buildRequestVars() {
		$oFilterManager = new utilityInputManager(utilityInputManager::LOOKUPGLOBALS_POST);
		$oFilterManager->addFilter('action', utilityInputFilter::filterString());
		foreach ( $this->getProperties() as $propertyName ) {
			$oFilterManager->addFilter($propertyName, utilityInputFilter::filterString());
		}
		$vars = $oFilterManager->doFilter();
		
		if ( count($vars) == 0 ) {
			$oFilterManager->setlookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
			$vars = $oFilterManager->doFilter();
		}
		return $vars;
	}
	
	/**
	 * Assigns request vars to the DaoObject
	 *
	 * @return void
	 */
	function doVarAssignment() {
		$data = $this->buildRequestVars();
		foreach ( $data as $var => $value ) {
			$var = str_replace('_','', $var);
			$method = 'set'.$var;
			if ( method_exists($this->_DaoObject, $method) ) {
				systemLog::info('Setting property with '.$method.' and value '.$value);
				$this->_DaoObject->$method($value);
			}
		}
	}
	
	/**
	 * Builds the HTML from the current template and returns it as a string
	 *
	 * @return string
	 */
	function buildHtml() {
		$this->_Engine->assign('textUtil', new utilityInflectorWrapper());
		$this->_Engine->assign('getMethods', $this->getGetMethods());
		$this->_Engine->assign('getMethodCount', count($this->getGetMethods()));
		$this->_Engine->assign('properties', $this->getProperties());
		$this->_Engine->assign('propertyValues', $this->getProperties(true));
		$this->_Engine->assign('propertyCount', count($this->getProperties()));
		$this->_Engine->assign('Action', $this->_Action);
		$this->_Engine->assign('ObjectName', $this->getReflectionObject()->getName());
		return $this->_Engine->fetch($this->_Template);
	}
	
	/**
	 * Returns to the home page (retrieval page)
	 *
	 * @return void
	 */
	function goHome() {
		header("Location: ".$_SERVER['SCRIPT_NAME'].'?action='.self::ACTION_RETRIEVE);
		exit;
	}
	
	/**
	 * Handles creating a record; if action is not DO_CREATE returns HTML
	 *
	 * @return void
	 */
	function doCreate() {
		if ( $this->_Action == self::ACTION_DO_CREATE ) {
			$this->doVarAssignment();
			try { 
				$this->_DaoObject->save();
				return true;
			} catch ( Exception $e ) {
				$this->_Engine->assign('Exception', $e);
				return false;
			}
		} else {
			$this->setTemplate('create.tpl');
			$this->_Engine->assign('DaoObject', $this->_DaoObject);
			return $this->buildHtml();
		}
	}
	
	/**
	 * Handles retrieving a record, returns HTML
	 *
	 * @return string
	 */
	function doRetrieve() {
		$this->_Engine->assign('records', $this->_DaoObject->listOfObjects());
		$this->setTemplate('retrieve.tpl');
		return $this->buildHtml();
	}
	
	/**
	 * Handles performing an update to the DAO object; if action is not DO_UPDATE returns HTML code
	 *
	 * @return boolean|string
	 */
	function doUpdate() {
		$this->doVarAssignment();
		
		if ( $this->_Action == self::ACTION_DO_UPDATE ) {
			try {
				$this->_DaoObject->save();
				return true;
			} catch ( Exception $e ) {
				$this->_Engine->assign('Exception', $e);
				return false;
			}
		} else {
			$this->_DaoObject->load();
			$this->setTemplate('update.tpl');
			$this->_Engine->assign('DaoObject', $this->_DaoObject);
			return $this->buildHtml();
		}
	}
	
	/**
	 * Handles delete request; if action is not DO_DELETE returns HTML code
	 *
	 * @return boolean|string
	 */
	function doDelete() {
		$this->doVarAssignment();
		if ( $this->_Action == self::ACTION_DO_DELETE ) {
			try {
				$this->_DaoObject->delete();
				return true;
			} catch ( Exception $e ) {
				$this->_Engine->assign('Exception', $e);
				return false;
			}
		} else {
			$this->_DaoObject->load();
			$this->setTemplate('delete.tpl');
			$this->_Engine->assign('DaoObject', $this->_DaoObject);
			return $this->buildHtml();
		}
	}
	
	
	
	/**
	 * Returns the value of $_Action
	 *
	 * @return string
	 */
	function getAction() {
		return $this->_Action;
	}
	
	/**
	 * Sets the value of $_Action to $inAction
	 *
	 * @var string $inAction
	 * @return scaffold
	 */
	function setAction($inAction) {
		if ( $this->_Action !== $inAction ) {
			$this->_Action = $inAction;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the value of $_Modified
	 *
	 * @return boolean
	 */
	function getModified() {
		return $this->_Modified;
	}
	
	/**
	 * Sets the value of $_Modified to $inModified
	 *
	 * @var boolean $inModified
	 * @return scaffold
	 */
	function setModified($inModified = true) {
		$this->_Modified = $inModified;
		return $this;
	}
	
	/**
	 * Returns the value of $_DaoObject
	 *
	 * @return systemDaoInterface
	 */
	function getDaoObject() {
		return $this->_DaoObject;
	}
	
	/**
	 * Sets the value of $_DaoObject to $inDaoObject
	 *
	 * @var systemDaoInterface $inDaoObject
	 * @return scaffold
	 */
	function setDaoObject($inDaoObject) {
		if ( $this->_DaoObject !== $inDaoObject ) {
			$this->_DaoObject = $inDaoObject;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the value of $_Template
	 *
	 * @return string
	 */
	function getTemplate() {
		return $this->_Template;
	}
	
	/**
	 * Set $_Template to $inTemplate
	 *
	 * @param string $inTemplate
	 * @return scaffold
	 */
	function setTemplate($inTemplate) {
		if ( $this->_Template !== $inTemplate ) {
			$this->_Template = $inTemplate;
			$this->setModified();
		}
		return $this;
	}
}