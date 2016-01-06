<?php
/**
 * reportBase
 * 
 * Stored in reportBase.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage report
 * @category reportBase
 * @version $Rev: 771 $
 */


/**
 * reportBase
 * 
 * This is the basic report class. It contains the shared logic for all reports
 * allowing for similar logic to be applied to all reports. It should be extended
 * into a specific report implementing the abstract methods and overriding any
 * others as needed.
 * 
 * Report data should always be compiled into the {@link reportData} object,
 * a generic intermediary with a defined interface. Report columns should be
 * initialised in the abstract {@link reportBase::initialise() initialise}
 * method. _run() is used to actually compile the report data. This method
 * should return true on success or throw an exception if an error is encountered.
 * 
 * The final major method is getCacheId(). This is used to create a hash that will
 * uniquely identify this run of the report, with the idea being that if the same
 * report is run again by another user, the cached data will be used. By default
 * this simply creates an MD5 of the current options, however these options
 * include the output type which may change. Therefore care should be taken to
 * set this when necessary.
 * 
 * Custom options should be added to the extended report. Accessors can be added if
 * necessary.
 * 
 * Example: extend into myReport, and run it
 * <code>
 * class myReport extends reportBase {
 * 
 *     function initialise() {
 *         $this->addReportColumn(new reportColumn('db_field_1', 'My Field'));
 *         $this->addReportColumn(new reportColumn('db_field_2', 'Another Field'));
 *         $this->addReportColumn(new reportColumn('db_field_3', 'Last Field'));
 *     }
 *     
 *     function _run() {
 *         $this->getReportData()->addRow(
 *             array(
 *                 'db_field_1' => 'some value',
 *                 'db_field_2' => 'second value',
 *                 'db_field_3' => 'third value',
 *             )
 *         );
 *         return true;
 *     }
 *     
 *     function isValid() {
 *         // doesn't have to be completed, but useful to validate report settings
 *         return true;
 *     }
 * }
 * 
 * // run report
 * $oReport = new myReport(
 *     array(
 *         reportBase::OPTION_USE_CACHE => true,
 *         reportBase::OPTION_OUTPUT_TYPE => reportManager::OUTPUT_HTML,
 *     )
 * );
 * $oReport->run();
 * $oWriter = $oReport->getReportWriter();
 * $oWriter->compile();
 * 
 * header("Content-Type: ".$oWriter->getMimeType());
 * readfile($oWriter->getFullPathToOutputFile());
 * </code>
 *
 *
 * <b>Caching</b>
 * 
 * With the move from an in-memory result set to an SQLite data store for the report
 * data, the cacheController instance is no longer required. The cache settings are
 * still used to force the report data to be re-built on every request or to just
 * use the existing pre-compiled data.
 * 
 * The file and folder permissions are used to set access permissions to the SQLite
 * file and any compiled report output types (e.g. PDF, XLSX etc).
 * 
 * cacheController and associated methods will be removed in a future revision.
 * 
 * @package scorpio
 * @subpackage report
 * @category reportBase
 */
abstract class reportBase {
	
	/**
	 * Stores $_Modified
	 * 
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;
	
	/**
	 * Stores the reportData object
	 *
	 * @var reportData
	 * @access protected
	 */
	protected $_ReportData;
	
	/**
	 * Stores options set
	 *
	 * @var baseOptionsSet
	 * @access protected
	 */
	protected $_Options;
	
	const OPTION_USE_CACHE = 'report.cache';
	const OPTION_CACHE_GC_CHANCE = 'report.cacheGcChance';
	const OPTION_CACHE_LIFETIME = 'report.cacheLifetime';
	const OPTION_CACHE_FILE_PERMISSIONS = 'report.cacheFilePermissions';
	const OPTION_CACHE_FOLDER_PERMISSIONS = 'report.cacheFolderPermissions';
	
	const OPTION_START_DATE = 'report.startDate';
	const OPTION_END_DATE = 'report.endDate';
	
	const OPTION_GROUP_BY = 'report.groupBy';
	const OPTION_ORDER_BY = 'report.orderBy';
	
	const OPTION_OUTPUT_TYPE = 'report.outputType';
	
	/**
	 * Stores the reportStyle, the font and colours etc
	 *
	 * @var reportStyle
	 * @access protected
	 */
	protected $_ReportStyle;
	
	/**
	 * An array of reportColumn objects defining the data in the report
	 *
	 * @var array
	 * @access protected
	 */
	protected $_ReportColumns;
	
	/**
	 * Stores the instance of the cacheController for caching
	 *
	 * @var cacheController
	 * @access protected
	 * @deprecated
	 */
	protected $_CacheController;
	
