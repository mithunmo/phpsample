<?php
/**
 * reportData
 * 
 * Stored in reportData.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage report
 * @category reportData
 * @version $Rev: 811 $
 */


/**
 * reportData
 * 
 * The reportData class stores all compiled report results. The results themselves
 * are held in an Sqlite database file. The main report table, named "report" is
 * comprised of a primary key: id and then each column from the report column
 * definition array (see {@link reportColumn} for details).
 * 
 * Internally the reportData automatically attempts to connect to the reports
 * data store, creating it if it does not already exist. The database file
 * contains three tables:
 * 
 * * report
 * * metadata
 * * cachedata
 * 
 * report is the main data while metadata contains additional information about a
 * specific rows and columns data. This is used to store any object data that has
 * been bound to the result set e.g. {@link reportDataAbstract} objects that
 * contain Excel formatting instructions. cachedata contains the creation date of
 * the database and any other properties that may be useful for caching purposes.
 * 
 * Several prepared statements are used in reportData - each is prepared when
 * first accessed but is cached afterwards for performance. Upon destruction each
 * statement is closed and the connection to the database file destroyed.
 * 
 * reportData implements the Iterator interface allowing it to be used in foreach
 * loops. Data is added via the {@link reportData::addRow} method. Rows start at 1
 * and increment from there. When adding a row of data it must contain the named
 * keys as described by the report columns.
 * 
 * Report data can come from any source, database via SQL query, XML, PHP
 * array, SOAP API calls etc. This container just holds the formatted results.
 * 
 * Example:
 * <code>
 * array(
 *   1 => array(field1 => value1, field2 => value2),
 *   2 => array(field1 => value1, field2 => value2),
 *   ...
 * );
 * </code>
 * 
 * Some additional helper methods are included for manipulating the data. These
 * include being able to sum a row of data or a whole column. Only the numeric
 * columns are summed.
 * 
 * <code>
 * $oReport = new report();
 * // sum a row
 * $oData = new reportData($oReport);
 * // add some data
 * $oData->addRow(array('field1' => 1, 'field2' => 2, 'field3' => 3, 'field4' => 'string'));
 * 
 * $total = $oData->sumRow(1);
 * echo $total; // outputs 6
 * 
 * // sum a column
 * $oData = new reportData();
 * $oData
 *     ->addRow(array('field1' => 1, 'field2' => 2, 'field3' => 3))
 *     ->addRow(array('field1' => 1, 'field2' => 2, 'field3' => 3))
 *     ->addRow(array('field1' => 1, 'field2' => 2, 'field3' => 3));
 * 
 * $total = $oData->sumColumn('field1');
 * echo $total; // outputs 3
 * </code>
 * 
 * Once compiled, additional queries can be run on the reportData by using the
 * {@link reportData::query} method. This returns the PDO connection instance
 * allowing any valid Sqlite query to be run.
 * 
 * 
 * <b>Improving Performance</b>
 * 
 * By default the reportData PDO connection to SQLite is run in auto-commit mode.
 * This is because the object can be used for both inserting and iterating data
 * and it is impossible to know what context is being used.
 * 
 * To improve insertion performance when inserting data be sure to call:
 * beginTransaction() before adding rows to the reportData object, and then
 * afterwards call commit() (both from {@link reportData->query()}. This will
 * wrap all inserts into a single transaction, greatly increasing the speed.
 * For example: 23K records in auto-commit might take 53 seconds, as a single
 * transaction 2.3 seconds (of course your mileage may vary).
 * 
 * You need to set the beginTransaction() and commit() inside the {@link reportBase->_run()}
 * method before and after you start adding rows to the data object.
 * 
 * <code>
 * // inside a report extending reportBase, in method _run()
 * function _run() {
 *     // do some setup and prep report data query or collate
 *     
 *     // ready to being storing data, start transaction
 *     $this->getReportData()->query()->beginTransaction();
 *     
 *     foreach ( $oResultSet as $row ) {
 *         $this->getReportData()->addRow($row);
 *     }
 *     
 *     // finished collating data, commit changes
 *     $this->getReportData()->query()->commit();
 *     
 *     // finish up report stuff
 *     return true;
 * }
 * </code>
 * 
 * @package scorpio
 * @subpackage report
 * @category reportData
 */
