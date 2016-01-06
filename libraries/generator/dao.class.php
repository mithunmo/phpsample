<?php
/**
 * generatorDao
 * 
 * Stored in generatorDao.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage generator
 * @category generatorDao
 * @version $Rev: 837 $
 */


/**
 * generatorDao class
 *
 * Code generator master class. This class will parse a database or database.table and
 * build a set of classes based on whatever templates have been configured. Code generator
 * does not build relationships nor does it attempt to link derived classes together,
 * it simply returns basic structures that then need further development to flesh them out.
 * While so of these will work without modification, many-to-many tables will need
 * replacing.
 * 
 * Generator is not intended to build whole systems, but to make the basic step of
 * building the base objects a little easier and quicker. Internally it uses dbMapper
 * and Smarty to fetch and then format the data.
 * 
 * The exception class and autoload class are the only pre-defined classes as there is not
 * much to them; plus they will only be generated if a database is parsed over.
 * 
 * Custom templates can be defined and used - they are just smarty templates.
 * 
 * 
 * <b>Notes and observations:</b>
 * 
 * When using this class there are a few points to remember and bare in mind. Under
 * certain circumstances it may not produce quite the result you intended. Principally
 * this is seen in class names and the "package base".
 * 
 * The package base is by default the database name - it will be overriden by setting a
 * class prefix. Now, the problems occur when databases are named outside of the expected
 * format. Scorpio convention for database names is "databaseName" with no underscores
 * or other separators. In most cases the database name is only a single all lowercase
 * word (e.g. system, wurfl, log etc.). If you use underscore separated names (for example)
 * you should specify a prefix otherwise your classes will be generated as TableName
 * but in folders of "database_name" and the autoload and exception files will be
 * likewise built as "database_name_autoload" and "database_name_exception". This format
 * may break the systemAutoload set-up.
 *
 * @package scorpio
 * @subpackage generator
 * @category generatorDao
 */
class generatorDao extends generatorBase {
	
	const OPTION_DATABASE = 'database';
	const OPTION_DATABASE_TABLE = 'database.table';
	const OPTION_USE_DATABASE_AS_PREFIX = 'use.db.as.prefix';
	const OPTION_CLASS_PREFIX = 'class.prefix';
	const OPTION_CLASS_NAME = 'class.name';
	const OPTION_PACKAGE_BASE = 'package.base';
	const OPTION_PACKAGE_EXCEPTION = 'package.exception';
	
	/**
	 * The database map compiled from the dbMapper
	 *
	 * @var dbMapper
	 * @access protected
	 */
	protected $_DbMap				= false;
	/**
	 * Array of autoload information: array(classname => location)
	 *
	 * @var array
	 * @access protected
	 */
	protected $_AutoloadArray		= array();
	/**
	 * Stores compiled autoload cache file
	 *
	 * @var string
	 * @access protected
	 */
	protected $_AutoloadData		= '';
	
	
	
	/**
	 * @see generatorBase::reset()
	 */
	function reset() {
		parent::reset();
		
		$this->getOptionsSet()->setOptions(
			array(
				self::OPTION_TEMPLATE_DIR_DEFAULT =>
					utilityStringFunction::cleanDirSlashes(system::getConfig()->getPathLibraries().'/generator/templates/dao'),
				self::OPTION_TEMPLATE_DIR_USER => system::getConfig()->getGeneratorUserTemplatePath(),
				self::OPTION_TEMPLATE_DEFAULT => 'default.tpl',
				
				self::OPTION_DATABASE => '',
				self::OPTION_DATABASE_TABLE => '',
				self::OPTION_CLASS_NAME => '',
				self::OPTION_CLASS_PREFIX => false,
				self::OPTION_USE_DATABASE_AS_PREFIX => false,
			)
		);
	}
	
	/**
	 * @see generatorBase::buildDataSource()
	 */
	function buildDataSource() {
		systemLog::getInstance()->setSource('buildMap');
		systemLog::message("Attempting to build data for >> ({$this->getDatabase()})");
		$oDbMapper = dbManager::getInstance()->getDbUtilities()->getDbMapper();
		$oDbMapper->setDatabase($this->getDatabase());
		if ( !$this->getTable() ) {
			systemLog::message("Building data for entire database");
			$oDbMapper->buildDbMap();
		} else {
			systemLog::message("Building data for table >> ({$this->getTable()})");
			$oDbUtils = dbManager::getInstance()->getDbUtilities();
			$oTableDefinition = dbMapperTableDefinition::getInstance(
				$this->getDatabase(), $this->getTable()
			)->setFields(
				dbMapperFieldSet::getInstance()->setFields(
					$oDbUtils->getTableProperties($this->getDatabase(), $this->getTable())
				)
			)->setIndexes(
				dbMapperIndexSet::getInstance()->setIndexes(
					$oDbUtils->getTableIndexes($this->getDatabase(), $this->getTable())
				)
			)->setConstraints(
				dbMapperConstraintSet::getInstance()->setConstraints(
					$oDbUtils->getTableForeignKeys($this->getDatabase(), $this->getTable())
				)
			);
			
			$oDbMapper->getDbMap()->addTableDefinition($oTableDefinition);
		}
		$this->setDbMap($oDbMapper);
		
		$this->buildPackageBase();
		$this->buildExceptionClass();
	}
	