	/**
	 * Is set to true if the reportData is pulled from the cache
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_DataFromCache;
	
	/**
	 * Stores the reportWriter instance
	 *
	 * @var reportWriterBase
	 * @access protected
	 */
	protected $_ReportWriter;
	
	/**
	 * Stores the computed cache id
	 *
	 * @var string
	 * @access protected
	 */
	protected $_CacheId;
	
	
	
	/**
	 * Creates a new report object
	 *
	 * @param array $inOptions
	 * @param reportStyle $inReportStyle
	 * @return reportBase
	 */
	function __construct(array $inOptions = array(), $inReportStyle = null) {
		$this->reset();
		$this->setOptions($inOptions);
		if ( $inReportStyle instanceof reportStyle ) {
			$this->setReportStyle($inReportStyle);
		}
		$this->initialise();
		
		/**
		 * Set cacheController, not needed, is being kept for compatibility.
		 * 
		 * @var cacheWriterFile $oWriter
		 * @deprecated will be removed in a later revision
		 */
		$oWriter = new cacheWriterFile(reportManager::buildFileStorePath($this));
		$oWriter->setUseSubFolders(false);
		$oWriter->setCacheFilePermissionsMask($this->getOption(self::OPTION_CACHE_FILE_PERMISSIONS));
		$oWriter->setCacheFolderPermissionsMask($this->getOption(self::OPTION_CACHE_FOLDER_PERMISSIONS));
		
		$this->_CacheController = new cacheController($oWriter);
		$this->_CacheController->setGcInterval($this->getOptionsSet()->getOptions(self::OPTION_CACHE_GC_CHANCE));
	}

	/**
	 * Resets the object
	 *
	 * @return void
	 */
	function reset() {
		$this->setOptions(
			array(
				self::OPTION_USE_CACHE => true,
				self::OPTION_CACHE_LIFETIME => 3600,
				self::OPTION_CACHE_GC_CHANCE => 1000,
				self::OPTION_CACHE_FILE_PERMISSIONS => 0644,
				self::OPTION_CACHE_FOLDER_PERMISSIONS => 0755,
				self::OPTION_OUTPUT_TYPE => reportManager::OUTPUT_XLSX,
				
				self::OPTION_START_DATE => date('Y-m-d 00:00:00', strtotime('-8 days')),
				self::OPTION_END_DATE => date('Y-m-d 23:59:59', strtotime('yesterday')),
			)
		);
		$this->_ReportColumns = array();
		$this->_ReportStyle = null;
		$this->_ReportData = null;
		$this->_ReportWriter = null;
		$this->_DataFromCache = false;
		
		$this->setModified(false);
	}
	
	
	
	/**
	 * Performs the custom setup for the report, e.g. setting columns and title
	 *
	 * @return void
	 * @abstract 
	 */
	abstract function initialise();
	
	/**
	 * Runs the report using the currently set options
	 * 
	 * If caching is enabled, the reportData is pulled from the cache,
	 * unless it has expired or is damaged. If successfully returned, the
	 * internal property {$link reportBase::$_DataFromCache} is set to true.
	 *
	 * @return boolean
	 */
	function run() {
		if ( $this->useCache() ) {
			$createDate = $this->getReportData()->getCreateDate();
			if ( (time()-strtotime($createDate)) >= $this->getCacheLifetime() ) {
				$this->getReportData()->purge();
			} else {
				if ( $this->getReportData()->getCount() > 0 ) {
					$this->setDataFromCache(true);
					return true;
				}
			}
		} else {
			$this->getReportData()->purge();
		}
		
		return $this->_run();
	}
	
	/**
	 * Abstract method that compiles the reportData object.
	 * 
	 * This method must return boolean true on success or throw exceptions
	 * in the case of any errors.
	 *
	 * @return boolean
	 * @throws reportException
	 * @abstract
	 */
	abstract function _run();
	
	/**
	 * Returns true if report options are valid
	 *
	 * @return boolean
	 * @abstract 
	 */
	function isValid() {
		return true;
	}
	
	
	
	/**
	 * Returns true if object has been modified
	 * 
	 * @return boolean
	 */
	function isModified() {
		return $this->_Modified;
	}
	
	/**
	 * Set the status of the object if it has been changed
	 * 
	 * @param boolean $status
	 * @return reportBase
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}
	
	/**
	 * Returns the report name/title, defaulting to the current class name
	 *
	 * @return string
	 * @abstract
	 */
	function getReportName() {
		return get_class($this);
	}
	
	/**
	 * Returns the report description, should be overridden in child objects
	 *
	 * @return string
	 * @abstract 
	 */
	function getReportDescription() {
		return 'A report of '.$this->getReportName().' between '.$this->getStartDate().' and '.$this->getEndDate();
	}
	