class reportData implements Iterator, Countable {
	
	/**
	 * Stores $_Modified
	 * 
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;
	
	/**
	 * Stores $_Report
	 *
	 * @var reportBase
	 * @access protected
	 */
	protected $_Report;
	
	/**
	 * Stores $_CurrentRow
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_CurrentRow;
	
	/**
	 * Stores $_CurrentRowData
	 *
	 * @var array
	 * @access protected
	 */
	protected $_CurrentRowData;
	
	/**
	 * Stores $_FileStore
	 *
	 * @var string
	 * @access protected
	 */
	protected $_FileStore;
	
	/**
	 * Stores $_DbFile
	 *
	 * @var string
	 * @access protected
	 */
	protected $_DbFile;
	
	/**
	 * Holds an instance of the SQLite database connection
	 *
	 * @var dbDriverSqlite
	 * @access private
	 */
	private $_DbConnection;
	
	/**
	 * Holds all open PDO statement objects
	 * 
	 * @var array(PDOStatement)
	 */
	private $_Statements;
	
	
	
	/**
	 * Creates a new reportData object
	 * 
	 * @param reportBase $inReport
	 * @return reportData
	 */
	function __construct(reportBase $inReport) {
		$this->reset();
		$this->setReport($inReport);
		$this->initialise();
	}
	
	/**
	 * Clean up all connections and optimise the data cache
	 * 
	 * @return void
	 */
	function __destruct() {
		foreach ( $this->_Statements as $oStatement ) {
			if ( $oStatement instanceof PDOStatement ) {
				$oStatement->closeCursor();
				$oStatement = null;
			}
		}
		
		$this->optimise();
		
		$this->_DbConnection = null;
		$this->_Report = null;
	}
	
	/**
	 * Sets up the reportData object creating structures as needed
	 * 
	 * @return void
	 */
	function initialise() {
		$this->setFileStore(reportManager::buildFileStorePath($this->getReport()));
		$this->setDbFile($this->getReport()->getCacheId().'.db');
		$this->_createDbStructure();
	}
	
	/**
	 * Resets the object
	 * 
	 * @return void
	 */
	function reset() {
		$this->_Report = null;
		$this->_CurrentRow = 0;
		$this->_CurrentRowData = null;
		$this->_FileStore = null;
		$this->_DbFile = null;
		$this->_Statements = array();
		$this->setModified(false);
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
	 * @return reportData
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}

	/**
	 * Returns the report
	 *
	 * @return reportBase
	 */
	function getReport() {
		return $this->_Report;
	}
	
	/**
	 * Set the report instance
	 *
	 * @param reportBase $inReport
	 * @return reportData
	 */
	function setReport(reportBase $inReport) {
		$this->_Report = $inReport;
		return $this;
	}

	/**
	 * Returns $_CurrentRow
	 *
	 * @return integer
	 */
	function getCurrentRow() {
		return $this->_CurrentRow;
	}
	