	/**
	 * @see generatorBase::build()
	 */
	function build() {
		$this->getEngine()->assign('classBaseString', $this->getPackageBase());
		$this->getEngine()->assign('database', $this->getDbMap()->getDatabase());
		$this->getEngine()->assign('exceptionClass', $this->getPackageException());
		$this->getEngine()->setTemplateDir(dirname($this->getTemplateFile($this->getTemplate())));
		
		if ( false ) $oTableDefinition = new dbMapperTableDefinition();
		foreach ( $this->getDbMap()->getDbMap() as $oTableDefinition ) {
			if ( $this->getClassname() ) {
				$className = $this->getClassname();
			} else {
				// avoid double prefixes -- except if packageBase has been set
				if ( (!$this->getPackageBase() && stripos($oTableDefinition->getTableName(), $this->getDatabase()) === 0) || stripos($oTableDefinition->getTableName(), $this->getPackageBase()) === 0 ) {
					$className = utilityInflector::variablize(utilityInflector::classify($oTableDefinition->getTableName()));
				} else {
					if ( $this->getUseDatabaseAsPrefix() || $this->getClassPrefix() ) {
						$className = $this->getPackageBase().utilityInflector::classify($oTableDefinition->getTableName());
					} else {
						$className = utilityInflector::variablize(utilityInflector::classify($oTableDefinition->getTableName()));
					}
				}
			}
			systemLog::getInstance()->setSource("buildClasses][$className");
			systemLog::message("Compiling data for class");
			
			systemLog::message("Fetching primary key data");
			$pKey = $oTableDefinition->getIndexes()->getFieldsInKey('PRIMARY');
			if ( count($pKey) == 0 ) {
				systemLog::warning("No primary key found, attempting to locate Unique Key");
				$uKey = $oTableDefinition->getIndexes()->getFieldsInUniqueKey();
				if ( count($uKey) > 0 ) {
					foreach ( $uKey as $indexName => $fields ) {
						$pKey = $fields;
						break;
					}
					
					$textkey = array();
					foreach ( $pKey as $index ) {
						$textkey[] = $index->getColumnName();
					}
					systemLog::notice("Found Unique key on ".implode(' -> ', $textkey)." using as primary key");
				} else {
					systemLog::warning("Failed to locate any kind of primary or unique key, continuing anyway but may have errors");
				}
			}
			
			$this->getEngine()->assign('hasUpdateField', $this->hasUpdateDateField($oTableDefinition));
			$this->getEngine()->assign('className', $className);
			$this->getEngine()->assign('primaryKey', $pKey);
			$this->getEngine()->assign('uniqueKeys', $oTableDefinition->getIndexes()->getFieldsInUniqueKey());
			$this->getEngine()->assign('nonUniqueFields', $oTableDefinition->getNonUniqueFields());
			$this->getEngine()->assign('uniqueKeyFields', $oTableDefinition->getIndexes()->getDistinctUniqueFields());
			$this->getEngine()->assign('oTableDefinition', $oTableDefinition);
			
			systemLog::message("Generating class via Smarty");
			$classData = $this->getEngine()->fetch($this->getTemplateFile($this->getTemplate()));
			
			// add to autoload
			$this->_AutoloadArray[$className] = $this->generateClassFileNameAndPath($className);
			$this->addGeneratedContent($classData, $className);
		}
		$this->buildAutoloadData();
	}

	/**
	 * Returns true if the field set has a field named updateDate or something similar
	 *
	 * @param dbMapperTableDefinition $oTableDefinition
	 * @return boolean
	 */
	function hasUpdateDateField($oTableDefinition) {
		foreach ( $oTableDefinition->getFields() as $oField ) {
			switch ( $oField->getField() ) {
				case 'updateDate':
				case 'updatedate':
				case 'update_date':
				case 'updated':
				case 'updateddate':
				case 'updated_date':
				case 'modified':
				case 'timestamp':
					return true;
				break;
			}
		}
		return false;
	}
	