	/**
	 * Returns the reportData object, creating an empty one if not set
	 *
	 * @return reportData
	 */
	function getReportData() {
		if ( !$this->_ReportData instanceof reportData ) {
			$this->_ReportData = new reportData($this);
		}
		return $this->_ReportData;
	}
	
	/**
	 * Set the reportData object
	 *
	 * @param reportData $inReportData
	 * @return reportBase
	 */
	function setReportData($inReportData) {
		if ( $inReportData !== $this->_ReportData ) {
			$this->_ReportData = $inReportData;
			$this->setModified();
		}
		return $this;
	}
	
	
	
	/**
	 * Returns the current baseOptionsSet instance
	 *
	 * @return baseOptionsSet
	 */
	function getOptionsSet() {
		if ( !$this->_Options instanceof baseOptionsSet ) {
			$this->_Options = new baseOptionsSet();
		}
		return $this->_Options;
	}
	
	/**
	 * Returns the option $inOption, or $inDefault if not found
	 * 
	 * @param string $inOption
	 * @param mixed $inDefault
	 * @return mixed
	 */
	function getOption($inOption, $inDefault = null) {
		return $this->getOptionsSet()->getOptions($inOption, $inDefault);
	}
	
	/**
	 * Set the options for the report.
	 * 
	 * $inOptions must be an array that contains at least one option in a key => value
	 * array pair. Multiple options can be set at the same time.
	 *
	 * @param array $inOptions
	 * @return reportBase
	 */
	function setOptions(array $inOptions = array()) {
		$this->getOptionsSet()->setOptions($inOptions);
		return $this;
	}
	
	/**
	 * Returns true if results should be cached
	 *
	 * @return boolean
	 */
	function useCache() {
		return $this->getOptionsSet()->getOptions(self::OPTION_USE_CACHE, true);
	}

	/**
	 * Returns the cache lifetime in seconds
	 *
	 * @return integer
	 */
	function getCacheLifetime() {
		return $this->getOptionsSet()->getOptions(self::OPTION_CACHE_LIFETIME, 3600);
	}

	/**
	 * Returns the cache id for the current report, creating one if not set
	 *
	 * @return string
	 * @abstract 
	 */
	function getCacheId() {
		if ( !$this->_CacheId ) {
			$ignore = array(
				self::OPTION_CACHE_FILE_PERMISSIONS, self::OPTION_CACHE_FOLDER_PERMISSIONS,
				self::OPTION_CACHE_GC_CHANCE, self::OPTION_CACHE_LIFETIME, self::OPTION_OUTPUT_TYPE,
				self::OPTION_USE_CACHE
			);
			
			$options = $this->getOptionsSet()->getOptions();
			if ( count($options) > 0 ) {
				foreach ( $ignore as $option ) {
					if ( array_key_exists($option, $options) ) {
						unset($options[$option]);
					}
				}
			}
			$this->_CacheId = md5(implode(':', $options));
		}
		return $this->_CacheId;
	}
	
	/**
	 * Returns the report output type
	 * 
	 * @return string
	 */
	function getOutputType() {
		return $this->getOptionsSet()->getOptions(self::OPTION_OUTPUT_TYPE, reportManager::OUTPUT_XLS);
	}
	
	/**
	 * Returns the start date
	 *
	 * @return datetime
	 */
	function getStartDate() {
		return $this->getOptionsSet()->getOptions(self::OPTION_START_DATE, date('Y-m-d 00:00:00', strtotime('-8 days')));
	}
	
	/**
	 * Returns the end date
	 *
	 * @return datetime
	 */
	function getEndDate() {
		return $this->getOptionsSet()->getOptions(self::OPTION_END_DATE, date('Y-m-d 23:59:59', strtotime('yesterday')));
	}
	
	/**
	 * Returns the group by option
	 *
	 * @return string
	 */
	function getGroupBy() {
		return $this->getOptionsSet()->getOptions(self::OPTION_GROUP_BY, false);
	}

	/**
	 * Returns the order by option
	 *
	 * @return string
	 */
	function getOrderBy() {
		return $this->getOptionsSet()->getOptions(self::OPTION_ORDER_BY, false);
	}
	
	/**
	 * Returns an array of valid group by options, override in the subclass
	 *
	 * @return array
	 * @abstract 
	 */
	function getValidGroupByOptions() {
		return array();
	}

	/**
	 * Returns an array of valid order by options, override in the subclass
	 *
	 * @return array
	 * @abstract 
	 */
	function getValidOrderByOptions() {
		return array();
	}
	
	