	/**
	 * Set $_CurrentRow to $inCurrentRow
	 *
	 * @param integer $inCurrentRow
	 * @return reportData
	 */
	function setCurrentRow($inCurrentRow) {
		if ( $inCurrentRow !== $this->_CurrentRow ) {
			$this->_CurrentRow = $inCurrentRow;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_CurrentRowData
	 *
	 * @return array
	 */
	function getCurrentRowData() {
		return $this->_CurrentRowData;
	}
	
	/**
	 * Set $_CurrentRowData to $inCurrentRowData
	 *
	 * @param array $inCurrentRowData
	 * @return class
	 */
	function setCurrentRowData($inCurrentRowData) {
		if ( $inCurrentRowData !== $this->_CurrentRowData ) {
			$this->_CurrentRowData = $inCurrentRowData;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_FileStore
	 *
	 * @return string
	 */
	function getFileStore() {
		return $this->_FileStore;
	}
	
	/**
	 * Set $_FileStore to $inFileStore
	 *
	 * @param string $inFileStore
	 * @return reportData
	 */
	function setFileStore($inFileStore) {
		if ( $inFileStore !== $this->_FileStore ) {
			$this->_FileStore = $inFileStore;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_DbFile
	 *
	 * @return string
	 */
	function getDbFile() {
		return $this->_DbFile;
	}
	
	/**
	 * Set $_DbFile to $inDbFile
	 *
	 * @param string $inDbFile
	 * @return reportData
	 */
	function setDbFile($inDbFile) {
		if ( $inDbFile !== $this->_DbFile ) {
			$this->_DbFile = $inDbFile;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the creation date of this database
	 * 
	 * @return string
	 */
	function getCreateDate() {
		return $this->getDbInstance()->query('SELECT createdate FROM cachedata LIMIT 1')->fetchColumn();
	}

	/**
	 * Returns the PDO connection to the SQLite database allowing arbitrary queries to be run
	 * 
	 * @return PDO
	 */
	function query() {
		return $this->getDbInstance();
	}
	
	
	
	/**
	 * @see Iterator::current()
	 */
	public function current() {
		// report data indexes start at 1
		if ( $this->getCurrentRow() == 0 ) {
			$this->next();
		}
		
		if ( !isset($this->_Statements['fetch']) ) {
			$this->_Statements['fetch'] = $this->getDbInstance()->prepare("SELECT * FROM report WHERE id = :Id LIMIT 1");
		}
		
		$tmp = $this->getCurrentRowData();
		if ( null === $tmp || $tmp['id'] != $this->getCurrentRow() ) {
			$this->_Statements['fetch']->bindValue(':Id', $this->getCurrentRow());
			$this->_Statements['fetch']->execute();
			$tmp = $this->_Statements['fetch']->fetch(PDO::FETCH_ASSOC);
		}
		
		$row = array();
		if ( is_array($tmp) ) {
			foreach ( $tmp as $key => $value ) {
				if ( $this->_hasObject($tmp['id'], $key) ) {
					$row[$key] = $this->_getObject($tmp['id'], $key);
				} else {
					$row[$key] = $value;
				}
			}
		}
		return $row;
	}

	/**
	 * @see Iterator::key()
	 */
	public function key() {
		return $this->getCurrentRow();
	}

	/**
	 * @see Iterator::next()
	 */
	public function next() {
		++$this->_CurrentRow;
	}

	/**
	 * @see Iterator::rewind()
	 */
	public function rewind() {
		$this->setCurrentRow(0);
	}

	/**
	 * @see Iterator::valid()
	 */
	public function valid() {
		if ( !isset($this->_Statements['valid']) ) {
			$this->_Statements['valid'] = $this->getDbInstance()->prepare("SELECT * FROM report WHERE id = :Id LIMIT 1");
		}
		
		$this->setCurrentRowData(null);
		if ( $this->key() <= $this->count() ) {
			$this->_Statements['valid']->bindValue(':Id', $this->key());
			if ( $this->_Statements['valid']->execute() ) {
				if ( $this->_Statements['valid']->rowCount() > 0 ) {
					$this->setCurrentRowData($this->_Statements['valid']->fetch(PDO::FETCH_ASSOC));
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * @see Countable::count()
	 */
	public function count() {
		return $this->getDbInstance()->query("SELECT COUNT(*) AS count FROM report")->fetchColumn(); 
	}
	
	/**
	 * Aliases of count()
	 * 
	 * @see baseSet::getCount()
	 * @return integer
	 */
	function getCount() {
		return $this->count();
	}
	
	

	/**
	 * Sets all rows for result set
	 *
	 * @param array $inArray
	 * @return reportData
	 */
	function setRows(array $inArray = array()) {
		foreach ( $inArray as $row ) {
			$this->addRow($row);
		}
		return $this;
	}
	
	/**
	 * Adds a row to the end of the set
	 *
	 * @param array $inArray
	 * @return reportData
	 */
	function addRow(array $inArray = array()) {
		$inArray = array('id' => ++$this->_CurrentRow) + $inArray;
		if ( !isset($this->_Statements['insert']) ) {
			$cols = array_keys($inArray);
			for ( $i=0; $i<count($cols); $i++ ) {
				$cols[$i] = $this->getDbInstance()->quoteIdentifier($cols[$i]);
			}
			
			$query = '
				INSERT INTO report
					('.implode(', ', $cols).')
				VALUES
					(:'.implode(', :', array_keys($inArray)).')';
			
			$this->_Statements['insert'] = $this->getDbInstance()->prepare($query);
		}
		
		/*
		 * Check for objects and store in metadata, remap keys to :named place holders
		 */
		foreach ( $inArray as $key => $value ) {
			if ( is_object($value) ) {
				$this->_storeObject($inArray[':id'], $key, $value);
			}
			
			$inArray[':'.$key] = $value;
			unset($inArray[$key]);
		}
		
		$this->_Statements['insert']->execute($inArray);
		
		return $this;
	}
	
	/**
	 * Returns row number $inRowNum
	 *
	 * @param integer $inRowNum
	 * @return mixed
	 */
	function getRow($inRowNum) {
		$this->setCurrentRow($inRowNum);
		if ( $this->valid() ) {
			return $this->current();
		} else {
			return false;
		}
	}
	
	/**
	 * Sums all values tagged with $inColName, but only if $inColName is numeric
	 *
	 * @param string $inColName
	 * @return integer|float
	 */
	function sumColumn($inColName) {
		$oRepCol = $this->getReport()->getReportColumn($inColName);
		if ( $oRepCol instanceof reportColumn && $oRepCol->isNumeric() ) {
			return (float) $this
				->getDbInstance()
					->query('SELECT SUM('.$this->getDbInstance()->quoteIdentifier($inColName).') AS total FROM report')
					->fetchColumn();
		} else {
			return 0;
		}
	}
	
	/**
	 * Sums a rows integer or float values, returns 0 if nothing to sum.
	 *
	 * @param integer $inRow
	 * @return integer|float
	 */
	function sumRow($inRow) {
		$cols = array();
		foreach ( $this->getReport()->getReportColumns() as $oColumn ) {
			if ( $oColumn->isNumeric() ) {
				$cols[] = $this->getDbInstance()->quoteIdentifier($oColumn->getFieldName());
			}
		}
		
		if ( count($cols) > 0 ) {
			$oStmt = $this
				->getDbInstance()
					->query('SELECT '.implode('+', $cols).' AS total FROM report WHERE id = :Id');
			$oStmt->bindValue(':Id', $inRow);
			$oStmt->execute();
			return (float) $oStmt->fetchColumn();
		} else {
			return 0;
		}
	}
	
	
	
	/**
	 * Returns an instance of dbDriverSqlite
	 *
	 * @return dbDriverSqlite
	 * @access private
	 */
	private function getDbInstance() {
		if ( !$this->_DbConnection instanceof dbDriverSqlite ) {
			if ( !file_exists($this->getFileStore()) ) {
				$oldmask = umask(0);
				@mkdir($this->getFileStore(), $this->getReport()->getOption(reportBase::OPTION_CACHE_FOLDER_PERMISSIONS), true);
				umask($oldmask);
			}
			
			$db = $this->getFileStore().system::getDirSeparator().$this->getDbFile();
			$this->_DbConnection = new dbDriverSqlite(
				new dbOptions('sqlite://localhost/'.$db.'?version=3')
			);
			
			if ( file_exists($db) ) {
				@chmod($db, $this->getReport()->getOption(reportBase::OPTION_CACHE_FILE_PERMISSIONS));
			}
		}
		return $this->_DbConnection;
	}
	
	/**
	 * Checks and builds as necessary the Sqlite DB structure
	 * 
	 * @return void
	 * @access private
	 */
	private function _createDbStructure() {
		if ( !$this->dbExists() ) {
			$query = "
				CREATE TABLE report (
				  id INTEGER PRIMARY KEY, ";
			
			$cols = array();
			foreach ( $this->getReport()->getReportColumns() as $oColumn ) {
				$cols[] = $this->getDbInstance()->quoteIdentifier($oColumn->getFieldName()).' '.strtoupper($oColumn->getFieldType()).' NOT NULL';
			}
			$query .= implode(' , ', $cols).' )';
			
			$this->getDbInstance()->exec($query);
			
			$query = '
				CREATE TABLE metadata (
				  "row" integer NOT NULL,
				  "column" text NOT NULL,
				  "object" text NOT NULL,
				  PRIMARY KEY ("row", "column") )';
			$this->getDbInstance()->exec($query);
			
			$query = '
				CREATE TABLE cachedata (
				  "createdate" DATETIME NOT NULL
				)';
			$this->getDbInstance()->exec($query);
			$this->getDbInstance()->exec('INSERT INTO cachedata (createdate) VALUES ('.$this->getDbInstance()->quote(date('Y-m-d H:i:s')).')');
		}
	}
	
	/**
	 * Returns true if the report DB is created
	 * 
	 * @return boolean
	 */
	function dbExists() {
		try {
			$this->getDbInstance()->query("SELECT 1 FROM report LIMIT 1");
			return true;
		} catch ( Exception $e ) {
			
		}
		return false;
	}
	
	/**
	 * Runs a vacuum on the SQLite database, optimising it
	 * 
	 * @return void
	 */
	function optimise() {
		$this->getDbInstance()->exec('VACUUM');
	}
	
	/**
	 * Drops the report table and cleans up the db file
	 * 
	 * @return void
	 */
	function purge() {
		$this->getDbInstance()->exec('DROP TABLE IF EXISTS report');
		$this->getDbInstance()->exec('DROP TABLE IF EXISTS metadata');
		$this->getDbInstance()->exec('DROP TABLE IF EXISTS cachedata');
		$this->optimise();
		$this->_createDbStructure();
	}

	/**
	 * Returns true if there is an object record for $inRow:$inColumn
	 * 
	 * @param integer $inRow
	 * @param string $inColumn
	 * @return boolean
	 * @access protected
	 */
	protected function _hasObject($inRow, $inColumn) {
		if ( !isset($this->_Statements['objectLookup']) ) {
			$query = 'SELECT object FROM metadata WHERE "row" = :row AND "column" = :column';
			
			$this->_Statements['objectLookup'] = $this->getDbInstance()->prepare($query);
		}
		
		$this->_Statements['objectLookup']->bindValue(':row', $inRow, PDO::PARAM_INT);
		$this->_Statements['objectLookup']->bindValue(':column', $inColumn, PDO::PARAM_STR);
		if ( $this->_Statements['objectLookup']->execute() ) {
			$object = $this->_Statements['objectLookup']->fetchColumn();
			if ( $object ) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Returns the stored object for $inRow:$inColumn, throws exception is cannot restore object
	 * 
	 * @param integer $inRow
	 * @param string $inColumn
	 * @return mixed
	 * @access protected
	 * @throws reportException
	 */
	protected function _getObject($inRow, $inColumn) {
		if ( !isset($this->_Statements['objectfetch']) ) {
			$query = 'SELECT object FROM metadata WHERE "row" = :row AND "column" = :column LIMIT 1';
			
			$this->_Statements['objectfetch'] = $this->getDbInstance()->prepare($query);
		}
		
		$this->_Statements['objectfetch']->execute(
			array(
				'row' => $inRow,
				'column' => $inColumn,
			)
		);
		
		$object = unserialize($this->_Statements['objectfetch']->fetchColumn());
		if ( is_object($object) ) {
			return $object;
		} else {
			throw new reportException("Failed to reload metadata object for $inRow:$inColumn");
		}
	}
	
	/**
	 * Stores an object in the metadata table
	 * 
	 * @param integer $inRow
	 * @param string $inColumn
	 * @param mixed $inObject
	 * @return void
	 */
	protected function _storeObject($inRow, $inColumn, $inObject) {
		if ( !isset($this->_Statements['metadata']) ) {
			$query = '
				INSERT INTO metadata
					("row", "column", "object")
				VALUES
					(:row, :column, :object)';
			
			$this->_Statements['metadata'] = $this->getDbInstance()->prepare($query);
		}
		
		$this->_Statements['metadata']->execute(
			array(
				'row' => $inRow,
				'column' => $inColumn,
				'object' => serialize($inObject),
			)
		);
		
		return $this;
	}
}