	/**
	 * Returns a string containing the path and filename for the class
	 *
	 * @param string $inClassname
	 * @return string
	 */
	function generateClassFileNameAndPath($inClassname) {
		$file = false;
		$components = explode(' ', utilityStringFunction::convertCapitalizedString(trim(str_replace($this->getPackageBase(), '', $inClassname))));
		
		/**
		 * Bug Fix: 2010-06-06 getting empty array elements that need to be cleaned out
		 * before checking number of components
		 */
		$tmp = array();
		foreach ( $components as $component ) {
			if ( strlen(trim($component)) > 0 ) {
				$tmp[] = $component;
			}
		}
		$components = $tmp;
		
		switch ( count($components) ) {
			case 0:
				break;

			default:
				$file =
					$this->getPackageBase().system::getDirSeparator().
					strtolower(
						implode(system::getDirSeparator(), $components)
					).
					'.class.php';
			break;
		}
		if ( !$file ) {
			$file = $this->getPackageBase().system::getDirSeparator().$inClassname.'.class.php';
		}
		
		return $file;
	}
	
	/**
	 * Creates a string for the package base
	 *
	 * @return void
	 */
	function buildPackageBase() {
		if ( $this->getClassPrefix() ) {
			$this->setPackageBase($this->getClassPrefix());
		} else {
			$this->setPackageBase(utilityInflector::singularize($this->getDbMap()->getDatabase()));
		}
	}
	
	/**
	 * Builds an exception object for the class, returning the class name
	 *
	 * @return void
	 */
	function buildExceptionClass() {
		systemLog::message("Building exception class for ({$this->getPackageBase()})");
		$this->setPackageException($this->getPackageBase().'Exception');
		$appAuthor = system::getConfig()->getParam('app','author');
		$appCopyright = system::getConfig()->getParam('app','copyright');
		
		$data = <<<BLOCK
<?php
/**
 * {$this->getPackageException()}
 *
 * Stored in exception.class.php
 * 
 * @author $appAuthor
 * @copyright $appCopyright
 * @package scorpio
 * @subpackage {$this->getPackageBase()}
 * @category {$this->getPackageException()}
 * @version \$Rev: 316 \$
 */


/**
 * {$this->getPackageException()}
 *
 * {$this->getPackageException()} class
 * 
 * @package scorpio
 * @subpackage {$this->getPackageBase()}
 * @category {$this->getPackageException()}
 */
class {$this->getPackageException()} extends systemException {
	
	/**
	 * Exception constructor
	 *
	 * @param string \$message
	 */
	function __construct(\$message) {
		parent::__construct(\$message);
	}
}
BLOCK;
		
		if ( !$this->getTable() ) {
			$this->_AutoloadArray[$this->getPackageException()] = $this->getPackageBase().system::getDirSeparator().'exception.class.php';
			$this->addGeneratedContent($data, $this->getPackageException());
		}
	}
	