	/**
	 * Returns the reportStyle object
	 *
	 * @return reportStyle
	 * @access public
	 */
	function getReportStyle() {
		if ( !$this->_ReportStyle instanceof reportStyle ) {
			$this->_ReportStyle = new reportStyle();
		}
		return $this->_ReportStyle;
	}
	
	/**
	 * Set the reportStyle object to use
	 *
	 * @param reportStyle $inReportStyle
	 * @return reportBase
	 * @access public
	 */
	function setReportStyle(reportStyle $inReportStyle) {
		if ( $this->_ReportStyle !== $inReportStyle ) {
			$this->_ReportStyle = $inReportStyle;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns all defined report columns as an array
	 *
	 * @return array
	 */
	function getReportColumns() {
		return $this->_ReportColumns;
	}
	
	/**
	 * Returns the reportColumn matching $inFieldName, null if not found
	 * 
	 * @param string $inFieldName
	 * @return reportColumn
	 */
	function getReportColumn($inFieldName) {
		if ( array_key_exists($inFieldName, $this->_ReportColumns) ) {
			return $this->_ReportColumns[$inFieldName];
		}
		return null;
	}
	
	/**
	 * Returns a uni-dimensional array of fields that are in use for data
	 *
	 * @return array
	 */
	function getDataColumns() {
		$data = array();
		if ( false ) $oColumn = new reportColumn();
		foreach ( $this->getReportColumns() as $oColumn ) {
			$data[] = $oColumn->getFieldName();
		}
		return $data;
	}
	
	/**
	 * Returns a uni-dimensional array of fields for display
	 *
	 * @return array
	 */
	function getDisplayColumns() {
		$data = array();
		if ( false ) $oColumn = new reportColumn();
		foreach ( $this->getReportColumns() as $oColumn ) {
			$data[] = $oColumn->getDisplayName();
		}
		return $data;
	}
	
	/**
	 * Adds a new column to the array of columns
	 * 
	 * Note: each column must have a unique field name.
	 *
	 * @param reportColumn $inColumn
	 * @return reportBase
	 */
	function addReportColumn(reportColumn $inColumn) {
		if ( !isset($this->_ReportColumns[$inColumn->getFieldName()]) ) {
			$this->_ReportColumns[$inColumn->getFieldName()] = $inColumn;
		}
		return $this;
	}
	
	/**
	 * Set an array of report column objects
	 *
	 * @param reportColumnGroup $inReportColumns Array of reportColumn objects
	 * @return reportBase
	 */
	function setReportColumns(array $inReportColumns = array()) {
		if ( $inReportColumns !== $this->_ReportColumns ) {
			$this->_ReportColumns = $inReportColumns;
			$this->setModified();
		}
		return $this;
	}
	
	
	
	/**
	 * Returns the current cacheController
	 *
	 * @return cacheController
	 * @deprecated reportData now handles caching
	 */
	function getCacheController() {
		if ( $this->_CacheController instanceof cacheController ) {
			if ( !$this->_CacheController->getCacheId() ) {
				$this->_CacheController->setCacheId($this->getCacheId());
			}
		}
		return $this->_CacheController;
	}
	
	/**
	 * Set the cacheController instance
	 *
	 * @param cacheController $inCacheController
	 * @return reportBase
	 * @deprecated reportData now handles caching
	 */
	function setCacheController(cacheController $inCacheController) {
		if ( $inCacheController !== $this->_CacheController ) {
			$this->_CacheController = $inCacheController;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns true if the reportData was loaded from cached data
	 *
	 * @return boolean
	 */
	function getDataFromCache() {
		return $this->_DataFromCache;
	}
	
	/**
	 * Set if reportData was loaded from cache or not
	 *
	 * @param boolean $inDataFromCache
	 * @return reportBase
	 */
	function setDataFromCache($inDataFromCache) {
		if ( $inDataFromCache !== $this->_DataFromCache ) {
			$this->_DataFromCache = $inDataFromCache;
			$this->setModified();
		}
		return $this;
	}
	
	
	
	/**
	 * Returns an array of supported output writers for the standard report
	 * 
	 * @return array
	 */
	function getSupportedReportWriters() {
		return array(
			reportManager::OUTPUT_CSV,
			reportManager::OUTPUT_HTML,
			reportManager::OUTPUT_ODS,
			reportManager::OUTPUT_PDF,
			reportManager::OUTPUT_XLS,
			reportManager::OUTPUT_XLSX,
			reportManager::OUTPUT_XML,
		);
	}
	
	/**
	 * Returns the report writer for the specified output format
	 *
	 * @return reportWriterBase
	 */
	function getReportWriter() {
		if ( !$this->_ReportWriter instanceof reportWriterBase ) {
			$this->_ReportWriter = reportManager::factoryWriter($this);
		}
		return $this->_ReportWriter;
	}
}