	/**
	 * Creates an autoload cache file from the class data
	 *
	 * @return void
	 */
	function buildAutoloadData() {
		systemLog::message("Building autoload data array");
		$appAuthor = system::getConfig()->getParam('app','author');
		$appCopyright = system::getConfig()->getParam('app','copyright');
		
		$data = <<<BLOCK
<?php
/**
 * system Autoload component; auto-generated by system generator
 * 
 * @author $appAuthor
 * @copyright $appCopyright
 * @package scorpio
 * @subpackage system
 * @category systemAutoload
 */
return array(

BLOCK;

		foreach ( $this->_AutoloadArray as $className => $location ) {
			$data .= "	'$className' => '$location',\n";
		}
		$data .= ');';
		
		$this->setAutoloadData($data);
	}
	
	

	/**
	 * @see generatorBase::_resolveUserTemplate()
	 */
	protected function _resolveUserTemplateName($inTemplate) {
		return $inTemplate;
	}
	
	/**
	 * @see generatorBase::_resolveDefaultTemplate()
	 */
	protected function _resolveDefaultTemplateName($inTemplate) {
		return $inTemplate;
	}

	/**
	 * @see generatorBase::_findTemplate()
	 * 
	 * If this method is called in DAO generator, then template was not found so die
	 */
	protected function _findTemplate($inTemplate) {
		throw new generatorException("Unable to locate $inTemplate in any system path");
	}
	
	
	
	/**
	 * Returns the dbMapper object
	 *
	 * @return dbMapper
	 */
	function getDbMap() {
		return $this->_DbMap;
	}
	
	/**
	 * Sets the dbMapper object externally, should not be used with buildDataSource
	 *
	 * @param dbMapper $inMapper
	 * @return generatorDao
	 */
	function setDbMap(dbMapper $inMapper) {
		$this->_DbMap = $inMapper;
		return $this;
	}
	
	/**
	 * Returns the internal array of autoload classes and locations
	 *
	 * @return array
	 */
	function getAutoloadArray() {
		return $this->_AutoloadArray;
	}
	
	/**
	 * Set an array of autoload data, array should be classname => location
	 *
	 * @param array $inArray
	 * @return generatorDao
	 */
	function setAutoloadArray(array $inArray = array()) {
		if ( $inArray !== $this->_AutoloadArray ) {
			$this->_AutoloadArray = $inArray;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the compiled autoload data cache file as a string
	 *
	 * @return string
	 */
	function getAutoloadData() {
		return $this->_AutoloadData;
	}
	 
	/**
	 * Sets the compiled autoload data
	 *
	 * @param string $inAutoloadData
	 * @return generator
	 */
	function setAutoloadData($inAutoloadData) {
		if ( $this->_AutoloadData !== $inAutoloadData ) {
			$this->_AutoloadData = $inAutoloadData;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns the database name to process
	 *
	 * @return string
	 */
	function getDatabase() {
		return $this->getOptionsSet()->getOptions(self::OPTION_DATABASE, false);
	}
	
	/**
	 * Set the database to process
	 *
	 * @param string $inDatabase
	 * @return generatorDao
	 */
	function setDatabase($inDatabase) {
		$this->getOptionsSet()->setOptions(array(self::OPTION_DATABASE => $inDatabase));
		return $this;
	}
	
	/**
	 * Returns the table to process
	 *
	 * @return string
	 */
	function getTable() {
		return $this->getOptionsSet()->getOptions(self::OPTION_DATABASE_TABLE, false);
	}
	
	/**
	 * Sets the table to process
	 *
	 * @param string $inTable
	 * @return generatorDao
	 */
	function setTable($inTable) {
		$this->getOptionsSet()->setOptions(array(self::OPTION_DATABASE_TABLE => $inTable));
		return $this;
	}
	
	/**
	 * Returns whether database name should be used as prefix
	 *
	 * @return boolean
	 */
	function getUseDatabaseAsPrefix() {
		return $this->getOptionsSet()->getOptions(self::OPTION_USE_DATABASE_AS_PREFIX, false);
	}
	
	/**
	 * Sets if database name should be used as class prefix
	 *
	 * @param boolean $inStatus
	 * @return generatorDao
	 */
	function setUseDatabaseAsPrefix($inStatus) {
		$this->getOptionsSet()->setOptions(array(self::OPTION_USE_DATABASE_AS_PREFIX => $inStatus));
		return $this;
	}
	
	/**
	 * Returns the prefix to be used on the class
	 *
	 * @return string
	 */
	function getClassPrefix() {
		return $this->getOptionsSet()->getOptions(self::OPTION_CLASS_PREFIX, false);
	}
	
	/**
	 * Sets the class prefix
	 *
	 * @param string $inPrefix
	 * @return generatorDao
	 */
	function setClassPrefix($inPrefix) {
		$this->getOptionsSet()->setOptions(array(self::OPTION_CLASS_PREFIX => $inPrefix));
		return $this;
	}
	
	/**
	 * Returns a class name to be used during generation; only effective if building a single table
	 *
	 * @return string
	 */
	function getClassname() {
		return $this->getOptionsSet()->getOptions(self::OPTION_CLASS_NAME);
	}

	/**
	 * Set the classname to be used for a single table
	 *
	 * @param string $inClassname
	 * @return generatorDao
	 */
	function setClassname($inClassname) {
		$this->getOptionsSet()->setOptions(array(self::OPTION_CLASS_NAME => $inClassname));
		return $this;
	}
	
	/**
	 * Returns the package name that the generated classes belong to
	 *
	 * @return string
	 */
	function getPackageBase() {
		return $this->getOptionsSet()->getOptions(self::OPTION_PACKAGE_BASE);
	}

	/**
	 * Set the package name
	 *
	 * @param string $inPackageBase
	 * @return generatorDao
	 */
	function setPackageBase($inPackageBase) {
		$this->getOptionsSet()->setOptions(array(self::OPTION_PACKAGE_BASE => $inPackageBase));
		return $this;
	}
	
	/**
	 * Returns the name of the package level exception
	 *
	 * @return string
	 */
	function getPackageException() {
		return $this->getOptionsSet()->getOptions(self::OPTION_PACKAGE_EXCEPTION);
	}

	/**
	 * Sets the name of the package level exception
	 *
	 * @param string $inName
	 * @return generatorDao
	 */
	function setPackageException($inName) {
		$this->getOptionsSet()->setOptions(array(self::OPTION_PACKAGE_EXCEPTION => $inName));
		return $this;
	}
}